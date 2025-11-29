<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\UserType;
use App\Models\School;
use App\Models\Academic\Enrollment;
use App\Notifications\TenantResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles {
        HasRoles::hasRole as traitHasRole;
        HasRoles::hasAnyRole as traitHasAnyRole;
        HasRoles::hasAllRoles as traitHasAllRoles;
    }

    protected $connection = 'tenant';

    /**
     * Get the database connection for the model.
     * Returns tenant connection if configured, otherwise returns central connection.
     */
    public function getConnectionName()
    {
        $defaultConnection = config('database.default', 'mysql');

        if ($defaultConnection === 'tenant' || app()->bound('currentSchool')) {
            return 'tenant';
        }

        return $defaultConnection;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'school_id',
        'approval_status',
        'profile_photo',
        'is_active',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'qualification',
        'specialization',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class,
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
            'expelled_at' => 'datetime',
            'registration_data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active
            && $this->approval_status === 'approved'
            && is_null($this->suspended_at)
            && is_null($this->expelled_at);
    }

    public function hasUserType(UserType|string $type): bool
    {
        $userType = $this->user_type instanceof UserType
            ? $this->user_type
            : UserType::tryFrom($this->user_type);

        $expected = $type instanceof UserType ? $type : UserType::tryFrom($type);

        if (! $userType || ! $expected) {
            return false;
        }

        return $userType === $expected;
    }

    /**
     * Tenant-level Admins should always satisfy role checks.
     */
    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->hasTenantAdminAuthority()) {
            return true;
        }

        return $this->traitHasRole($roles, $guard);
    }

    public function hasAnyRole(...$roles): bool
    {
        if ($this->hasTenantAdminAuthority()) {
            return true;
        }

        return $this->traitHasAnyRole(...$roles);
    }

    public function hasAllRoles($roles, ?string $guard = null): bool
    {
        if ($this->hasTenantAdminAuthority()) {
            return true;
        }

        return $this->traitHasAllRoles($roles, $guard);
    }

    protected function hasTenantAdminAuthority(): bool
    {
        if ($this->getIsLandlordAttribute()) {
            return false;
        }

        $type = $this->user_type instanceof UserType
            ? $this->user_type->value
            : (string) $this->user_type;

        return strcasecmp($type, UserType::ADMIN->value) === 0;
    }

    /**
     * Returns true if the user is a landlord (super-admin, not attached to a school).
     */
    public function getIsLandlordAttribute(): bool
    {
        return ($this->user_type instanceof UserType
                ? $this->user_type === UserType::ADMIN
                : $this->user_type === UserType::ADMIN->value)
            && empty($this->school_id);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the URL for the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        return null;
    }

    /**
     * Get the biometric templates for this user.
     */
    public function biometricTemplates()
    {
        return $this->morphMany(BiometricTemplate::class, 'user');
    }

    /**
     * Get the user who approved this user's registration.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(\App\Models\Grade::class, 'student_id');
    }

    /**
     * Subjects assigned to this user (as a teacher).
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Academic\Subject::class, 'class_subject', 'teacher_id', 'subject_id')
            ->withPivot('class_id', 'is_compulsory')
            ->withTimestamps();
    }

    /**
     * Active class enrollments for student-type users.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Active class enrollments for student-type users.
     */
    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    /**
     * Get the current enrollment (most recent active enrollment).
     */
    public function currentEnrollment()
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->latest('enrollment_date');
    }

    /**
     * Get the user's preferences.
     */
    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the CSS badge class based on approval status.
     */
    public function getApprovalLabel(): string
    {
        return match($this->approval_status) {
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            'pending' => __('Pending Approval'),
            default => __('Unknown'),
        };
    }

    public function getApprovalBadgeClass(): string
    {
        return match($this->approval_status) {
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'pending' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new TenantResetPasswordNotification($token));
    }

    /**
     * Get the parent profile associated with the user.
     */
    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }



    /**
     * Get attendance records for this user (as a student).
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    /**
     * Subjects assigned to this user (as a student).
     */
    public function studentSubjects(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Academic\Subject::class, 'student_subject', 'student_id', 'subject_id')
            ->withPivot('academic_year', 'is_core', 'status')
            ->withTimestamps();
    }

    /**
     * Get the invoices for the user (as a student).
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }
}
