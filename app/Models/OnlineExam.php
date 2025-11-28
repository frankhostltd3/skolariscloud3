<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class OnlineExam extends Model
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
        'duration_minutes',
        'total_marks',
        'pass_marks',
        'exam_date',
        'starts_at',
        'ends_at',
        'status',
        'proctored',
        'allow_backtrack',
        'auto_submit_on',
        'max_tab_switches',
        'disable_copy_paste',
        'shuffle_questions',
        'shuffle_answers',
        'show_results_immediately',
        'grading_method',
        'creation_method',
        'activation_mode',
        'approval_status',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
        'submitted_for_review_at',
        'generation_status',
        'generation_provider',
        'generation_metadata',
        'activated_at',
        'completed_at',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'proctored' => 'boolean',
        'disable_copy_paste' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_backtrack' => 'boolean',
        'reviewed_at' => 'datetime',
        'submitted_for_review_at' => 'datetime',
        'generation_metadata' => 'array',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $appends = ['status_label', 'is_available', 'attempts_count'];

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

    public function sections()
    {
        return $this->hasMany(OnlineExamSection::class)->orderBy('order');
    }

    public function questions()
    {
        return $this->hasManyThrough(
            OnlineExamQuestion::class,
            OnlineExamSection::class,
            'online_exam_id', // Foreign key on sections table
            'section_id',     // Foreign key on questions table
            'id',             // Local key on exams table
            'id'              // Local key on sections table
        );
    }

    public function attempts()
    {
        return $this->hasMany(OnlineExamAttempt::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNeedsReview($query)
    {
        return $query->whereIn('approval_status', ['pending_review', 'changes_requested']);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getIsAvailableAttribute()
    {
        if ($this->status !== 'active' && $this->status !== 'scheduled') {
            return false;
        }

        $now = now();

        $start = $this->start_time;
        if ($start && $now->isBefore($start)) {
            return false;
        }

        $end = $this->end_time;
        if ($end && $now->isAfter($end)) {
            return false;
        }

        return true;
    }

    public function getAttemptsCountAttribute()
    {
        return $this->attempts()->count();
    }

    /**
     * Methods
     */
    public function publish()
    {
        $this->update(['status' => 'scheduled']);
    }

    public function activate()
    {
        $this->forceFill([
            'status' => 'active',
            'activated_at' => $this->activated_at ?? now(),
        ])->save();
    }

    public function complete()
    {
        $this->forceFill([
            'status' => 'completed',
            'completed_at' => $this->completed_at ?? now(),
        ])->save();
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getAttemptByStudent($studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->first();
    }

    public function hasAttempted($studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->exists();
    }

    public function canAttempt($studentId)
    {
        if (!$this->is_available) {
            return false;
        }

        // Check if student already attempted
        if ($this->hasAttempted($studentId)) {
            return false;
        }

        return true;
    }

    public function getTotalQuestions()
    {
        return $this->questions()->count();
    }

    public function calculateTotalMarks()
    {
        return $this->questions()->sum('marks');
    }

    public function getAverageScore()
    {
        return $this->attempts()
            ->whereNotNull('score')
            ->avg('score') ?? 0;
    }

    public function getPassRate()
    {
        $totalAttempts = $this->attempts()->whereNotNull('score')->count();

        if ($totalAttempts === 0) {
            return 0;
        }

        $passedAttempts = $this->attempts()
            ->whereNotNull('score')
            ->where('score', '>=', $this->pass_marks)
            ->count();

        return round(($passedAttempts / $totalAttempts) * 100, 2);
    }

    public function getPendingGradingCount()
    {
        return $this->attempts()
            ->whereNotNull('submitted_at')
            ->whereNull('score')
            ->count();
    }

    public function getViolationsCount()
    {
        return $this->attempts()->sum('tab_switches_count');
    }

    public function getStartTimeAttribute(): ?Carbon
    {
        $value = $this->attributes['starts_at'] ?? null;

        return $value ? Carbon::parse($value) : null;
    }

    public function setStartTimeAttribute($value): void
    {
        $this->attributes['starts_at'] = $value;
    }

    public function getEndTimeAttribute(): ?Carbon
    {
        $value = $this->attributes['ends_at'] ?? null;

        return $value ? Carbon::parse($value) : null;
    }

    public function setEndTimeAttribute($value): void
    {
        $this->attributes['ends_at'] = $value;
    }

    public function markPendingReview(): void
    {
        $this->forceFill([
            'approval_status' => 'pending_review',
            'submitted_for_review_at' => now(),
        ])->save();
    }

    public function markDraft(): void
    {
        $this->forceFill([
            'approval_status' => 'draft',
            'submitted_for_review_at' => null,
            'review_notes' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ])->save();
    }

    public function approve(User $reviewer, ?string $notes = null): void
    {
        $this->forceFill([
            'approval_status' => 'approved',
            'review_notes' => $notes,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();
    }

    public function requestChanges(User $reviewer, ?string $notes = null): void
    {
        $this->forceFill([
            'approval_status' => 'changes_requested',
            'review_notes' => $notes,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'status' => 'draft',
        ])->save();
    }

    public function reject(User $reviewer, ?string $notes = null): void
    {
        $this->forceFill([
            'approval_status' => 'rejected',
            'review_notes' => $notes,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'status' => 'archived',
        ])->save();
    }

    public function shouldActivate(Carbon $moment): bool
    {
        return $this->approval_status === 'approved'
            && $this->activation_mode !== 'manual'
            && $this->status === 'scheduled'
            && $this->start_time
            && $moment->greaterThanOrEqualTo($this->start_time);
    }

    public function shouldComplete(Carbon $moment): bool
    {
        return $this->status === 'active'
            && $this->end_time
            && $moment->greaterThanOrEqualTo($this->end_time);
    }
}
