<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'admission_no',
        'email',
        'phone',
        'gender',
        'dob',
        'national_id',
        'profile_photo',
        'blood_group',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'class_id',
        'class_stream_id',
        'roll_number',
        'section',
        'admission_date',
        'status',
        'father_name',
        'father_phone',
        'father_occupation',
        'father_email',
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'mother_email',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'guardian_email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'medical_conditions',
        'allergies',
        'medications',
        'previous_school',
        'previous_class',
        'transfer_reason',
        'notes',
        'has_special_needs',
        'special_needs_description',
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
        'has_special_needs' => 'boolean',
    ];

    /**
     * Get the class that the student belongs to
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Linked tenant user account (matched by email).
     */
    public function account(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    /**
     * Get the student's full name
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->name;
    }

    /**
     * Get the student's age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->dob ? $this->dob->age : null;
    }

    /**
     * Get the invoices for the student (via linked user account).
     */
    public function invoices()
    {
        return $this->hasManyThrough(
            \App\Models\Invoice::class,
            User::class,
            'email', // Foreign key on users table
            'student_id', // Foreign key on invoices table
            'email', // Local key on students table
            'id' // Local key on users table
        );
    }

    /**
     * Get the grades for the student (via linked user account).
     */
    public function grades()
    {
        return $this->hasManyThrough(
            \App\Models\Grade::class,
            User::class,
            'email', // Foreign key on users table
            'student_id', // Foreign key on grades table
            'email', // Local key on students table
            'id' // Local key on users table
        );
    }

    /**
     * Get the parents associated with this student.
     */
    public function parents()
    {
        return $this->belongsToMany(ParentProfile::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_primary', 'can_pickup', 'financial_responsibility')
            ->withTimestamps();
    }

    /**
     * Get the stream that the student belongs to
     */
    public function stream(): BelongsTo
    {
        return $this->belongsTo(ClassStream::class, 'class_stream_id');
    }

    /**
     * Get the subjects assigned to this student
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'student_id', 'subject_id')
            ->withPivot('academic_year', 'is_core', 'status')
            ->withTimestamps();
    }

    /**
     * Get behaviour records for this student.
     */
    public function behaviours()
    {
        return $this->hasMany(StudentBehaviour::class);
    }
}
