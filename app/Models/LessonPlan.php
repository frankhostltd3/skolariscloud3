<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPlan extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    public const REVIEW_NOT_SUBMITTED = 'not_submitted';
    public const REVIEW_PENDING = 'pending';
    public const REVIEW_APPROVED = 'approved';
    public const REVIEW_REVISION = 'revision_requested';
    public const REVIEW_REJECTED = 'rejected';

    protected $connection = 'tenant';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'lesson_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'objectives',
        'materials_needed',
        'introduction',
        'main_content',
        'activities',
        'assessment',
        'homework',
        'notes',
        'status',
        'review_status',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'reviewed_by',
        'review_feedback',
        'is_template',
        'delivered_at',
        'requires_revision',
        'archived_at',
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'duration_minutes' => 'integer',
        'objectives' => 'array',
        'materials_needed' => 'array',
        'activities' => 'array',
        'is_template' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
        'requires_revision' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected $appends = ['status_label', 'review_status_label'];

    protected static function booted(): void
    {
        static::saving(function (self $lessonPlan): void {
            $lessonPlan->sanitizeStructuredFields();

            if (! $lessonPlan->duration_minutes && $lessonPlan->start_time && $lessonPlan->end_time) {
                $lessonPlan->duration_minutes = self::calculateDuration($lessonPlan->start_time, $lessonPlan->end_time);
            }

            if ($lessonPlan->status === self::STATUS_ARCHIVED && is_null($lessonPlan->archived_at)) {
                $lessonPlan->archived_at = now();
            }

            if ($lessonPlan->status !== self::STATUS_ARCHIVED) {
                $lessonPlan->archived_at = null;
            }
        });
    }

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

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeForReviewStatus($query, ?string $status)
    {
        return $status ? $query->where('review_status', $status) : $query;
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ARCHIVED => 'Archived',
            default => 'Unknown',
        };
    }

    public function getReviewStatusLabelAttribute(): string
    {
        return match ($this->review_status) {
            self::REVIEW_NOT_SUBMITTED => 'Not Submitted',
            self::REVIEW_PENDING => 'Pending Review',
            self::REVIEW_APPROVED => 'Approved',
            self::REVIEW_REVISION => 'Revision Requested',
            self::REVIEW_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SCHEDULED => 'info',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_ARCHIVED => 'dark',
            default => 'secondary',
        };
    }

    public function getReviewStatusBadgeClassAttribute(): string
    {
        return match ($this->review_status) {
            self::REVIEW_NOT_SUBMITTED => 'secondary',
            self::REVIEW_PENDING => 'warning text-dark',
            self::REVIEW_APPROVED => 'success',
            self::REVIEW_REVISION => 'info',
            self::REVIEW_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Methods
     */
    public function publish(): void
    {
        $this->update(['status' => self::STATUS_SCHEDULED]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'delivered_at' => now(),
        ]);
    }

    public function submitForReview(): void
    {
        $this->update([
            'review_status' => self::REVIEW_PENDING,
            'submitted_at' => now(),
            'requires_revision' => false,
        ]);
    }

    public function requestRevision(?string $feedback, int $reviewerId): void
    {
        $this->update([
            'review_status' => self::REVIEW_REVISION,
            'requires_revision' => true,
            'review_feedback' => $feedback,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'approved_at' => null,
        ]);
    }

    public function approve(?string $feedback, int $reviewerId): void
    {
        $this->update([
            'review_status' => self::REVIEW_APPROVED,
            'requires_revision' => false,
            'review_feedback' => $feedback,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'approved_at' => now(),
            'status' => $this->status === self::STATUS_DRAFT ? self::STATUS_SCHEDULED : $this->status,
        ]);
    }

    public function reject(?string $feedback, int $reviewerId): void
    {
        $this->update([
            'review_status' => self::REVIEW_REJECTED,
            'requires_revision' => false,
            'review_feedback' => $feedback,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'approved_at' => null,
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'review_status' => self::REVIEW_NOT_SUBMITTED,
            'requires_revision' => false,
            'review_feedback' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'approved_at' => null,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => self::STATUS_ARCHIVED,
            'archived_at' => now(),
        ]);
    }

    public function unarchive(): void
    {
        $this->update([
            'status' => self::STATUS_DRAFT,
            'archived_at' => null,
        ]);
    }

    public function saveAsTemplate(): self
    {
        $template = $this->replicate();
        $template->is_template = true;
        $template->status = self::STATUS_DRAFT;
        $template->lesson_date = null;
        $template->review_status = self::REVIEW_NOT_SUBMITTED;
        $template->submitted_at = null;
        $template->approved_at = null;
        $template->reviewed_at = null;
        $template->reviewed_by = null;
        $template->review_feedback = null;
        $template->save();

        return $template;
    }

    public function createFromTemplate($classId, $lessonDate)
    {
        $plan = $this->replicate();
        $plan->is_template = false;
        $plan->class_id = $classId;
        $plan->lesson_date = $lessonDate;
        $plan->status = self::STATUS_DRAFT;
        $plan->review_status = self::REVIEW_NOT_SUBMITTED;
        $plan->save();

        return $plan;
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public static function reviewStatusOptions(): array
    {
        return [
            self::REVIEW_NOT_SUBMITTED => 'Not Submitted',
            self::REVIEW_PENDING => 'Pending Review',
            self::REVIEW_APPROVED => 'Approved',
            self::REVIEW_REVISION => 'Revision Requested',
            self::REVIEW_REJECTED => 'Rejected',
        ];
    }

    public function isEditable(): bool
    {
        if ($this->status === self::STATUS_ARCHIVED) {
            return false;
        }

        return ! in_array($this->review_status, [self::REVIEW_APPROVED, self::REVIEW_REJECTED], true);
    }

    public function canSubmit(): bool
    {
        return in_array($this->review_status, [self::REVIEW_NOT_SUBMITTED, self::REVIEW_REVISION], true)
            && $this->status !== self::STATUS_ARCHIVED;
    }

    public function canResubmit(): bool
    {
        return $this->review_status === self::REVIEW_REVISION;
    }

    public function canMarkDelivered(): bool
    {
        return $this->review_status === self::REVIEW_APPROVED
            && $this->status !== self::STATUS_COMPLETED;
    }

    public function sanitizeStructuredFields(): void
    {
        $this->objectives = $this->cleanArrayField($this->objectives);
        $this->materials_needed = $this->cleanArrayField($this->materials_needed);
        $this->activities = $this->cleanArrayField($this->activities);
    }

    protected function cleanArrayField($value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        $cleaned = array_values(array_filter(array_map(function ($item) {
            if (is_string($item)) {
                $trimmed = trim($item);

                return $trimmed === '' ? null : $trimmed;
            }

            return $item;
        }, $value), fn ($item) => ! is_null($item)));

        return $cleaned === [] ? null : $cleaned;
    }

    protected static function calculateDuration($start, $end): ?int
    {
        try {
            $startTime = 
                $start instanceof \DateTimeInterface ? $start : now()->setTimeFromTimeString((string) $start);
            $endTime = 
                $end instanceof \DateTimeInterface ? $end : now()->setTimeFromTimeString((string) $end);

            $minutes = $startTime->diffInMinutes($endTime, false);

            return $minutes > 0 ? $minutes : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
