<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'semester_id',
        'teacher_id',
        'assessment_type',
        'assessment_name',
        'marks_obtained',
        'total_marks',
        'grade_letter',
        'grade_point',
        'assessment_date',
        'remarks',
        'is_published',
        'entered_by',
        'published_at',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'assessment_date' => 'date',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\ClassRoom::class, 'class_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Term::class, 'semester_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUnpublished($query)
    {
        return $query->where('is_published', false);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopeByAssessmentType($query, $type)
    {
        return $query->where('assessment_type', $type);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Accessors & Mutators
    public function getPercentageAttribute()
    {
        if ($this->total_marks > 0) {
            return round(($this->marks_obtained / $this->total_marks) * 100, 2);
        }
        return 0;
    }

    public function getStatusAttribute()
    {
        $passPercentage = $this->subject->pass_percentage ?? 40;
        return $this->percentage >= $passPercentage ? 'pass' : 'fail';
    }

    public function getStatusBadgeClassAttribute()
    {
        return $this->status === 'pass' ? 'bg-success' : 'bg-danger';
    }

    public function getAssessmentTypeDisplayAttribute()
    {
        return match ($this->assessment_type) {
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'project' => 'Project',
            'presentation' => 'Presentation',
            'lab' => 'Lab Work',
            'homework' => 'Homework',
            default => ucfirst($this->assessment_type)
        };
    }

    // Helper methods
    public function isPassed(): bool
    {
        return $this->status === 'pass';
    }

    public function isFailed(): bool
    {
        return $this->status === 'fail';
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function publish(): void
    {
        $this->is_published = true;
        $this->published_at = now();
        $this->save();
    }

    public function unpublish(): void
    {
        $this->is_published = false;
        $this->published_at = null;
        $this->save();
    }

    public function calculateGradeLetter(): string
    {
        $percentage = $this->percentage;

        return match (true) {
            $percentage >= 97 => 'A+',
            $percentage >= 93 => 'A',
            $percentage >= 90 => 'A-',
            $percentage >= 87 => 'B+',
            $percentage >= 83 => 'B',
            $percentage >= 80 => 'B-',
            $percentage >= 77 => 'C+',
            $percentage >= 73 => 'C',
            $percentage >= 70 => 'C-',
            $percentage >= 67 => 'D+',
            $percentage >= 65 => 'D',
            default => 'F'
        };
    }

    public function calculateGradePoint(): float
    {
        $percentage = $this->percentage;

        return match (true) {
            $percentage >= 97 => 4.0,
            $percentage >= 93 => 4.0,
            $percentage >= 90 => 3.7,
            $percentage >= 87 => 3.3,
            $percentage >= 83 => 3.0,
            $percentage >= 80 => 2.7,
            $percentage >= 77 => 2.3,
            $percentage >= 73 => 2.0,
            $percentage >= 70 => 1.7,
            $percentage >= 67 => 1.3,
            $percentage >= 65 => 1.0,
            default => 0.0
        };
    }

    public function autoCalculateGrade(): void
    {
        $this->grade_letter = $this->calculateGradeLetter();
        $this->grade_point = $this->calculateGradePoint();
        $this->save();
    }

    // Static methods
    public static function getAssessmentTypes(): array
    {
        return [
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            'midterm' => 'Midterm Exam',
            'final' => 'Final Exam',
            'project' => 'Project',
            'presentation' => 'Presentation',
            'lab' => 'Lab Work',
            'homework' => 'Homework',
        ];
    }

    public static function calculateClassAverage($classId, $subjectId, $semesterId = null): float
    {
        $query = static::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('is_published', true);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        // Prefer SQL aggregation if columns exist; otherwise fall back to PHP calculation
        if (self::hasMarksColumns() || self::hasPercentageColumn()) {
            $row = $query->selectRaw(self::percentageAvgExpression() . ' AS avg_pct')->first();
            return $row && $row->avg_pct !== null ? (float) $row->avg_pct : 0.0;
        }

        // Fallback: compute in memory using accessor
        $grades = $query->get();
        if ($grades->isEmpty()) return 0.0;
        $avg = $grades->avg(fn($g) => (float) $g->percentage);
        return round((float) $avg, 2);
    }

    /**
     * Determine if marks_obtained and total_marks columns exist.
     */
    public static function hasMarksColumns(): bool
    {
        return Schema::hasColumn('grades', 'marks_obtained') && Schema::hasColumn('grades', 'total_marks');
    }

    /**
     * Determine if computed percentage column exists.
     */
    public static function hasPercentageColumn(): bool
    {
        return Schema::hasColumn('grades', 'percentage');
    }

    /**
     * Get a SQL expression that yields the percentage value for a single row.
     * Returns a CASE expression using marks columns when available, otherwise the percentage column.
     */
    public static function percentageValueExpression(): string
    {
        if (self::hasMarksColumns()) {
            return '(CASE WHEN grades.total_marks > 0 THEN (grades.marks_obtained / grades.total_marks) * 100 ELSE 0 END)';
        }
        if (self::hasPercentageColumn()) {
            return 'grades.percentage';
        }
        // As a last resort, 0 to avoid SQL errors; callers may compute in PHP instead.
        return '0';
    }

    /**
     * Get a SQL AVG(expression) for percentage across rows.
     */
    public static function percentageAvgExpression(): string
    {
        return 'AVG(' . self::percentageValueExpression() . ')';
    }

    public static function calculateStudentGPA($studentId, $semesterId = null): float
    {
        $query = static::where('student_id', $studentId)
            ->where('is_published', true);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return $query->avg('grade_point') ?? 0;
    }
}
