<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'description',
        'instructions',
        'available_from',
        'available_until',
        'duration_minutes',
        'total_marks',
        'pass_marks',
        'max_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_results_immediately',
        'show_correct_answers',
        'allow_review',
        'status',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'duration_minutes' => 'integer',
        'total_marks' => 'integer',
        'pass_marks' => 'integer',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'allow_review' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_question')->withPivot(['points'])->withTimestamps();
    }

    public function classes(): BelongsToMany
    {
        // Older tenants created pivot column `class_id`, so define explicit keys
        return $this->belongsToMany(ClassRoom::class, 'quiz_class', 'quiz_id', 'class_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(\App\Models\Academic\QuizAttempt::class);
    }

    // Backward compatibility accessors
    public function getStartAtAttribute()
    {
        return $this->available_from;
    }

    public function getEndAtAttribute()
    {
        return $this->available_until;
    }

    public function setStartAtAttribute($value)
    {
        $this->attributes['available_from'] = $value;
    }

    public function setEndAtAttribute($value)
    {
        $this->attributes['available_until'] = $value;
    }
}
