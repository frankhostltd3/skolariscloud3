<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'score_auto',
        'score_manual',
        'score_total',
        'minutes_late',
        'status',
        'answers',
        'feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score_auto' => 'decimal:2',
        'score_manual' => 'decimal:2',
        'score_total' => 'decimal:2',
        'minutes_late' => 'integer',
        'answers' => 'array',
    ];

    /**
     * Get the school that owns the attempt.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the quiz this attempt belongs to.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
/**
 * Student quiz attempts for the teacher quiz module shared with the student portal.
 */

    /**
     * Get the student who made this attempt.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Computed score accessor for legacy views expecting `score` column.
     */
    public function getScoreAttribute()
    {
        if (array_key_exists('score', $this->attributes) && $this->attributes['score'] !== null) {
            return $this->attributes['score'];
        }

        return $this->score_total ?? $this->score_auto ?? null;
    }

    /**
     * Scope a query to only include attempts for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include late submissions.
     */
    public function scopeLate($query)
    {
        return $query->where('minutes_late', '>', 0);
    }

    /**
     * Scope a query to only include submitted attempts.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('submitted_at', [$from, $to]);
    }

    /**
     * Scope a query to filter by student name or ID.
     */
    public function scopeStudentSearch($query, $search)
    {
        return $query->whereHas('student', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    /**
     * Auto-grade objective questions using QuizQuestion records.
     */
    public function autoGrade(): int
    {
        $quiz = $this->quiz()->with('questions')->first();
        if (!$quiz) {
            return 0;
        }

        $answers = $this->answers ?? [];
        $score = 0;

        foreach ($quiz->questions as $question) {
            $questionPoints = (int) ($question->marks ?? 1);
            $response = $answers[$question->id] ?? null;
            $isCorrect = null;

            // Get the correct answer - handle both array cast and raw string
            $correctRaw = $question->getRawOriginal('correct_answer') ?? $question->correct_answer;

            switch ($question->type) {
                case 'multiple_choice':
                    // Normalize both to comparable format (trimmed, uppercase string)
                    $correctValue = $this->extractFirstValue($correctRaw);
                    $studentValue = $this->extractFirstValue($response);
                    $isCorrect = $correctValue !== null && $studentValue !== null
                        && strtoupper(trim($correctValue)) === strtoupper(trim($studentValue));
                    break;
                case 'true_false':
                    $correctValue = $this->extractFirstValue($correctRaw);
                    $studentValue = $this->extractFirstValue($response);
                    // Normalize boolean-like values
                    $correctBool = $this->normalizeBooleanValue($correctValue);
                    $studentBool = $this->normalizeBooleanValue($studentValue);
                    $isCorrect = $correctBool !== null && $correctBool === $studentBool;
                    break;
                case 'short_answer':
                    $correctValue = $this->extractFirstValue($correctRaw);
                    $studentValue = $this->extractFirstValue($response);
                    $expected = trim(strtolower((string) $correctValue));
                    $given = trim(strtolower((string) $studentValue));
                    $isCorrect = $expected !== '' && $given !== '' && $given === $expected;
                    break;
                default:
                    $isCorrect = null; // essay/manual grading
            }

            if ($isCorrect === true) {
                $score += $questionPoints;
            }
        }

        $this->score_auto = $score;
        $this->score_total = $score + (float) ($this->score_manual ?? 0);
        $this->save();

        return $score;
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
            // Try numeric keys first (indexed array)
            if (isset($value[0])) {
                return $value[0];
            }
            // Otherwise get first value from associative array
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

    private function normalizeAnswer($value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return array_values(array_filter($value, fn($v) => $v !== null && $v !== ''));
    }
}
