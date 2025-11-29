<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentProfile extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        // Personal Information
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'date_of_birth',
        'national_id',
        'blood_group',
        'profile_photo',
        // Contact Information
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        // Occupation Information
        'occupation',
        'employer',
        'work_phone',
        'work_address',
        'annual_income',
        // Relationship
        'relation_to_students',
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        // Additional
        'medical_conditions',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'annual_income' => 'decimal:2',
        'relation_to_students' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the parent profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the students associated with this parent.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship', 'is_primary', 'can_pickup', 'financial_responsibility')
            ->withTimestamps();
    }

    /**
     * Get the parent's full name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get the parent's age.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Check if parent is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'active' => 'bg-success bg-opacity-10 text-success',
            'inactive' => 'bg-warning bg-opacity-10 text-warning',
            'deceased' => 'bg-dark bg-opacity-10 text-dark',
            default => 'bg-secondary bg-opacity-10 text-secondary',
        };
    }

    /**
     * Get formatted phone number.
     */
    public function getFormattedPhoneAttribute(): string
    {
        return $this->phone ?? 'N/A';
    }

    /**
     * Get primary contact (phone or alternate phone).
     */
    public function getPrimaryContactAttribute(): string
    {
        return $this->phone ?? $this->alternate_phone ?? 'N/A';
    }
}
