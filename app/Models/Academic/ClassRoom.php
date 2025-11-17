<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'education_level_id',
        'capacity',
        'active_students_count',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active_students_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(\App\Models\Academic\EducationLevel::class);
    }

    public function streams()
    {
        return $this->hasMany(\App\Models\Academic\ClassStream::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class, 'class_id');
    }

    public function teachers()
    {
        return $this->hasMany(\App\Models\Teacher::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(\App\Models\Academic\Subject::class, 'class_subjects', 'class_id', 'subject_id');
    }

    /**
     * Scope a query to only include classes for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive classes.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by education level.
     */
    public function scopeByEducationLevel($query, $educationLevelId)
    {
        return $query->where('education_level_id', $educationLevelId);
    }

    /**
     * Scope a query to include classes with available capacity.
     */
    public function scopeWithCapacity($query, $requiredSlots = 1)
    {
        return $query->whereRaw('(capacity - active_students_count) >= ?', [$requiredSlots]);
    }

    /**
     * Check if the class has available capacity.
     */
    public function hasCapacity(int $requiredSlots = 1): bool
    {
        if (!$this->capacity) {
            return true; // No capacity limit set
        }

        $available = $this->capacity - ($this->active_students_count ?? 0);
        return $available >= $requiredSlots;
    }

    /**
     * Get the percentage of capacity used.
     */
    public function getCapacityPercentageAttribute(): float
    {
        if (!$this->capacity || $this->capacity == 0) {
            return 0;
        }

        return round((($this->active_students_count ?? 0) / $this->capacity) * 100, 1);
    }

    /**
     * Get the available capacity.
     */
    public function getAvailableCapacityAttribute(): int
    {
        if (!$this->capacity) {
            return 999; // Return large number if no limit
        }

        return max(0, $this->capacity - ($this->active_students_count ?? 0));
    }

    /**
     * Get the capacity status (available, filling_up, almost_full, full).
     */
    public function getCapacityStatusAttribute(): string
    {
        $percentage = $this->capacity_percentage;

        if ($percentage >= 100) {
            return 'full';
        } elseif ($percentage >= 90) {
            return 'almost_full';
        } elseif ($percentage >= 70) {
            return 'filling_up';
        }

        return 'available';
    }

    /**
     * Get the full class name with education level.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->educationLevel) {
            return $this->educationLevel->name . ' - ' . $this->name;
        }

        return $this->name;
    }
}
