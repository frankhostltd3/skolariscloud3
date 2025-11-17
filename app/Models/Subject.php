<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['code','name','education_level_id'];

    public function grades()
    {
        return $this->hasMany(\App\Models\Grade::class);
    }

    public function classes()
    {
        return $this->belongsToMany(\App\Models\Academic\ClassRoom::class, 'class_subjects', 'subject_id', 'class_id')
            ->withPivot(['teacher_id', 'periods_per_week', 'start_time', 'end_time', 'schedule_days', 'room_number', 'is_active', 'notes'])
            ->withTimestamps();
    }

    public function educationLevel()
    {
        return $this->belongsTo(\App\Models\EducationLevel::class, 'education_level_id');
    }

    /**
     * Get all teachers teaching this subject
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher', 'subject_id', 'teacher_id')
            ->withPivot('class_id', 'academic_year')
            ->withTimestamps();
    }

    /**
     * Get all students taking this subject
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject', 'subject_id', 'student_id')
            ->withPivot('academic_year', 'is_core', 'status')
            ->withTimestamps();
    }

    /**
     * Get all learning materials for this subject
     */
    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class, 'subject_id');
    }

    /**
     * Get all student notes for this subject
     */
    public function studentNotes()
    {
        return $this->hasMany(StudentNote::class, 'subject_id');
    }

    /**
     * Get the display name with education level indicator
     */
    public function getDisplayNameAttribute(): string
    {
        $level = $this->educationLevel;
        if ($level) {
            $levelCode = $level->code ?? '';
            if ($levelCode === 'O') {
                return '[O-Level] ' . $this->name;
            } elseif ($levelCode === 'A') {
                return '[A-Level] ' . $this->name;
            } elseif ($levelCode === 'P') {
                return '[Primary] ' . $this->name;
            } elseif ($levelCode === 'PRE') {
                return '[Pre-Primary] ' . $this->name;
            } else {
                return '[' . $level->name . '] ' . $this->name;
            }
        }
        return $this->name;
    }

    /**
     * Get the short level indicator
     */
    public function getLevelBadgeAttribute(): string
    {
        $level = $this->educationLevel;
        if ($level) {
            $levelCode = $level->code ?? '';
            $colors = [
                'O' => 'primary',
                'A' => 'success',
                'P' => 'info',
                'PRE' => 'warning',
            ];
            $color = $colors[$levelCode] ?? 'secondary';
            $displayCode = $levelCode === 'O' ? 'O-Level' : ($levelCode === 'A' ? 'A-Level' : $level->name);
            return '<span class="badge bg-' . $color . '">' . htmlspecialchars($displayCode) . '</span>';
        }
        return '';
    }
}
