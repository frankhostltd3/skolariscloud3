<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradingScheme extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'country',
        'examination_body_id',
        'description',
        'is_current',
        'is_active',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Filter by school
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope: Only active schemes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Only current scheme
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope: By examination body
     */
    public function scopeByExaminationBody($query, $examinationBodyId)
    {
        return $query->where('examination_body_id', $examinationBodyId);
    }

    /**
     * Relationship: Grading bands
     */
    public function bands(): HasMany
    {
        return $this->hasMany(GradingBand::class)->orderBy('sort_order');
    }

    /**
     * Relationship: Examination body
     */
    public function examinationBody(): BelongsTo
    {
        return $this->belongsTo(ExaminationBody::class);
    }

    /**
     * Relationship: School
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    /**
     * Get the grade for a given score
     */
    public function getGradeForScore(float $score): ?GradingBand
    {
        return $this->bands()
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }

    /**
     * Check if bands have overlapping score ranges
     */
    public function hasOverlappingBands(): bool
    {
        $bands = $this->bands()->get();

        foreach ($bands as $i => $band1) {
            foreach ($bands as $j => $band2) {
                if ($i >= $j) continue;

                // Check for overlap
                if (
                    ($band1->min_score <= $band2->max_score && $band1->max_score >= $band2->min_score) ||
                    ($band2->min_score <= $band1->max_score && $band2->max_score >= $band1->min_score)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get score coverage percentage
     */
    public function getScoreCoverageAttribute(): float
    {
        $bands = $this->bands;

        if ($bands->isEmpty()) {
            return 0;
        }

        $minScore = $bands->min('min_score');
        $maxScore = $bands->max('max_score');

        return $maxScore - $minScore;
    }

    /**
     * Boot method to handle is_current logic
     */
    protected static function boot()
    {
        parent::boot();

        // When marking as current, unmark all others
        static::saving(function ($scheme) {
            if ($scheme->is_current && $scheme->isDirty('is_current')) {
                static::where('school_id', $scheme->school_id)
                    ->where('id', '!=', $scheme->id)
                    ->update(['is_current' => false]);
            }
        });
    }
}
