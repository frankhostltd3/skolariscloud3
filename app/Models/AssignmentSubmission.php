<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'notes',
        'attachment_path',
        'submitted_at',
        'marks',
        'feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    /**
     * Get the assignment this submission belongs to.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student who made this submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the teacher who graded this submission.
     */
    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Check if submission is graded.
     */
    public function isGraded(): bool
    {
        return !is_null($this->graded_at);
    }

    /**
     * Check if submission is late.
     */
    public function isLate(): bool
    {
        return $this->submitted_at && 
               $this->assignment->due_date && 
               $this->submitted_at->isAfter($this->assignment->due_date);
    }
}
