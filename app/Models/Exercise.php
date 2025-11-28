<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
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
        'content',
        'due_date',
        'max_score',
        'allow_late_submission',
        'late_penalty_percent',
        'submission_type',
        'attachments',
        'auto_grade',
        'show_answers_after_submit',
        'allow_file_upload',
        'allow_text_response',
        'allowed_file_types',
        'max_file_size_mb',
        'rubric',
        'plagiarism_check_enabled',
        'peer_review_enabled',
        'peer_review_count',
        'status',
        'version',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
        'late_penalty_percent' => 'decimal:2',
        'attachments' => 'array',
        'auto_grade' => 'boolean',
        'show_answers_after_submit' => 'boolean',
        'allow_file_upload' => 'boolean',
        'allow_text_response' => 'boolean',
        'allowed_file_types' => 'array',
        'rubric' => 'array',
        'plagiarism_check_enabled' => 'boolean',
        'peer_review_enabled' => 'boolean',
    ];

    protected $appends = ['status', 'submissions_count', 'graded_count', 'is_overdue', 'completion_rate'];

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

    public function questions()
    {
        return $this->hasMany(ExerciseQuestion::class)->orderBy('order');
    }

    public function fileAttachments()
    {
        return $this->hasMany(ExerciseAttachment::class);
    }

    /**
     * Check if this exercise has auto-gradable questions
     */
    public function getHasAutoGradableQuestionsAttribute(): bool
    {
        return $this->questions()->whereIn('type', ExerciseQuestion::AUTO_GRADABLE_TYPES)->exists();
    }

    /**
     * Get total marks from all questions
     */
    public function getTotalQuestionMarksAttribute(): float
    {
        return (float) $this->questions()->sum('marks');
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

    public function getCompletionRateAttribute()
    {
        $totalStudents = $this->class->students()->count();
        if ($totalStudents === 0) return 0;
        
        $submitted = $this->submissions()->count();
        return round(($submitted / $totalStudents) * 100, 1);
    }

    /**
     * Check if assignment has rubric
     */
    public function hasRubric()
    {
        return !empty($this->rubric) && is_array($this->rubric);
    }

    /**
     * Get total rubric points
     */
    public function getTotalRubricPoints()
    {
        if (!$this->hasRubric()) return 0;
        
        return collect($this->rubric)->sum('points');
    }

    /**
     * Get students who haven't submitted
     */
    public function getStudentsNotSubmitted()
    {
        $submittedStudentIds = $this->submissions()->pluck('student_id');
        return $this->class->students()->whereNotIn('id', $submittedStudentIds)->get();
    }

    /**
     * Check if assignment is active
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->is_overdue;
    }

    /**
     * Check if assignment is closed
     */
    public function isClosed()
    {
        return $this->status === 'closed' || ($this->is_overdue && !$this->allow_late_submission);
    }

    /**
     * Scope for archived assignments
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope for draft assignments
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
