<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    use HasFactory;

    protected $fillable = ['name','name_translations','country','code','order','min_year','max_year','grading_scheme_id'];

    protected $casts = [
        'name_translations' => 'array',
        'min_year' => 'integer',
        'max_year' => 'integer',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function gradingScheme()
    {
        return $this->belongsTo(GradingScheme::class, 'grading_scheme_id');
    }
}
