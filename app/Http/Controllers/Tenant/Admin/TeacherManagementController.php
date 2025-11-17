<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\ClassRoom;
use App\Models\ClassStream;
use App\Models\Academic\Enrollment;
use App\Models\Employee;
use App\Models\Student as StudentProfile;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\TeacherAssignedToClass;
use App\Notifications\TeacherAssignedSubjects;
use App\Notifications\StudentEnrolledToClass;

class TeacherManagementController extends Controller
{
    /**
     * Register a staff User as a Teacher and link to Employee if available.
     */
    public function register(Request $request, User $user)
    {
        $this->authorize('manage academics');

        // Ensure role assignment
        try { $user->assignRole('Teacher'); } catch (\Throwable $e) { /* role may already be assigned */ }

        // If teacher already exists, return early
        $existing = Teacher::where('email', $user->email)
            ->orWhere('employee_record_id', $user->employee_record_id ?? null)
            ->first();
        if ($existing) {
            return back()->with('info', __('User is already registered as a teacher.'));
        }

        // Link to Employee record if present
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();

        [$first, $last] = $this->splitName($user->name);

        Teacher::create([
            'name' => $user->name,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $user->email,
            'phone' => $user->phone,
            'employee_number' => $employee?->employee_number,
            'employee_record_id' => $employee?->id,
            'gender' => $employee?->gender,
            'joining_date' => $employee?->hire_date,
            'employment_type' => $employee?->employee_type,
            'status' => 'active',
        ]);

        // Mark employee as teacher for sync
        if ($employee) {
            $employee->update(['is_teacher' => true]);
        }

        return back()->with('success', __('Teacher registered successfully.'));
    }

    /**
     * Assign a class to be managed by the teacher (class teacher).
     */
    public function assignClass(Request $request, User $teacherUser)
    {
        $this->authorize('manage academics');
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $class = ClassRoom::findOrFail($data['class_id']);
        $class->update(['class_teacher_id' => $teacherUser->id]);

        // Notify teacher
        try {
            $teacherUser->notify(new TeacherAssignedToClass($class->name));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify teacher about class assignment', [
                'user_id' => $teacherUser->id,
                'class_id' => $class->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', __('Class assigned to teacher.'));
    }

    /**
     * Assign subjects to a teacher for a given class (uses class_subjects pivot teacher_id as User ID).
     */
    public function assignSubjects(Request $request, User $teacherUser)
    {
        $this->authorize('manage academics');
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $class = ClassRoom::findOrFail($data['class_id']);

        // Ensure subjects are attached to class and update teacher on pivot
        foreach ($data['subject_ids'] as $subjectId) {
            $exists = $class->subjects()->where('subjects.id', $subjectId)->exists();
            if (!$exists) {
                $class->subjects()->attach($subjectId, ['teacher_id' => $teacherUser->id, 'is_active' => true]);
            } else {
                $class->subjects()->updateExistingPivot($subjectId, ['teacher_id' => $teacherUser->id, 'is_active' => true]);
            }
        }

        // Notify teacher with subject list
        try {
            $subjectNames = Subject::whereIn('id', $data['subject_ids'])->orderBy('name')->pluck('name')->all();
            $teacherUser->notify(new TeacherAssignedSubjects($class->name, $subjectNames));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify teacher about subject assignments', [
                'user_id' => $teacherUser->id,
                'class_id' => $class->id,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', __('Subjects assigned to teacher for this class.'));
    }

    /**
     * Enroll a student User into a class and optional stream; updates Student profile too.
     */
    public function enrollStudent(Request $request, User $studentUser)
    {
        $this->authorize('manage academics');
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'class_stream_id' => ['nullable', 'exists:class_streams,id'],
            'status' => ['nullable', 'in:active,dropped,transferred,completed'],
            'enrollment_date' => ['nullable', 'date'],
        ]);

        $status = $data['status'] ?? 'active';

        DB::transaction(function () use ($studentUser, $data, $status) {
            // Create or update Student profile
            $studentProfile = StudentProfile::firstOrCreate(
                ['email' => $studentUser->email],
                [
                    'name' => $studentUser->name,
                    'first_name' => explode(' ', $studentUser->name)[0] ?? $studentUser->name,
                    'last_name' => trim(str_replace(explode(' ', $studentUser->name)[0] ?? '', '', $studentUser->name)),
                    'status' => 'active',
                ]
            );

            $studentProfile->class_id = $data['class_id'];
            $studentProfile->class_stream_id = $data['class_stream_id'] ?? null;
            $studentProfile->save();

            // Create enrollment (Users are referenced in enrollments table)
            Enrollment::create([
                'student_id' => $studentUser->id,
                'class_id' => $data['class_id'],
                'enrollment_date' => $data['enrollment_date'] ?? now(),
                'status' => $status,
                'enrolled_by' => auth()->id(),
                'fees_paid' => 0,
                'fees_total' => 0,
            ]);
        });

        // Notify student
        try {
            $class = ClassRoom::find($data['class_id']);
            $stream = isset($data['class_stream_id']) && $data['class_stream_id'] ? ClassStream::find($data['class_stream_id']) : null;
            $studentUser->notify(new StudentEnrolledToClass($class?->name ?? __('your class'), $stream?->name, $status));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify student about enrollment', [
                'user_id' => $studentUser->id,
                'class_id' => $data['class_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', __('Student enrolled to class successfully.'));
    }

    private function splitName(?string $fullName): array
    {
        $fullName = trim((string) $fullName);
        if ($fullName === '') { return ['User', '']; }
        $parts = preg_split('/\s+/', $fullName);
        $first = array_shift($parts);
        $last = count($parts) ? implode(' ', $parts) : '';
        return [$first, $last];
    }
}
