<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineExamQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'online_exam_section_id',
        'type',
        'question_text',
        'options',
        'correct_answer',
        'marks',
        'order',
        'attachments',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'attachments' => 'array',
    ];

    protected $appends = ['type_label'];

    /**
     * Relationships
     */
    public function section()
    {
        return $this->belongsTo(OnlineExamSection::class, 'online_exam_section_id');
    }

    public function answers()
    {
        return $this->hasMany(OnlineExamAnswer::class);
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
            'fill_blank' => 'Fill in the Blank',
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

        // For other types, manual grading is needed
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

    public function needsManualGrading()
    {
        return in_array($this->type, ['short_answer', 'essay', 'fill_blank']);
    }
}
