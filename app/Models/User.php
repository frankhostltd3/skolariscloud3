<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\UserType;
use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $connection = 'tenant';

    /**
     * Get the database connection for the model.
     * Returns tenant connection if configured, otherwise returns central connection.
     */
    public function getConnectionName()
    {
        $tenantDb = config('database.connections.tenant.database');

        // If no tenant database is configured, use central connection
        if (empty($tenantDb)) {
            return config('database.default', 'mysql');
        }

        return $this->connection;
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
        ];
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

    /**
     * Get the CSS badge class based on approval status.
     */
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
     * Get the human-readable approval status label.
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
}
