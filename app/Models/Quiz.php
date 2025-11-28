<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'instructions',
        'available_from',
        'available_until',
        'duration_minutes',
        'total_marks',
        'pass_marks',
        'max_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_results_immediately',
        'show_correct_answers',
        'allow_review',
        'status',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'allow_review' => 'boolean',
        'duration_minutes' => 'integer',
        'total_marks' => 'integer',
        'pass_marks' => 'integer',
        'max_attempts' => 'integer',
    ];

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
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the subject this quiz belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Legacy relation for quizzes assigned to multiple classes via pivot.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'quiz_class', 'quiz_id', 'class_id');
    }

    /**
     * Get all questions for this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    /**
     * Get all attempts for this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Scope a query to only include active quizzes.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include quizzes within date range.
     */
    public function scopeAvailable($query)
    {
        $now = now();
        return $query->where('status', 'published')
                     ->where(function($q) use ($now) {
                         $q->whereNull('available_from')->orWhere('available_from', '<=', $now);
                     })
                     ->where(function($q) use ($now) {
                         $q->whereNull('available_until')->orWhere('available_until', '>=', $now);
                     });
    }

    /**
     * Backward-compatible accessor for start_at column used in student portal.
     */
    public function getStartAtAttribute()
    {
        if (!empty($this->attributes['available_from'])) {
            return $this->asDateTime($this->attributes['available_from']);
        }

        if (array_key_exists('start_at', $this->attributes) && !empty($this->attributes['start_at'])) {
            return $this->asDateTime($this->attributes['start_at']);
        }

        return null;
    }

    public function setStartAtAttribute($value): void
    {
        $this->attributes['available_from'] = $value;
    }

    public function getEndAtAttribute()
    {
        if (!empty($this->attributes['available_until'])) {
            return $this->asDateTime($this->attributes['available_until']);
        }

        if (array_key_exists('end_at', $this->attributes) && !empty($this->attributes['end_at'])) {
            return $this->asDateTime($this->attributes['end_at']);
        }

        return null;
    }

    public function setEndAtAttribute($value): void
    {
        $this->attributes['available_until'] = $value;
    }

    public function getTotalPointsAttribute()
    {
        if (!empty($this->attributes['total_points'])) {
            return $this->attributes['total_points'];
        }

        if (!empty($this->attributes['total_marks'])) {
            return $this->attributes['total_marks'];
        }

        return $this->questions->sum('marks');
    }
}
