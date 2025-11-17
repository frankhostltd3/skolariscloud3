<?php

namespace App\Models\Academic;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimetableEntry extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'class_id',
        'class_stream_id',
        'subject_id',
        'teacher_id',
        'room_id',
        'day_of_week',
        'starts_at',
        'ends_at',
        'room',
        'notes',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(ClassStream::class, 'class_stream_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to current school's timetable entries only
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope to specific class
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to specific class stream
     */
    public function scopeForStream($query, $streamId)
    {
        return $query->where('class_stream_id', $streamId);
    }

    /**
     * Scope to specific day of week
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope to specific teacher
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to specific subject
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to entries within a time range
     */
    public function scopeWithinTimeRange($query, $startTime, $endTime)
    {
        return $query->where(function ($q) use ($startTime, $endTime) {
            $q->whereBetween('starts_at', [$startTime, $endTime])
                ->orWhereBetween('ends_at', [$startTime, $endTime])
                ->orWhere(function ($q2) use ($startTime, $endTime) {
                    $q2->where('starts_at', '<=', $startTime)
                        ->where('ends_at', '>=', $endTime);
                });
        });
    }

    /**
     * Scope for entries on a specific day and time (conflict detection)
     */
    public function scopeConflictsWith($query, $dayOfWeek, $startsAt, $endsAt, $excludeId = null)
    {
        $query->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($startsAt, $endsAt) {
                // Check if times overlap
                $q->where(function ($q2) use ($startsAt, $endsAt) {
                    // New entry starts during existing entry
                    $q2->where('starts_at', '<=', $startsAt)
                        ->where('ends_at', '>', $startsAt);
                })->orWhere(function ($q2) use ($startsAt, $endsAt) {
                    // New entry ends during existing entry
                    $q2->where('starts_at', '<', $endsAt)
                        ->where('ends_at', '>=', $endsAt);
                })->orWhere(function ($q2) use ($startsAt, $endsAt) {
                    // New entry completely contains existing entry
                    $q2->where('starts_at', '>=', $startsAt)
                        ->where('ends_at', '<=', $endsAt);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    /**
     * Scope to order by day and time
     */
    public function scopeOrderedBySchedule($query)
    {
        return $query->orderBy('day_of_week')->orderBy('starts_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Get the day name (e.g., "Monday")
     */
    public function getDayNameAttribute(): string
    {
        return Carbon::create()->startOfWeek()->addDays($this->day_of_week - 1)->format('l');
    }

    /**
     * Get short day name (e.g., "Mon")
     */
    public function getShortDayNameAttribute(): string
    {
        return Carbon::create()->startOfWeek()->addDays($this->day_of_week - 1)->format('D');
    }

    /**
     * Get the duration in minutes
     */
    public function getDurationInMinutesAttribute(): int
    {
        $start = Carbon::parse($this->starts_at);
        $end = Carbon::parse($this->ends_at);
        return $start->diffInMinutes($end);
    }

    /**
     * Get formatted time range (e.g., "08:00 - 09:00")
     */
    public function getTimeRangeAttribute(): string
    {
        return sprintf('%s - %s',
            Carbon::parse($this->starts_at)->format('H:i'),
            Carbon::parse($this->ends_at)->format('H:i')
        );
    }

    /**
     * Get formatted full schedule (e.g., "Monday 08:00 - 09:00")
     */
    public function getFullScheduleAttribute(): string
    {
        return sprintf('%s %s', $this->day_name, $this->time_range);
    }

    /**
     * Get class with stream name (e.g., "Grade 5 - Stream A")
     */
    public function getClassWithStreamAttribute(): string
    {
        $className = $this->class->name ?? 'Unknown Class';

        if ($this->stream) {
            return sprintf('%s - %s', $className, $this->stream->name);
        }

        return $className;
    }

    /**
     * Check if room is occupied for given slot
     */
    public static function roomOccupied($schoolId, $roomId, $day, $start, $end): bool
    {
        return static::forSchool($schoolId)
            ->where('room_id', $roomId)
            ->conflictsWith($day, $start, $end)
            ->exists();
    }

    /**
     * Check if this entry conflicts with teacher's schedule
     */
    public function hasTeacherConflict(): bool
    {
        if (!$this->teacher_id) {
            return false;
        }

        return static::forSchool($this->school_id)
            ->forTeacher($this->teacher_id)
            ->conflictsWith(
                $this->day_of_week,
                $this->starts_at,
                $this->ends_at,
                $this->id
            )
            ->exists();
    }

    /**
     * Check if this entry conflicts with class schedule
     */
    public function hasClassConflict(): bool
    {
        $query = static::forSchool($this->school_id)
            ->forClass($this->class_id)
            ->conflictsWith(
                $this->day_of_week,
                $this->starts_at,
                $this->ends_at,
                $this->id
            );

        // If stream is specified, check for that specific stream
        if ($this->class_stream_id) {
            $query->where(function ($q) {
                $q->where('class_stream_id', $this->class_stream_id)
                    ->orWhereNull('class_stream_id');
            });
        }

        return $query->exists();
    }

    /**
     * Get all conflicts for this entry
     */
    public function getConflicts(): array
    {
        $conflicts = [];

        if ($this->hasTeacherConflict()) {
            $conflicts[] = 'Teacher has another class at this time';
        }

        if ($this->hasClassConflict()) {
            $conflicts[] = 'Class has another subject at this time';
        }

        return $conflicts;
    }

    /**
     * Get color for day (for UI visualization)
     */
    public function getDayColorAttribute(): string
    {
        $colors = [
            1 => '#4285F4', // Monday - Blue
            2 => '#34A853', // Tuesday - Green
            3 => '#FBBC04', // Wednesday - Yellow
            4 => '#EA4335', // Thursday - Red
            5 => '#9C27B0', // Friday - Purple
            6 => '#FF9800', // Saturday - Orange
            7 => '#607D8B', // Sunday - Grey
        ];

        return $colors[$this->day_of_week] ?? '#6C757D';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get all entries for a class grouped by day
     */
    public static function getWeeklyScheduleForClass($schoolId, $classId, $streamId = null): array
    {
        $query = static::forSchool($schoolId)
            ->forClass($classId)
            ->with(['subject', 'teacher', 'stream'])
            ->orderedBySchedule();

        if ($streamId) {
            $query->forStream($streamId);
        }

        $entries = $query->get();

        // Group by day
        $schedule = [];
        for ($day = 1; $day <= 7; $day++) {
            $schedule[$day] = $entries->where('day_of_week', $day)->values();
        }

        return $schedule;
    }

    /**
     * Get all entries for a teacher grouped by day
     */
    public static function getWeeklyScheduleForTeacher($schoolId, $teacherId): array
    {
        $entries = static::forSchool($schoolId)
            ->forTeacher($teacherId)
            ->with(['subject', 'class', 'stream'])
            ->orderedBySchedule()
            ->get();

        // Group by day
        $schedule = [];
        for ($day = 1; $day <= 7; $day++) {
            $schedule[$day] = $entries->where('day_of_week', $day)->values();
        }

        return $schedule;
    }

    /**
     * Get statistics for a school's timetable
     */
    public static function getStatistics($schoolId): array
    {
        $query = static::forSchool($schoolId);

        return [
            'total_entries' => $query->count(),
            'entries_per_day' => [
                1 => $query->clone()->forDay(1)->count(),
                2 => $query->clone()->forDay(2)->count(),
                3 => $query->clone()->forDay(3)->count(),
                4 => $query->clone()->forDay(4)->count(),
                5 => $query->clone()->forDay(5)->count(),
                6 => $query->clone()->forDay(6)->count(),
                7 => $query->clone()->forDay(7)->count(),
            ],
            'unique_classes' => $query->clone()->distinct('class_id')->count('class_id'),
            'unique_teachers' => $query->clone()->whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id'),
            'unique_subjects' => $query->clone()->distinct('subject_id')->count('subject_id'),
            'unassigned_teachers' => $query->clone()->whereNull('teacher_id')->count(),
        ];
    }

    /**
     * Delete all entries for a class
     */
    public static function deleteForClass($schoolId, $classId): int
    {
        return static::forSchool($schoolId)
            ->forClass($classId)
            ->delete();
    }

    /**
     * Get available rooms for a slot
     */
    public static function availableRooms($schoolId, $day, $start, $end)
    {
        $occupied = static::forSchool($schoolId)
            ->whereNotNull('room_id')
            ->forDay($day)
            ->withinTimeRange($start, $end)
            ->pluck('room_id')
            ->filter();

        return Room::where('school_id', $schoolId)
            ->where('is_active', true)
            ->whereNotIn('id', $occupied)
            ->orderBy('name')
            ->get();
    }
}
