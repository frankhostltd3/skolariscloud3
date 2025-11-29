<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'meeting_type',
        'platform',
        'meeting_link',
        'location',
        'status',
        'organizer_id',
        'student_id',
        'teacher_id',
        'participants',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'participants' => 'array',
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
    ];

    /**
     * Get the organizer of this meeting.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the student associated with this meeting.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the teacher associated with this meeting.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Scope for active meetings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for meetings by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('meeting_type', $type);
    }

    /**
     * Scope for meetings by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for upcoming meetings.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())
                    ->where('status', 'scheduled');
    }

    /**
     * Scope for meetings where user is a participant.
     */
    public function scopeForParticipant($query, $userId)
    {
        return $query->whereJsonContains('participants', (string)$userId);
    }

    /**
     * Check if user is a participant.
     */
    public function hasParticipant($userId): bool
    {
        $participants = $this->participants ?? [];
        return in_array((string)$userId, $participants);
    }

    /**
     * Get formatted scheduled time.
     */
    public function getFormattedScheduledTimeAttribute()
    {
        return $this->scheduled_at->format('M j, Y g:i A');
    }

    /**
     * Get meeting type label.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->meeting_type) {
            'general' => 'General Meeting',
            'parent_teacher' => 'Parent-Teacher Meeting',
            'counseling' => 'Counseling Session',
            'academic' => 'Academic Meeting',
            default => 'Meeting'
        };
    }

    /**
     * Get platform label.
     */
    public function getPlatformLabelAttribute()
    {
        return match($this->platform) {
            'zoom' => 'Zoom',
            'google_meet' => 'Google Meet',
            'teams' => 'Microsoft Teams',
            'physical' => 'In-Person',
            default => $this->platform ?? 'Unknown'
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => '#0d6efd',
            'completed' => '#198754',
            'cancelled' => '#dc3545',
            'rescheduled' => '#fd7e14',
            default => '#6c757d'
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'rescheduled' => 'Rescheduled',
            default => 'Unknown'
        };
    }

    /**
     * Get type color.
     */
    public function getTypeColorAttribute()
    {
        return match($this->meeting_type) {
            'general' => 'secondary',
            'parent_teacher' => 'primary',
            'counseling' => 'info',
            'academic' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Check if meeting is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture() && $this->status === 'scheduled';
    }

    /**
     * Get end time.
     */
    public function getEndTimeAttribute()
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }
}
