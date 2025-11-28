<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\ClassStream;

class Enrollment extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'student_id',
        'class_id',
        'class_stream_id',
        'academic_year_id',
        'semester_id',
        'enrollment_date',
        'status',
        'fees_paid',
        'fees_total',
        'notes',
        'enrolled_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'fees_paid' => 'decimal:2',
        'fees_total' => 'decimal:2',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(ClassStream::class, 'class_stream_id');
    }

    // Alias to match usage in dashboards (classroom instead of class)
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    // Alias to match legacy naming expectations (schoolClass)
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Term::class, 'semester_id');
    }

    public function enrolledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeForSemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors & Mutators
    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            'active' => 'Active',
            'dropped' => 'Dropped',
            'transferred' => 'Transferred',
            'completed' => 'Completed',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'dropped' => 'bg-danger',
            'transferred' => 'bg-warning',
            'completed' => 'bg-primary',
            default => 'bg-secondary'
        };
    }

    public function getFeesBalanceAttribute()
    {
        return $this->fees_total - $this->fees_paid;
    }

    public function getFeesPaymentPercentageAttribute()
    {
        if ($this->fees_total > 0) {
            return round(($this->fees_paid / $this->fees_total) * 100, 2);
        }
        return 0;
    }

    public function getIsFeesCompleteAttribute()
    {
        return $this->fees_paid >= $this->fees_total;
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDropped(): bool
    {
        return $this->status === 'dropped';
    }

    public function isTransferred(): bool
    {
        return $this->status === 'transferred';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsDropped(string $reason = null): void
    {
        $this->status = 'dropped';
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Dropped: " . $reason;
        }
        $this->save();

        // Update class enrollment count
        $this->class->updateEnrollmentCount();
    }

    public function markAsTransferred(string $reason = null): void
    {
        $this->status = 'transferred';
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Transferred: " . $reason;
        }
        $this->save();

        // Update class enrollment count
        $this->class->updateEnrollmentCount();
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->save();

        // Update class enrollment count
        $this->class->updateEnrollmentCount();
    }

    public function addFeesPayment(float $amount, string $note = null): void
    {
        $this->fees_paid += $amount;
        if ($note) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Payment: $" . number_format($amount, 2) . " - " . $note;
        }
        $this->save();
    }
}