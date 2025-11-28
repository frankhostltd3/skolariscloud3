<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'education_level_id',
        'description',
        'type',
        'credit_hours',
        'required_periods_per_week',
        'pass_mark',
        'max_marks',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_hours' => 'integer',
        'required_periods_per_week' => 'integer',
        'pass_mark' => 'integer',
        'max_marks' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Scope: Filter by school
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Get required weekly periods (default fallback if null)
     */
    public function getRequiredWeeklyPeriodsAttribute(): int
    {
        return $this->required_periods_per_week ?? 0; // 0 means no hard requirement
    }

    /**
     * Scope: Only active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By type (core, elective, optional)
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: By education level
     */
    public function scopeByEducationLevel($query, $educationLevelId)
    {
        return $query->where('education_level_id', $educationLevelId);
    }

    /**
     * Scope: Core subjects only
     */
    public function scopeCore($query)
    {
        return $query->where('type', 'core');
    }

    /**
     * Scope: Elective subjects only
     */
    public function scopeElective($query)
    {
        return $query->where('type', 'elective');
    }

    /**
     * Relationship: School
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    /**
     * Relationship: Education level
     */
    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Relationship: Classes (many-to-many)
     */
    public function classes(): BelongsToMany
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

        return $this->belongsToMany(ClassRoom::class, $pivotTable, 'subject_id', 'class_id')
            ->withPivot('teacher_id', 'is_compulsory')
            ->withTimestamps();
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'core' => 'primary',
            'elective' => 'success',
            'optional' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'core' => 'Core',
            'elective' => 'Elective',
            'optional' => 'Optional',
            default => 'Unknown',
        };
    }

    /**
     * Get full name with code
     */
    public function getFullNameAttribute(): string
    {
        if ($this->code) {
            return "{$this->name} ({$this->code})";
        }

        return $this->name;
    }

    /**
     * Check if subject has passing score
     */
    public function isPassing(int $score): bool
    {
        return $score >= $this->pass_mark;
    }

    /**
     * Get percentage score
     */
    public function getPercentage(int $score): float
    {
        if ($this->max_marks == 0) {
            return 0;
        }

        return ($score / $this->max_marks) * 100;
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'bg-success' : 'bg-warning';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
