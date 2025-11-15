<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'arrival_time',
        'departure_time',
        'minutes_late',
        'excuse_reason',
        'excuse_document',
        'notified_parent',
        'notification_sent_at',
        'notes',
    ];

    protected $casts = [
        'arrival_time' => 'datetime:H:i',
        'departure_time' => 'datetime:H:i',
        'minutes_late' => 'integer',
        'notified_parent' => 'boolean',
        'notification_sent_at' => 'datetime',
    ];

    /**
     * Get the attendance session for this record.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Get the student for this record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get present students.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope to get absent students.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope to get late students.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Check if student is present.
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * Check if student is absent.
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * Check if student is late.
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'present' => 'bg-success',
            'absent' => 'bg-danger',
            'late' => 'bg-warning text-dark',
            'excused' => 'bg-info',
            'sick' => 'bg-secondary',
            'half_day' => 'bg-primary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'excused' => 'Excused',
            'sick' => 'Sick',
            'half_day' => 'Half Day',
            default => ucfirst($this->status),
        };
    }
}
