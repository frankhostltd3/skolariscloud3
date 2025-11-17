<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'pass_marks',
        'exam_date',
        'start_time',
        'end_time',
        'status',
        'proctored',
        'max_tab_switches',
        'disable_copy_paste',
        'shuffle_questions',
        'shuffle_answers',
        'show_results_immediately',
        'grading_method',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'proctored' => 'boolean',
        'disable_copy_paste' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_results_immediately' => 'boolean',
    ];

    protected $appends = ['status_label', 'is_available', 'attempts_count'];

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

    public function sections()
    {
        return $this->hasMany(OnlineExamSection::class)->orderBy('order');
    }

    public function questions()
    {
        return $this->hasManyThrough(OnlineExamQuestion::class, OnlineExamSection::class);
    }

    public function attempts()
    {
        return $this->hasMany(OnlineExamAttempt::class);
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getIsAvailableAttribute()
    {
        if ($this->status !== 'active' && $this->status !== 'scheduled') {
            return false;
        }

        $now = now();

        if ($this->start_time && $now->isBefore($this->start_time)) {
            return false;
        }

        if ($this->end_time && $now->isAfter($this->end_time)) {
            return false;
        }

        return true;
    }

    public function getAttemptsCountAttribute()
    {
        return $this->attempts()->count();
    }

    /**
     * Methods
     */
    public function publish()
    {
        $this->update(['status' => 'scheduled']);
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function getAttemptByStudent($studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->first();
    }

    public function hasAttempted($studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->exists();
    }

    public function canAttempt($studentId)
    {
        if (!$this->is_available) {
            return false;
        }

        // Check if student already attempted
        if ($this->hasAttempted($studentId)) {
            return false;
        }

        return true;
    }

    public function getTotalQuestions()
    {
        return $this->questions()->count();
    }

    public function calculateTotalMarks()
    {
        return $this->questions()->sum('marks');
    }

    public function getAverageScore()
    {
        return $this->attempts()
            ->whereNotNull('score')
            ->avg('score') ?? 0;
    }

    public function getPassRate()
    {
        $totalAttempts = $this->attempts()->whereNotNull('score')->count();
        
        if ($totalAttempts === 0) {
            return 0;
        }

        $passedAttempts = $this->attempts()
            ->whereNotNull('score')
            ->where('score', '>=', $this->pass_marks)
            ->count();

        return round(($passedAttempts / $totalAttempts) * 100, 2);
    }

    public function getPendingGradingCount()
    {
        return $this->attempts()
            ->whereNotNull('submitted_at')
            ->whereNull('score')
            ->count();
    }

    public function getViolationsCount()
    {
        return $this->attempts()->sum('tab_switches_count');
    }
}
