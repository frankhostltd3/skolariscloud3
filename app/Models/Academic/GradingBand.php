<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingBand extends Model
{
    protected $fillable = [
        'grading_scheme_id',
        'grade',
        'label',
        'min_score',
        'max_score',
        'grade_point',
        'remarks',
        'sort_order',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship: Grading scheme
     */
    public function gradingScheme(): BelongsTo
    {
        return $this->belongsTo(GradingScheme::class);
    }

    /**
     * Get score range as string
     */
    public function getScoreRangeAttribute(): string
    {
        return "{$this->min_score} - {$this->max_score}";
    }

    /**
     * Get full display name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->label) {
            return "{$this->grade} ({$this->label})";
        }

        return $this->grade;
    }

    /**
     * Check if score falls within this band
     */
    public function containsScore(float $score): bool
    {
        return $score >= $this->min_score && $score <= $this->max_score;
    }

    /**
     * Get badge color based on grade point
     */
    public function getBadgeColorAttribute(): string
    {
        if (!$this->grade_point) {
            return 'secondary';
        }

        if ($this->grade_point >= 4.0) return 'success';
        if ($this->grade_point >= 3.0) return 'primary';
        if ($this->grade_point >= 2.0) return 'info';
        if ($this->grade_point >= 1.0) return 'warning';

        return 'danger';
    }
}
