<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'quiz_id',
        'type',
        'question',
        'options',
        'correct_answer',
        'marks',
        'order',
        'explanation',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answer' => 'array',
        'is_required' => 'boolean',
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
        if ($this->type === 'multiple_choice') {
            // Get raw correct answer (before array cast)
            $correctRaw = $this->getRawOriginal('correct_answer') ?? $this->correct_answer;
            $correctValue = $this->extractFirstValue($correctRaw);
            $studentValue = $this->extractFirstValue($studentAnswer);
            
            return $correctValue !== null && $studentValue !== null 
                && strtoupper(trim((string) $correctValue)) === strtoupper(trim((string) $studentValue));
        }

        if ($this->type === 'true_false') {
            $correctRaw = $this->getRawOriginal('correct_answer') ?? $this->correct_answer;
            $correctValue = $this->extractFirstValue($correctRaw);
            $studentValue = $this->extractFirstValue($studentAnswer);
            
            // Normalize both to boolean
            $correctBool = $this->normalizeBooleanValue($correctValue);
            $studentBool = $this->normalizeBooleanValue($studentValue);
            
            return $correctBool !== null && $correctBool === $studentBool;
        }

        // For short answers and essays, manual grading is needed
        return null;
    }

    /**
     * Extract the first/primary value from various input formats.
     */
    private function extractFirstValue($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // If it's a string that looks like JSON, decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        // If it's an array, get the first non-empty value
        if (is_array($value)) {
            if (isset($value[0])) {
                return $value[0];
            }
            $firstValue = reset($value);
            return $firstValue !== false ? $firstValue : null;
        }

        return (string) $value;
    }

    /**
     * Normalize boolean-like values for true/false comparison.
     */
    private function normalizeBooleanValue($value): ?bool
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));
        
        if (in_array($value, ['true', '1', 'yes', 't'], true)) {
            return true;
        }
        
        if (in_array($value, ['false', '0', 'no', 'f'], true)) {
            return false;
        }

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
