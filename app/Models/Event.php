<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'event_type',
        'priority',
        'target_audience',
        'is_all_day',
        'color',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_audience' => 'array',
        'is_all_day' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active events.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for events by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope for events by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope for events in date range.
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
              ->orWhere(function($q2) use ($start, $end) {
                  $q2->where('start_date', '<=', $start)
                     ->where('end_date', '>=', $end);
              });
        });
    }

    /**
     * Check if event is for students.
     */
    public function isForStudents(): bool
    {
        $audience = $this->target_audience ?? [];
        return in_array('students', $audience) || empty($audience);
    }

    /**
     * Get formatted date range.
     */
    public function getFormattedDateRangeAttribute()
    {
        if ($this->is_all_day) {
            if ($this->end_date && $this->start_date->format('Y-m-d') !== $this->end_date->format('Y-m-d')) {
                return $this->start_date->format('M j') . ' - ' . $this->end_date->format('M j, Y');
            }
            return $this->start_date->format('M j, Y');
        }

        if ($this->end_date) {
            return $this->start_date->format('M j, Y g:i A') . ' - ' . $this->end_date->format('g:i A');
        }

        return $this->start_date->format('M j, Y g:i A');
    }

    /**
     * Get event type label.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->event_type) {
            'general' => 'General Event',
            'holiday' => 'Holiday',
            'exam' => 'Examination',
            'sports' => 'Sports Event',
            'cultural' => 'Cultural Event',
            'academic' => 'Academic Event',
            default => 'Event'
        };
    }

    /**
     * Get event color.
     */
    public function getColorAttribute()
    {
        return match($this->event_type) {
            'general' => '#6c757d',
            'holiday' => '#198754',
            'exam' => '#dc3545',
            'sports' => '#fd7e14',
            'cultural' => '#0dcaf0',
            'academic' => '#6610f2',
            default => '#0d6efd'
        };
    }

    /**
     * Get priority color.
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => '#6c757d',
            'normal' => '#0d6efd',
            'high' => '#fd7e14',
            default => '#0d6efd'
        };
    }
}
