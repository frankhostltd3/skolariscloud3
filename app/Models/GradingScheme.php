<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'country', 'examination_body_id', 'is_current', 'meta',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'meta' => 'array',
    ];

    public function examinationBody()
    {
        return $this->belongsTo(ExaminationBody::class);
    }

    public function bands()
    {
        return $this->hasMany(GradingBand::class)->orderBy('order');
    }
}
