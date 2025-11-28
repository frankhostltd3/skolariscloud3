<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exercise_id',
        'student_id',
        'submission_text',
        'attachments',
        'submitted_at',
        'score',
        'feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    protected $appends = ['is_late', 'is_graded', 'status_label'];

    /**
     * Relationships
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Accessors
     */
    public function getIsLateAttribute()
    {
        if (!$this->exercise || !$this->exercise->due_date) {
            return false;
        }

        return $this->submitted_at->isAfter($this->exercise->due_date);
    }

    public function getIsGradedAttribute()
    {
        return $this->score !== null;
    }

    public function getStatusLabelAttribute()
    {
        if ($this->is_graded) {
            return 'Graded';
        }

        if ($this->is_late) {
            return 'Late Submission';
        }

        return 'Submitted';
    }

    public function getScorePercentageAttribute()
    {
        if (!$this->is_graded || !$this->exercise || !$this->exercise->max_score) {
            return null;
        }

        return round(($this->score / $this->exercise->max_score) * 100, 2);
    }

    /**
     * Methods
     */
    public function grade($score, $feedback, $gradedBy)
    {
        $this->update([
            'score' => $score,
            'feedback' => $feedback,
            'graded_at' => now(),
            'graded_by' => $gradedBy,
        ]);

        // Send notification to student
        $this->student->notify(new \App\Notifications\AssignmentGradedNotification($this));
    }

    /**
     * Calculate score with late penalty
     */
    public function calculateScoreWithPenalty()
    {
        if (!$this->is_late || !$this->exercise->late_penalty_percent) {
            return $this->score;
        }

        $penalty = ($this->score * $this->exercise->late_penalty_percent) / 100;
        return max(0, $this->score - $penalty);
    }
}
