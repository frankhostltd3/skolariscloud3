<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'education_level_id',
        'capacity',
        'active_students_count',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active_students_count' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(\App\Models\Academic\EducationLevel::class);
    }

    public function streams()
    {
        return $this->hasMany(\App\Models\Academic\ClassStream::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(\App\Models\Student::class, 'class_id');
    }

    public function teachers()
    {
        return $this->hasMany(\App\Models\Teacher::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(\App\Models\Academic\Subject::class, 'class_subjects', 'class_id', 'subject_id');
    }
}
