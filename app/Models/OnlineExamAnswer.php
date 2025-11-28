<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineExamAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'online_exam_attempt_id',
        'online_exam_question_id',
        'answer',
        'score',
        'feedback',
    ];

    protected $casts = [
        'answer' => 'array',
        'score' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function attempt()
    {
        return $this->belongsTo(OnlineExamAttempt::class, 'online_exam_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(OnlineExamQuestion::class, 'online_exam_question_id');
    }

    /**
     * Methods
     */
    public function autoGrade()
    {
        $score = $this->question->calculateScore($this->answer);
        
        if ($score !== null) {
            $this->update(['score' => $score]);
        }

        return $score;
    }

    public function manualGrade($score, $feedback = null)
    {
        $this->update([
            'score' => $score,
            'feedback' => $feedback,
        ]);

        // Recalculate attempt score
        $this->attempt->calculateScore();
        $this->attempt->save();
    }
}
