<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

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
        $connection = $this->getConnectionName() ?? config('database.default');

        if (Schema::connection($connection)->hasTable('enrollments')) {
            return $this->belongsToMany(\App\Models\User::class, 'enrollments', 'class_id', 'student_id')
                ->withPivot('status', 'academic_year_id', 'semester_id', 'enrollment_date')
                ->withTimestamps();
        }

        if (Schema::connection($connection)->hasTable('students')) {
            return $this->hasMany(\App\Models\Student::class, 'class_id');
        }

        return $this->hasMany(\App\Models\User::class, 'id', $this->getKeyName())->whereRaw('1 = 0');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Models\Academic\Enrollment::class, 'class_id');
    }

    public function activeEnrollments()
    {
        return $this->hasMany(\App\Models\Academic\Enrollment::class, 'class_id')->where('status', 'active');
    }

    public function teachers()
    {
        return $this->hasMany(\App\Models\Teacher::class, 'class_id');
    }

    public function teacher()
    {
        // Primary class teacher, if your schema stores their user/teacher ID
        return $this->belongsTo(\App\Models\Teacher::class, 'class_teacher_id');
    }

    public function subjects(): BelongsToMany
    {
        $connection = $this->getConnectionName() ?? config('database.default');
        $pivotTable = null;

        foreach (['class_subjects', 'class_subject'] as $candidate) {
            if (tenant_table_exists($candidate, $connection)) {
                $pivotTable = $candidate;
                break;
            }
        }

        $pivotTable ??= 'class_subjects';

        return $this->belongsToMany(\App\Models\Academic\Subject::class, $pivotTable, 'class_id', 'subject_id')
            ->withPivot('teacher_id', 'is_compulsory')
            ->withTimestamps();
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
    public function getComputedStudentsCountAttribute(): int
    {
        if (array_key_exists('active_enrollments_count', $this->attributes)) {
            return (int) $this->attributes['active_enrollments_count'];
        }

        if (!is_null($this->active_students_count)) {
            return (int) $this->active_students_count;
        }

        return 0;
    }

    public function getCapacityPercentageAttribute(): float
    {
        if (!$this->capacity || $this->capacity == 0) {
            return 0;
        }

        return round(($this->computed_students_count / $this->capacity) * 100, 1);
    }

    /**
     * Get the available capacity.
     */
    public function getAvailableCapacityAttribute(): int
    {
        if (!$this->capacity) {
            return 999; // Return large number if no limit
        }

        return max(0, $this->capacity - $this->computed_students_count);
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

    /**
     * Recalculate and persist the number of active students in this class.
     */
    public function updateEnrollmentCount(): void
    {
        $connection = $this->getConnectionName() ?? config('database.default');

        // Prefer enrollments table if available; otherwise fall back to students.
        if (Schema::connection($connection)->hasTable('enrollments')) {
            $count = Enrollment::on($connection)
                ->where('class_id', $this->id)
                ->where('status', 'active')
                ->count();
        } elseif (Schema::connection($connection)->hasTable('students')) {
            $count = Student::on($connection)
                ->where('class_id', $this->id)
                ->count();
        } else {
            $count = 0;
        }

        $this->forceFill(['active_students_count' => $count])->save();
    }

    /**
     * Get the academic year for the class.
     * Since classes are perennial, this returns the current active academic year.
     */
    public function getAcademicYearAttribute()
    {
        return \App\Models\Academic\AcademicYear::where('is_current', true)->first();
    }
}
