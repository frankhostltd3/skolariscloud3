<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Academic\Enrollment;
use App\Models\Academic\EducationLevel;

class SchoolClass extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'grade_level',
        'stream',
        'capacity',
        'teacher_id',
        'room_number',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns the class.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the teacher assigned to this class.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the education level for this class.
     */
    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Get all quizzes for this class.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'class_id');
    }

    /**
     * Get the students enrolled in this class.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            Enrollment::class,
            'class_id', // Foreign key on enrollments table...
            'id', // Foreign key on students table...
            'id', // Local key on classes table...
            'student_id' // Local key on enrollments table...
        );
    }

    /**
     * Scope a query to only include classes for a specific school.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope a query to only include active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
