<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'teacher_id',
        'class_id',
        'title',
        'description',
        'duration_minutes',
        'total_marks',
        'start_at',
        'end_at',
        'is_active',
        'allow_late_submission',
        'late_penalty_percent',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'allow_late_submission' => 'boolean',
        'duration_minutes' => 'integer',
        'total_marks' => 'integer',
        'late_penalty_percent' => 'integer',
    ];

    /**
     * Get the school that owns the quiz.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the teacher who created the quiz.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the class this quiz is assigned to.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get all attempts for this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Scope a query to only include quizzes for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include active quizzes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include quizzes within date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('end_at', [$from, $to]);
    }
}
