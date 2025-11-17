<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quiz_id',
        'type',
        'question_text',
        'options',
        'correct_answer',
        'marks',
        'order',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
    ];

    protected $appends = ['type_label'];

    /**
     * Relationships
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Accessors
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'short_answer' => 'Short Answer',
            'essay' => 'Essay',
            default => 'Unknown'
        };
    }

    /**
     * Methods
     */
    public function checkAnswer($studentAnswer)
    {
        if ($this->type === 'multiple_choice' || $this->type === 'true_false') {
            return $studentAnswer === $this->correct_answer;
        }

        // For short answers and essays, manual grading is needed
        return null;
    }

    public function calculateScore($studentAnswer)
    {
        if ($this->checkAnswer($studentAnswer) === true) {
            return $this->marks;
        }

        if ($this->checkAnswer($studentAnswer) === false) {
            return 0;
        }

        // Return null for questions that need manual grading
        return null;
    }
}
