<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\EmployeeObserver;

#[ObservedBy([EmployeeObserver::class])]
class Employee extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'employee_number',
        'employee_type',
        'national_id',
        'gender',
        'department_id',
        'position_id',
        'salary_scale_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'birth_date',
        'employment_status',
        'photo_path',
        'user_id',
        'metadata',
        'is_teacher',
        'teacher_id',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Relationships
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function salaryScale()
    {
        return $this->belongsTo(SalaryScale::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(\App\Models\Teacher::class, 'teacher_id');
    }

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByEmployeeType($query, $type)
    {
        return $query->where('employee_type', $type);
    }
}
