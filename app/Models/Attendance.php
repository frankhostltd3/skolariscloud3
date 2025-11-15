<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\ClassStream;
use App\Models\Academic\Subject;

class Attendance extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'attendance';

    protected $fillable = [
        'school_id',
        'class_id',
        'class_stream_id',
        'subject_id',
        'teacher_id',
        'attendance_date',
        'time_in',
        'time_out',
        'attendance_type',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
    ];

    /**
     * Get the school that owns the attendance.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the class for this attendance.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get the class stream for this attendance.
     */
    public function classStream(): BelongsTo
    {
        return $this->belongsTo(ClassStream::class);
    }

    /**
     * Get the subject for this attendance.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher for this attendance.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get all attendance records for this attendance.
     */
    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
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
     * Get attendance statistics.
     */
    public function getStatistics()
    {
        $total = $this->records()->count();
        $present = $this->records()->where('status', 'present')->count();
        $absent = $this->records()->where('status', 'absent')->count();
        $late = $this->records()->where('status', 'late')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }
}
