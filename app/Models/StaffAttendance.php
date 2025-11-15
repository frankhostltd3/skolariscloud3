<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'staff_attendance';

    protected $fillable = [
        'school_id',
        'staff_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'minutes_late',
        'hours_worked',
        'leave_reason',
        'leave_document',
        'approved',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'minutes_late' => 'integer',
        'hours_worked' => 'decimal:2',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the school that owns the staff attendance.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the staff member.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the approver.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to filter by school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('attendance_date', [$from, $to]);
    }

    /**
     * Scope to filter by today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get present staff.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope to get absent staff.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
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
            'half_day' => 'bg-info',
            'on_leave' => 'bg-primary',
            'sick_leave' => 'bg-secondary',
            'official_duty' => 'bg-info',
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
            'half_day' => 'Half Day',
            'on_leave' => 'On Leave',
            'sick_leave' => 'Sick Leave',
            'official_duty' => 'Official Duty',
            default => ucfirst($this->status),
        };
    }
}
