<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'due_date',
        'max_marks',
        'attachment_path',
        'allow_resubmission',
        'published',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_resubmission' => 'boolean',
        'published' => 'boolean',
    ];

    /**
     * Get the teacher who created the assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the class this assignment is for.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the subject this assignment is for.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get all submissions for this assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Check if assignment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Get submission count.
     */
    public function getSubmissionCountAttribute(): int
    {
        return $this->submissions()->count();
    }

    /**
     * Check if late submission is allowed.
     */
    public function getAllowLateSubmissionAttribute(): bool
    {
        return $this->allow_resubmission; // Can be a separate field
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        
        if ($this->due_date && $this->due_date->diffInDays(now()) <= 2) {
            return 'Due Soon';
        }
        
        return 'Active';
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->isOverdue()) {
            return 'danger';
        }
        
        if ($this->due_date && $this->due_date->diffInDays(now()) <= 2) {
            return 'warning';
        }
        
        return 'success';
    }
}
