<?php

namespace App\Models\Academic;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ClassStream extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'class_id',
        'name',
        'code',
        'description',
        'capacity',
        'active_students_count',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active_students_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(\App\Models\Academic\ClassRoom::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class, 'class_stream_id');
    }

    public function getFullNameAttribute()
    {
        return $this->class->name . ' ' . $this->name;
    }

    /**
     * Scope a query to only include active streams.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive streams.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Check if the stream has available capacity.
     */
    public function hasCapacity(int $requiredSlots = 1): bool
    {
        if (!$this->capacity) {
            return true; // No capacity limit set
        }

        $available = $this->capacity - ($this->active_students_count ?? 0);
        return $available >= $requiredSlots;
    }

    /**
     * Get the percentage of capacity used.
     */
    public function getCapacityPercentageAttribute(): float
    {
        if (!$this->capacity || $this->capacity == 0) {
            return 0;
        }

        return round((($this->active_students_count ?? 0) / $this->capacity) * 100, 1);
    }

    /**
     * Get the available capacity.
     */
    public function getAvailableCapacityAttribute(): int
    {
        if (!$this->capacity) {
            return 999; // Return large number if no limit
        }

        return max(0, $this->capacity - ($this->active_students_count ?? 0));
    }

    /**
     * Recalculate and persist the number of active students in this stream.
     */
    public function updateEnrollmentCount(): void
    {
        $connection = $this->getConnectionName() ?? config('database.default');

        if (Schema::connection($connection)->hasTable('enrollments') && Schema::connection($connection)->hasColumn('enrollments', 'class_stream_id')) {
            $count = Enrollment::on($connection)
                ->where('class_stream_id', $this->id)
                ->where('status', 'active')
                ->count();
        } elseif (Schema::connection($connection)->hasTable('students')) {
            $count = Student::on($connection)
                ->where('class_stream_id', $this->id)
                ->count();
        } else {
            $count = 0;
        }

        $this->forceFill(['active_students_count' => $count])->save();
    }
}
