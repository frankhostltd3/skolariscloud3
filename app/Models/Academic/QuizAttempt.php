<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'score_auto',
        'score_manual',
        'score_total',
        'answers',
        'is_late'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score_auto' => 'integer',
        'score_manual' => 'integer',
        'score_total' => 'integer',
        'answers' => 'array',
        'is_late' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    /**
     * Auto-grade objective questions (mcq, true_false, short_answer keyword contains)
     * Updates score_auto and score_total; does not change score_manual.
     */
    public function autoGrade(): int
    {
        $quiz = $this->quiz()->with(['questions' => function ($q) {
            $q->select('questions.*');
        }])->first();
        if (!$quiz) return 0;
        $answers = $this->answers ?? [];
        $score = 0;
        $total = 0;
        foreach ($quiz->questions as $q) {
            $points = (int)($q->pivot->points ?? $q->points ?? 1);
            $total += $points;
            $ans = $answers[$q->id] ?? null;
            $key = $q->answer_key ?? [];
            $correct = false;
            switch ($q->type) {
                case 'mcq':
                    // answer_key.correct is index or array of indexes
                    $correctIndexes = is_array($key['correct'] ?? null) ? $key['correct'] : [$key['correct'] ?? null];
                    $ansArr = is_array($ans) ? $ans : [$ans];
                    sort($correctIndexes);
                    sort($ansArr);
                    $correct = ($ansArr == $correctIndexes);
                    break;
                case 'true_false':
                    $correct = ((bool)($key['true'] ?? false)) === ((bool)$ans);
                    break;
                case 'short_answer':
                    $needle = trim(strtolower((string)($key['contains'] ?? '')));
                    $hay = trim(strtolower((string)$ans));
                    $correct = $needle !== '' && str_contains($hay, $needle);
                    break;
            }
            if ($correct) {
                $score += $points;
            }
        }
        $this->score_auto = $score;
        $this->score_total = $score + (int)($this->score_manual ?? 0);
        $this->save();
        return $score;
    }
}