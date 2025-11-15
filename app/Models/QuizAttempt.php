<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'score_auto',
        'score_manual',
        'score_total',
        'minutes_late',
        'status',
        'answers',
        'feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score_auto' => 'decimal:2',
        'score_manual' => 'decimal:2',
        'score_total' => 'decimal:2',
        'minutes_late' => 'integer',
        'answers' => 'array',
    ];

    /**
     * Get the school that owns the attempt.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the quiz this attempt belongs to.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the student who made this attempt.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Scope a query to only include attempts for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include late submissions.
     */
    public function scopeLate($query)
    {
        return $query->where('minutes_late', '>', 0);
    }

    /**
     * Scope a query to only include submitted attempts.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('submitted_at', [$from, $to]);
    }

    /**
     * Scope a query to filter by student name or ID.
     */
    public function scopeStudentSearch($query, $search)
    {
        return $query->whereHas('student', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }
}
