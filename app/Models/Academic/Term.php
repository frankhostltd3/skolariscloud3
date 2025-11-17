<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Term extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'code',
        'academic_year',
        'start_date',
        'end_date',
        'description',
        'is_current',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    /**
     * Scopes
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Attributes
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->academic_year})";
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getDurationInWeeksAttribute(): int
    {
        return (int) ceil($this->duration_in_days / 7);
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        $now = now();
        if ($this->start_date->isFuture()) {
            return 'upcoming';
        } elseif ($this->end_date->isPast()) {
            return 'completed';
        } else {
            return 'ongoing';
        }
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'upcoming' => 'bg-info',
            'ongoing' => 'bg-success',
            'completed' => 'bg-secondary',
            'inactive' => 'bg-warning',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'inactive' => 'Inactive',
            default => 'Unknown',
        };
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->status === 'upcoming') {
            return 0;
        } elseif ($this->status === 'completed') {
            return 100;
        } else {
            $totalDays = $this->duration_in_days;
            $daysPassed = $this->start_date->diffInDays(now());
            return $totalDays > 0 ? round(($daysPassed / $totalDays) * 100, 1) : 0;
        }
    }

    /**
     * Methods
     */
    public function setAsCurrent(): bool
    {
        // Unset all other current terms for this school
        static::where('school_id', $this->school_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Set this term as current
        $this->is_current = true;
        return $this->save();
    }

    public static function getCurrentTerm($schoolId)
    {
        return static::forSchool($schoolId)
            ->current()
            ->first();
    }
}
