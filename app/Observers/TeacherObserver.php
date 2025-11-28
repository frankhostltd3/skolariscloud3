<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Log;

class TeacherObserver
{
    /**
     * Handle the Teacher "created" event.
     * Auto-create employee record for every teacher
     *
     * @param Teacher $teacher
     * @return void
     */
    public function created(Teacher $teacher): void
    {
        // If teacher already has an employee record, skip
        if ($teacher->employee_record_id) {
            return;
        }

        try {
            // Find or create "Teaching Staff" / "Academic" department
            $department = Department::where('name', 'like', '%teaching%')
                ->orWhere('name', 'like', '%academic%')
                ->first();

            if (!$department) {
                // Create default teaching department
                $department = Department::create([
                    'name' => 'Teaching Staff',
                    'code' => 'TEA',
                    'description' => 'Academic teaching staff department',
                ]);
                
                Log::info('Teaching department auto-created', [
                    'department_id' => $department->id,
                ]);
            }

            // Find or create "Teacher" position
            $position = Position::where('title', 'like', '%teacher%')->first();

            if (!$position) {
                // Create default teacher position
                $position = Position::create([
                    'title' => 'Teacher',
                    'department_id' => $department->id,
                    'description' => 'Teaching staff position',
                ]);
                
                Log::info('Teacher position auto-created', [
                    'position_id' => $position->id,
                ]);
            }

            // Check if employee already exists with this email
            $existingEmployee = Employee::where('email', $teacher->email)->first();

            if ($existingEmployee) {
                // Link existing employee to teacher
                $existingEmployee->is_teacher = true;
                $existingEmployee->teacher_id = $teacher->id;
                $existingEmployee->saveQuietly();

                $teacher->employee_record_id = $existingEmployee->id;
                $teacher->employee_number = $existingEmployee->employee_number;
                $teacher->saveQuietly();

                Log::info('Teacher linked to existing employee', [
                    'teacher_id' => $teacher->id,
                    'employee_id' => $existingEmployee->id,
                ]);
            } else {
                // Create new employee record
                $employee = Employee::create([
                    'first_name' => $teacher->first_name ?? explode(' ', $teacher->name)[0],
                    'last_name' => $teacher->last_name ?? (explode(' ', $teacher->name)[1] ?? ''),
                    'gender' => $teacher->gender ?? 'other',
                    'birth_date' => $teacher->date_of_birth,
                    'national_id' => $teacher->national_id,
                    'phone' => $teacher->phone,
                    'email' => $teacher->email,
                    'department_id' => $department->id,
                    'position_id' => $position->id,
                    'hire_date' => $teacher->joining_date ?? now(),
                    'employee_type' => $teacher->employment_type ?? 'full_time',
                    'employment_status' => $teacher->status ?? 'active',
                    'is_teacher' => true,
                    'teacher_id' => $teacher->id,
                ]);

                // Link teacher to employee
                $teacher->employee_record_id = $employee->id;
                $teacher->employee_number = $employee->employee_number;
                $teacher->saveQuietly(); // Use saveQuietly to avoid infinite loop

                Log::info('Employee record auto-created from teacher', [
                    'teacher_id' => $teacher->id,
                    'employee_id' => $employee->id,
                    'employee_number' => $employee->employee_number,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create employee record from teacher', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the Teacher "updated" event.
     * Sync updates to linked employee record
     *
     * @param Teacher $teacher
     * @return void
     */
    public function updated(Teacher $teacher): void
    {
        if ($teacher->employee_record_id) {
            try {
                $employee = Employee::find($teacher->employee_record_id);
                
                if ($employee) {
                    $employee->updateQuietly([
                        'first_name' => $teacher->first_name ?? explode(' ', $teacher->name)[0],
                        'last_name' => $teacher->last_name ?? (explode(' ', $teacher->name)[1] ?? ''),
                        'gender' => $teacher->gender ?? $employee->gender,
                        'birth_date' => $teacher->date_of_birth ?? $employee->birth_date,
                        'national_id' => $teacher->national_id ?? $employee->national_id,
                        'phone' => $teacher->phone ?? $employee->phone,
                        'email' => $teacher->email ?? $employee->email,
                        'employment_status' => $teacher->status ?? $employee->employment_status,
                    ]);

                    Log::info('Employee record synced from teacher update', [
                        'teacher_id' => $teacher->id,
                        'employee_id' => $employee->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to sync employee from teacher update', [
                    'teacher_id' => $teacher->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle the Teacher "deleted" event.
     * Optionally unlink or delete employee record
     *
     * @param Teacher $teacher
     * @return void
     */
    public function deleted(Teacher $teacher): void
    {
        if ($teacher->employee_record_id) {
            try {
                $employee = Employee::find($teacher->employee_record_id);
                
                if ($employee) {
                    // Just unlink, don't delete employee
                    $employee->is_teacher = false;
                    $employee->teacher_id = null;
                    $employee->saveQuietly();

                    Log::info('Teacher deleted, employee unlinked', [
                        'teacher_id' => $teacher->id,
                        'employee_id' => $employee->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to unlink employee on teacher deletion', [
                    'teacher_id' => $teacher->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Teacher deleted', [
            'teacher_id' => $teacher->id,
            'name' => $teacher->name,
        ]);
    }
}
