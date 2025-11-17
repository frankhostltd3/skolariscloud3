<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'max_score',
        'allow_late_submission',
        'late_penalty_percent',
        'submission_type',
        'attachments',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
        'late_penalty_percent' => 'decimal:2',
        'attachments' => 'array',
    ];

    protected $appends = ['status', 'submissions_count', 'graded_count', 'is_overdue'];

    /**
     * Relationships
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function submissions()
    {
        return $this->hasMany(ExerciseSubmission::class);
    }

    /**
     * Scopes
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeActive($query)
    {
        return $query->where('due_date', '>', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    /**
     * Accessors
     */
    public function getStatusAttribute()
    {
        if ($this->due_date) {
            return $this->due_date->isPast() ? 'closed' : 'active';
        }
        return 'active';
    }

    public function getSubmissionsCountAttribute()
    {
        return $this->submissions()->count();
    }

    public function getGradedCountAttribute()
    {
        return $this->submissions()->whereNotNull('score')->count();
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Methods
     */
    public function getSubmissionByStudent($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    public function hasSubmitted($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->exists();
    }

    public function canSubmit($studentId)
    {
        // Check if already submitted
        if ($this->hasSubmitted($studentId)) {
            return false;
        }

        // Check if due date passed and late submission not allowed
        if ($this->is_overdue && !$this->allow_late_submission) {
            return false;
        }

        return true;
    }

    public function calculateScore($rawScore, $submittedAt)
    {
        $score = $rawScore;

        // Apply late penalty if submission is late
        if ($this->due_date && $submittedAt->isAfter($this->due_date)) {
            $penalty = ($score * $this->late_penalty_percent) / 100;
            $score = max(0, $score - $penalty);
        }

        return round($score, 2);
    }

    public function getPendingSubmissionsCount()
    {
        return $this->submissions()->whereNull('score')->count();
    }

    public function getAverageScore()
    {
        return $this->submissions()->whereNotNull('score')->avg('score') ?? 0;
    }
}
