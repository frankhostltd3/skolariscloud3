<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseQuestion extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'exercise_id',
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
        'marks' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    /**
     * Question types that can be auto-graded
     */
    public const AUTO_GRADABLE_TYPES = ['multiple_choice', 'true_false', 'short_answer', 'fill_blank'];

    /**
     * Relationships
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
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
            'matching' => 'Matching',
            default => 'Unknown'
        };
    }

    public function getIsAutoGradableAttribute(): bool
    {
        return in_array($this->type, self::AUTO_GRADABLE_TYPES);
    }

    /**
     * Check if the student's answer is correct
     */
    public function checkAnswer($studentAnswer): ?bool
    {
        if (!$this->is_auto_gradable) {
            return null; // Needs manual grading
        }

        $correctAnswer = $this->correct_answer;

        if (empty($correctAnswer)) {
            return null; // No correct answer set
        }

        switch ($this->type) {
            case 'multiple_choice':
                return $this->checkMultipleChoice($studentAnswer, $correctAnswer);

            case 'true_false':
                return $this->checkTrueFalse($studentAnswer, $correctAnswer);

            case 'short_answer':
            case 'fill_blank':
                return $this->checkShortAnswer($studentAnswer, $correctAnswer);

            default:
                return null;
        }
    }

    /**
     * Calculate score for this question based on student's answer
     */
    public function calculateScore($studentAnswer): ?float
    {
        $isCorrect = $this->checkAnswer($studentAnswer);

        if ($isCorrect === true) {
            return (float) $this->marks;
        }

        if ($isCorrect === false) {
            return 0;
        }

        return null; // Needs manual grading
    }

    /**
     * Check multiple choice answer
     */
    private function checkMultipleChoice($studentAnswer, $correctAnswer): bool
    {
        $correct = $this->normalizeAnswer($correctAnswer);
        $student = $this->normalizeAnswer($studentAnswer);

        return !empty($correct) && $correct === $student;
    }

    /**
     * Check true/false answer
     */
    private function checkTrueFalse($studentAnswer, $correctAnswer): bool
    {
        $correct = $this->normalizeBooleanValue($this->extractFirstValue($correctAnswer));
        $student = $this->normalizeBooleanValue($this->extractFirstValue($studentAnswer));

        return $correct !== null && $correct === $student;
    }

    /**
     * Check short answer / fill blank - supports multiple acceptable answers
     */
    private function checkShortAnswer($studentAnswer, $correctAnswer): bool
    {
        $student = trim(strtolower((string) $this->extractFirstValue($studentAnswer)));

        if ($student === '') {
            return false;
        }

        // Support multiple correct answers
        $acceptableAnswers = is_array($correctAnswer) ? $correctAnswer : [$correctAnswer];

        foreach ($acceptableAnswers as $acceptable) {
            $normalized = trim(strtolower((string) $acceptable));
            if ($normalized !== '' && $student === $normalized) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize answer to array for comparison
     */
    private function normalizeAnswer($value): array
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = [strtoupper(trim((string) $value))];
        } else {
            $value = array_map(fn($v) => strtoupper(trim((string) $v)), array_values($value));
        }

        return array_filter($value, fn($v) => $v !== '');
    }

    /**
     * Extract first value from various formats
     */
    private function extractFirstValue($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            return $value[0] ?? reset($value) ?: null;
        }

        return (string) $value;
    }

    /**
     * Normalize boolean-like values
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
}
