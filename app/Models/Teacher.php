<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\TeacherObserver;

#[ObservedBy([TeacherObserver::class])]
class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'employee_number',
        'employee_record_id',
        'gender',
        'date_of_birth',
        'national_id',
        'profile_photo',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'employee_id',
        'qualification',
        'specialization',
        'experience_years',
        'joining_date',
        'employment_type',
        'status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'blood_group',
        'medical_conditions',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'experience_years' => 'integer',
    ];

    /**
     * Get the classes where this teacher is the class teacher
     */
    public function classesAsClassTeacher(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'class_teacher_id');
    }

    /**
     * Get all lesson plans created by this teacher
     */
    public function lessonPlans(): HasMany
    {
        return $this->hasMany(LessonPlan::class, 'teacher_id');
    }

    /**
     * Get all quizzes created by this teacher
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'teacher_id');
    }

    /**
     * Get all discussions created by this teacher
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class, 'teacher_id');
    }

    /**
     * Get all virtual classes hosted by this teacher
     */
    public function virtualClasses(): HasMany
    {
        return $this->hasMany(VirtualClass::class, 'teacher_id');
    }

    /**
     * Get the linked employee record
     */
    public function employeeRecord(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_record_id');
    }

    /**
     * Get the classes this teacher teaches
     */
    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_teacher', 'teacher_id', 'class_id')
            ->withPivot('academic_year', 'is_class_teacher')
            ->withTimestamps();
    }

    /**
     * Get the subjects this teacher teaches
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
            ->withPivot('class_id', 'academic_year')
            ->withTimestamps();
    }

    /**
     * Get the streams this teacher is assigned to
     */
    public function streams()
    {
        return $this->belongsToMany(\App\Models\Academic\ClassStream::class, 'class_stream_teacher', 'teacher_id', 'class_stream_id')
            ->withPivot('academic_year')
            ->withTimestamps();
    }

    /**
     * Get classes where this teacher is the main class teacher (via classes table)
     */
    public function classesAsMainTeacher(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'class_teacher_id');
    }
}
