<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'education_level_id',
        'name',
        'code',
        'description',
        'category',
        'is_compulsory',
        'is_active',
        'pass_mark',
    ];

    protected $casts = [
        'is_compulsory' => 'boolean',
        'is_active' => 'boolean',
        'pass_mark' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(\App\Models\Academic\EducationLevel::class);
    }

    public function classes()
    {
        return $this->belongsToMany(\App\Models\Academic\ClassRoom::class, 'class_subjects', 'subject_id', 'class_id')
            ->withPivot('teacher_id', 'periods_per_week', 'is_active')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(\App\Models\User::class, 'class_subjects', 'subject_id', 'teacher_id')
            ->withPivot('class_id', 'periods_per_week', 'is_active')
            ->withTimestamps();
    }
}
