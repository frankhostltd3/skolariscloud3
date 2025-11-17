<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingBand extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_scheme_id', 'code', 'label', 'min_score', 'max_score', 'order', 'awards',
    ];

    protected $casts = [
        'awards' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($band) {
            // Ensure min_score is not greater than max_score
            if ($band->min_score > $band->max_score) {
                throw new \InvalidArgumentException('Minimum score cannot be greater than maximum score.');
            }

            // Ensure scores are non-negative
            if ($band->min_score < 0 || $band->max_score < 0) {
                throw new \InvalidArgumentException('Scores cannot be negative.');
            }
        });
    }
}
