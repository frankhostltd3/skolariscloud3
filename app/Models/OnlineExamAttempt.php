<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineExamAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'online_exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'score',
        'total_marks',
        'percentage',
        'passed',
        'time_taken_minutes',
        'tab_switches_count',
        'violation_logs',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'passed' => 'boolean',
        'score' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'violation_logs' => 'array',
    ];

    protected $appends = ['status_label', 'is_completed', 'has_violations'];

    /**
     * Relationships
     */
    public function exam()
    {
        return $this->belongsTo(OnlineExam::class, 'online_exam_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(OnlineExamAnswer::class);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        if ($this->submitted_at) {
            if ($this->score !== null) {
                return $this->passed ? 'Passed' : 'Failed';
            }
            return 'Pending Grading';
        }
        return 'In Progress';
    }

    public function getIsCompletedAttribute()
    {
        return $this->submitted_at !== null;
    }

    public function getHasViolationsAttribute()
    {
        return $this->tab_switches_count > 0 || !empty($this->violation_logs);
    }

    /**
     * Methods
     */
    public function submit()
    {
        $this->submitted_at = now();
        $this->time_taken_minutes = now()->diffInMinutes($this->started_at);
        
        // Calculate score
        $this->calculateScore();
        
        $this->save();
    }

    public function calculateScore()
    {
        $totalScore = 0;
        $totalMarks = 0;
        $needsGrading = false;

        foreach ($this->answers as $answer) {
            $question = $answer->question;
            $totalMarks += $question->marks;

            if ($answer->score !== null) {
                $totalScore += $answer->score;
            } else {
                // Auto-grade if possible
                $autoScore = $question->calculateScore($answer->answer);
                if ($autoScore !== null) {
                    $answer->update(['score' => $autoScore]);
                    $totalScore += $autoScore;
                } else {
                    $needsGrading = true;
                }
            }
        }

        // Only set final score if all questions are graded
        if (!$needsGrading) {
            $this->score = $totalScore;
            $this->total_marks = $totalMarks;
            $this->percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;
            $this->passed = $this->score >= $this->exam->pass_marks;
        }
    }

    public function recordTabSwitch()
    {
        $this->increment('tab_switches_count');
        
        $violations = $this->violation_logs ?? [];
        $violations[] = [
            'type' => 'tab_switch',
            'timestamp' => now()->toDateTimeString(),
        ];
        
        $this->update(['violation_logs' => $violations]);
    }

    public function recordViolation($type, $details = null)
    {
        $violations = $this->violation_logs ?? [];
        $violations[] = [
            'type' => $type,
            'details' => $details,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        $this->update(['violation_logs' => $violations]);
    }

    public function isTimedOut()
    {
        if (!$this->exam->duration_minutes) {
            return false;
        }

        $elapsedMinutes = now()->diffInMinutes($this->started_at);
        return $elapsedMinutes > $this->exam->duration_minutes;
    }

    public function autoSubmitIfExpired()
    {
        if ($this->isTimedOut() && !$this->is_completed) {
            $this->submit();
            return true;
        }
        return false;
    }
}
