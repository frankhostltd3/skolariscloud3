<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class VirtualClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'platform',
        'meeting_id',
        'meeting_password',
        'meeting_url',
        'scheduled_at',
        'duration_minutes',
        'status',
        'recording_url',
        'auto_record',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_end_date',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'recurrence_end_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'auto_record' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    protected $appends = ['status_label', 'platform_label', 'is_live', 'can_join'];

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

    public function attendances()
    {
        return $this->hasMany(VirtualClassAttendance::class);
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '>', now());
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
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
            'scheduled' => 'Scheduled',
            'live' => 'Live Now',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getPlatformLabelAttribute()
    {
        return match($this->platform) {
            'zoom' => 'Zoom',
            'google_meet' => 'Google Meet',
            'microsoft_teams' => 'Microsoft Teams',
            'youtube' => 'YouTube Live',
            'other' => 'Other',
            default => 'Unknown'
        };
    }

    public function getIsLiveAttribute()
    {
        if ($this->status === 'live') {
            return true;
        }

        if ($this->scheduled_at && $this->duration_minutes) {
            $start = $this->scheduled_at;
            $end = $start->copy()->addMinutes($this->duration_minutes);
            $now = now();

            return $now->between($start, $end) && $this->status === 'scheduled';
        }

        return false;
    }

    public function getCanJoinAttribute()
    {
        if ($this->status === 'live') {
            return true;
        }

        // Allow joining 15 minutes before scheduled time
        if ($this->scheduled_at) {
            $joinTime = $this->scheduled_at->copy()->subMinutes(15);
            return now()->greaterThanOrEqualTo($joinTime) && $this->status === 'scheduled';
        }

        return false;
    }

    public function getEndTimeAttribute()
    {
        if ($this->scheduled_at && $this->duration_minutes) {
            return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
        }
        return null;
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . ' minutes';
    }

    /**
     * Methods
     */
    public function start()
    {
        $this->update([
            'status' => 'live',
            'started_at' => now(),
        ]);
    }

    public function end()
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function recordAttendance($studentId, $joinedAt = null, $leftAt = null)
    {
        return $this->attendances()->create([
            'student_id' => $studentId,
            'joined_at' => $joinedAt ?? now(),
            'left_at' => $leftAt,
            'duration_minutes' => $leftAt ? now()->diffInMinutes($joinedAt ?? now()) : null,
        ]);
    }

    public function getAttendanceCount()
    {
        return $this->attendances()->distinct('student_id')->count();
    }

    public function getAttendancePercentage()
    {
        if (!$this->class) {
            return 0;
        }

        $totalStudents = $this->class->activeEnrollments()->count();
        if ($totalStudents === 0) {
            return 0;
        }

        $attended = $this->getAttendanceCount();
        return round(($attended / $totalStudents) * 100, 2);
    }
}
