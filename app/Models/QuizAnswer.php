<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        'answer',
        'score',
        'is_correct',
    ];

    protected $casts = [
        'answer' => 'array',
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    /**
     * Methods
     */
    public function autoGrade()
    {
        $score = $this->question->calculateScore($this->answer);
        
        if ($score !== null) {
            $this->update([
                'score' => $score,
                'is_correct' => $score === $this->question->marks,
            ]);
        }

        return $score;
    }
}
