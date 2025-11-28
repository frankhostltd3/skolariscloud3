<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Enrollment as ClassEnrollment;
use App\Models\ClassStream;
use App\Models\Subject;
use App\Models\Department;
use App\Models\Position;
use App\Notifications\UserApprovedNotification;
use App\Notifications\UserRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserApprovalController extends Controller
{
    /**
     * Display user registrations (pending, approved, or rejected)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = User::where('approval_status', $status)
            ->orderBy('created_at', 'desc');

        // Filter by role if specified
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->appends($request->query());

        // Count by status
        $counts = [
            'pending' => User::where('approval_status', 'pending')->count(),
            'approved' => User::where('approval_status', 'approved')->count(),
            'rejected' => User::where('approval_status', 'rejected')->count(),
        ];

        // For approved list, prepare quick-manage datasets (classes, streams, subjects)
        $classes = collect();
        $streams = collect();
        $departments = collect();
        $positions = collect();
        $subjects = collect();
        $subjectsByLevel = collect();
        if ($status === 'approved') {
            try {
                $classes = ClassRoom::orderBy('name')->get(['id','name','code']);
            } catch (\Throwable $e) {
                // Ignore if academics not migrated yet for a tenant
                $classes = collect();
            }
            try {
                $streams = ClassStream::orderBy('name')->get(['id','name','class_id']);
            } catch (\Throwable $e) {
                $streams = collect();
            }
            try {
                $departments = Department::orderBy('name')->get(['id','name','code']);
            } catch (\Throwable $e) {
                $departments = collect();
            }
            try {
                $positions = Position::with(['department:id,name'])
                    ->orderBy('title')
                    ->get(['id','title','department_id','code']);
            } catch (\Throwable $e) {
                $positions = collect();
            }
            try {
                $subjects = Subject::with(['educationLevel:id,name,code'])
                    ->orderBy('name')
                    ->get(['id','name','code','education_level_id']);
                $subjectsByLevel = $subjects->groupBy(function ($subject) {
                    $level = $subject->educationLevel;
                    if ($level) {
                        return $level->name ?? __('Uncategorized');
                    }
                    return __('Uncategorized');
                });
            } catch (\Throwable $e) {
                $subjects = collect();
                $subjectsByLevel = collect();
            }
        }

        return view('admin.user-approvals.index', compact('users', 'counts', 'status', 'classes', 'streams', 'departments', 'positions', 'subjects', 'subjectsByLevel'));
    }

    /**
     * Show details of a user registration
     */
    public function show(User $user)
    {
        // Allow viewing any user with approval status
        if (!in_array($user->approval_status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.user-approvals.index')
                ->with('error', 'This user has already been processed.');
        }

        return view('admin.user-approvals.show', compact('user'));
    }

    /**
     * Approve a user registration
     */
    public function approve(Request $request, User $user)
    {
        if ($user->approval_status !== 'pending') {
            return back()->with('error', 'This user has already been processed.');
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'email_verified_at' => $user->email_verified_at ?? now(), // Auto-verify email on approval
            ]);

            // Run tenant-specific logic on the tenant database
            if (app()->bound('currentSchool')) {
                // We are already in tenant context
                if ($user->hasRole('Student') || $user->hasUserType(\App\Enums\UserType::STUDENT)) {
                    $this->createStudentRecord($user);
                }
                if ($user->hasRole('Staff') || $user->hasRole('Teacher') || $user->hasUserType(\App\Enums\UserType::TEACHING_STAFF) || $user->hasUserType(\App\Enums\UserType::GENERAL_STAFF)) {
                    $this->createEmployeeRecord($user);
                }
            } elseif ($user->school_id) {
                $this->runOnTenant($user->school_id, function () use ($user) {
                    // Auto-create Student record if user has Student role or type
                    if ($user->hasRole('Student') || $user->hasUserType(\App\Enums\UserType::STUDENT)) {
                        $this->createStudentRecord($user);
                    }

                    // Auto-create Employee record if user has Staff role or type
                    if ($user->hasRole('Staff') || $user->hasRole('Teacher') || $user->hasUserType(\App\Enums\UserType::TEACHING_STAFF) || $user->hasUserType(\App\Enums\UserType::GENERAL_STAFF)) {
                        $this->createEmployeeRecord($user);
                    }
                });
            }

            // Send approval notification
            try {
                $user->notify(new UserApprovedNotification());
            } catch (\Throwable $e) {
                Log::warning('Failed to send UserApprovedNotification', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return redirect()->route('admin.user-approvals.index')
            ->with('success', "User {$user->name} has been approved successfully.");
    }

    /**
     * Reject a user registration
     */
    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($user->approval_status !== 'pending') {
            return back()->with('error', 'This user has already been processed.');
        }

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Send rejection notification
            try {
                $user->notify(new UserRejectedNotification($validated['rejection_reason']));
            } catch (\Throwable $e) {
                Log::warning('Failed to send UserRejectedNotification', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return redirect()->route('admin.user-approvals.index')
            ->with('success', "User {$user->name} has been rejected.");
    }

    /**
     * Bulk approve users
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $approvedCount = 0;

        DB::transaction(function () use ($validated, &$approvedCount) {
            $users = User::whereIn('id', $validated['user_ids'])
                ->where('approval_status', 'pending')
                ->get();

            foreach ($users as $user) {
                $user->update([
                    'approval_status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);

                if ($user->school_id) {
                    $this->runOnTenant($user->school_id, function () use ($user) {
                        // Auto-create Student record if user has Student role
                        if ($user->hasRole('Student') || $user->hasUserType(\App\Enums\UserType::STUDENT)) {
                            $this->createStudentRecord($user);
                        }

                        // Auto-create Employee record if user has Staff role
                        if ($user->hasRole('Staff') || $user->hasRole('Teacher') || $user->hasUserType(\App\Enums\UserType::TEACHING_STAFF) || $user->hasUserType(\App\Enums\UserType::GENERAL_STAFF)) {
                            $this->createEmployeeRecord($user);
                        }
                    });
                }

                // Send approval notification
                try {
                    $user->notify(new UserApprovedNotification());
                } catch (\Throwable $e) {
                    Log::warning('Failed to send UserApprovedNotification (bulk)', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
                $approvedCount++;
            }
        });

        return redirect()->route('admin.user-approvals.index')
            ->with('success', "{$approvedCount} user(s) have been approved successfully.");
    }

    /**
     * Bulk reject users
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $rejectedCount = 0;

        DB::transaction(function () use ($validated, &$rejectedCount) {
            $users = User::whereIn('id', $validated['user_ids'])
                ->where('approval_status', 'pending')
                ->get();

            foreach ($users as $user) {
                $user->update([
                    'approval_status' => 'rejected',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'rejection_reason' => $validated['rejection_reason'],
                ]);

                // Send rejection notification
                try {
                    $user->notify(new UserRejectedNotification($validated['rejection_reason']));
                } catch (\Throwable $e) {
                    Log::warning('Failed to send UserRejectedNotification (bulk)', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
                $rejectedCount++;
            }
        });

        return redirect()->route('admin.user-approvals.index')
            ->with('success', "{$rejectedCount} user(s) have been rejected.");
    }

    /**
     * Update employment category for an approved user and optionally allocate teacher to class/subjects.
     */
    public function updateEmployment(Request $request, User $user)
    {
        $validated = $request->validate([
            'employment_role' => 'required|in:Teacher,Bursar,Nurse,Staff,Other',
            'employee_type' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            // Optional quick allocations if set to Teacher
            'class_id' => 'nullable|exists:classes,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        if ($user->approval_status !== 'approved') {
            return back()->with('error', __('Only approved users can be managed.'));
        }

        DB::transaction(function () use ($user, $validated) {
            if (app()->bound('currentSchool')) {
                // Ensure Employee record exists
                $employee = Employee::where('user_id', $user->id)->first();
                if (!$employee) {
                    $this->createEmployeeRecord($user);
                    $employee = Employee::where('user_id', $user->id)->first();
                }

                // Normalize employee_type
                $normalizedType = strtolower($validated['employee_type'] ?? $validated['employment_role']);

                // Update base employment fields
                if ($employee) {
                    // If employment role is Teacher, normalize to allowed teacher employment types
                    $employeeTypeForSave = $validated['employment_role'] === 'Teacher'
                        ? $this->normalizeTeacherEmploymentType($normalizedType)
                        : $normalizedType;

                    $employee->update([
                        'employee_type' => $employeeTypeForSave,
                        'department_id' => $validated['department_id'] ?? $employee->department_id,
                        'position_id' => $validated['position_id'] ?? $employee->position_id,
                    ]);
                }

                // Role assignment + teacher linkage
                if ($validated['employment_role'] === 'Teacher') {
                    try {
                        $user->assignRole('Teacher');
                    } catch (\Throwable $e) { /* ignore */
                    }

                    // Flag employee as teacher and ensure Teacher profile exists
                    if ($employee && !$employee->is_teacher) {
                        $employee->is_teacher = true;
                        $employee->save();
                    }

                    // Ensure Teacher profile
                    $teacher = Teacher::where('email', $user->email)->first();
                    if (!$teacher) {
                        [$first, $last] = $this->splitName($user->name);
                        $teacher = Teacher::create([
                            'name' => $user->name,
                            'first_name' => $first,
                            'last_name' => $last,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'employee_number' => $employee?->employee_number,
                            'employee_record_id' => $employee?->id,
                            'gender' => $employee?->gender,
                            'joining_date' => $employee?->hire_date,
                            // Teachers.employment_type is enum; normalize to allowed set
                            'employment_type' => $this->normalizeTeacherEmploymentType($employee?->employee_type),
                            'status' => 'active',
                        ]);
                    }
                    if ($employee && !$employee->teacher_id && $teacher) {
                        $employee->teacher_id = $teacher->id;
                        $employee->save();
                    }

                    // Optional allocations
                    if (!empty($validated['class_id'])) {
                        // First, remove this teacher from any other classes (unique constraint)
                        try {
                            ClassRoom::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                        } catch (\Throwable $e) {
                            // Ignore if classes table doesn't exist yet
                        }

                        // Now assign teacher to the selected class
                        $class = ClassRoom::find($validated['class_id']);
                        if ($class) {
                            $class->update(['class_teacher_id' => $user->id]);
                        }
                    }
                    if (!empty($validated['class_id']) && !empty($validated['subject_ids'])) {
                        $class = ClassRoom::find($validated['class_id']);
                        if ($class) {
                            foreach ($validated['subject_ids'] as $sid) {
                                $exists = $class->subjects()->where('subjects.id', $sid)->exists();
                                if (!$exists) {
                                    $class->subjects()->attach($sid, ['teacher_id' => $user->id]);
                                } else {
                                    $class->subjects()->updateExistingPivot($sid, ['teacher_id' => $user->id]);
                                }
                            }
                        }
                    }
                } else {
                    // If changing away from Teacher, unflag
                    if ($employee && $employee->is_teacher) {
                        $employee->is_teacher = false;
                        $employee->save();
                    }
                }
            } elseif ($user->school_id) {
                $this->runOnTenant($user->school_id, function () use ($user, $validated) {
                    // Ensure Employee record exists
                    $employee = Employee::where('user_id', $user->id)->first();
                    if (!$employee) {
                        $this->createEmployeeRecord($user);
                        $employee = Employee::where('user_id', $user->id)->first();
                    }

                    // Normalize employee_type
                    $normalizedType = strtolower($validated['employee_type'] ?? $validated['employment_role']);

                    // Update base employment fields
                    if ($employee) {
                        // If employment role is Teacher, normalize to allowed teacher employment types
                        $employeeTypeForSave = $validated['employment_role'] === 'Teacher'
                            ? $this->normalizeTeacherEmploymentType($normalizedType)
                            : $normalizedType;

                        $employee->update([
                            'employee_type' => $employeeTypeForSave,
                            'department_id' => $validated['department_id'] ?? $employee->department_id,
                            'position_id' => $validated['position_id'] ?? $employee->position_id,
                        ]);
                    }

                    // Role assignment + teacher linkage
                    if ($validated['employment_role'] === 'Teacher') {
                        try {
                            $user->assignRole('Teacher');
                        } catch (\Throwable $e) { /* ignore */
                        }

                        // Flag employee as teacher and ensure Teacher profile exists
                        if ($employee && !$employee->is_teacher) {
                            $employee->is_teacher = true;
                            $employee->save();
                        }

                        // Ensure Teacher profile
                        $teacher = Teacher::where('email', $user->email)->first();
                        if (!$teacher) {
                            [$first, $last] = $this->splitName($user->name);
                            $teacher = Teacher::create([
                                'name' => $user->name,
                                'first_name' => $first,
                                'last_name' => $last,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'employee_number' => $employee?->employee_number,
                                'employee_record_id' => $employee?->id,
                                'gender' => $employee?->gender,
                                'joining_date' => $employee?->hire_date,
                                // Teachers.employment_type is enum; normalize to allowed set
                                'employment_type' => $this->normalizeTeacherEmploymentType($employee?->employee_type),
                                'status' => 'active',
                            ]);
                        }
                        if ($employee && !$employee->teacher_id && $teacher) {
                            $employee->teacher_id = $teacher->id;
                            $employee->save();
                        }

                        // Optional allocations
                        if (!empty($validated['class_id'])) {
                            // First, remove this teacher from any other classes (unique constraint)
                            try {
                                ClassRoom::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                            } catch (\Throwable $e) {
                                // Ignore if classes table doesn't exist yet
                            }

                            // Now assign teacher to the selected class
                            $class = ClassRoom::find($validated['class_id']);
                            if ($class) {
                                $class->update(['class_teacher_id' => $user->id]);
                            }
                        }
                        if (!empty($validated['class_id']) && !empty($validated['subject_ids'])) {
                            $class = ClassRoom::find($validated['class_id']);
                            if ($class) {
                                foreach ($validated['subject_ids'] as $sid) {
                                    $exists = $class->subjects()->where('subjects.id', $sid)->exists();
                                    if (!$exists) {
                                        $class->subjects()->attach($sid, ['teacher_id' => $user->id, 'is_active' => true]);
                                    } else {
                                        $class->subjects()->updateExistingPivot($sid, ['teacher_id' => $user->id, 'is_active' => true]);
                                    }
                                }
                            }
                        }
                    } else {
                        // If changing away from Teacher, unflag
                        if ($employee && $employee->is_teacher) {
                            $employee->is_teacher = false;
                            $employee->save();
                        }
                    }
                });
            }
        });

        return back()->with('success', __('Employment details updated.'));
    }

    /**
     * Update student enrollment (class, stream, subjects) for an approved student user.
     */
    public function updateStudentEnrollment(Request $request, User $user)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_stream_id' => 'nullable|exists:class_streams,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        if ($user->approval_status !== 'approved') {
            return back()->with('error', __('Only approved users can be managed.'));
        }

        if (!$user->hasRole('Student')) {
            return back()->with('error', __('This action is only for students.'));
        }

        DB::transaction(function () use ($user, $validated) {
            if (app()->bound('currentSchool')) {
                // Ensure Student record exists
                $student = \App\Models\Student::where('email', $user->email)->first();
                if (!$student) {
                    $this->createStudentRecord($user);
                    $student = \App\Models\Student::where('email', $user->email)->first();
                }

                // Update class and stream
                if ($student) {
                    $previousClassId = $student->class_id;

                    $student->update([
                        'class_id' => $validated['class_id'],
                        'class_stream_id' => $validated['class_stream_id'] ?? null,
                    ]);

                    // Sync subjects if provided
                    if (!empty($validated['subject_ids'])) {
                        // Clear existing and add new
                        $student->subjects()->detach();
                        foreach ($validated['subject_ids'] as $subjectId) {
                            $student->subjects()->attach($subjectId, [
                                'is_core' => true,
                                'status' => 'active',
                            ]);
                        }
                    }

                    // Ensure enrollment record exists for this class and academic year
                    $academicYearId = AcademicYear::current()->value('id');
                    if (!$academicYearId) {
                        $existingEnrollmentYear = ClassEnrollment::where('student_id', $user->id)
                            ->latest('enrollment_date')
                            ->value('academic_year_id');
                        $academicYearId = $existingEnrollmentYear ?? AcademicYear::orderByDesc('start_date')->value('id');
                    }

                    if ($academicYearId) {
                        ClassEnrollment::updateOrCreate(
                            [
                                'student_id' => $user->id,
                                'class_id' => $validated['class_id'],
                                'academic_year_id' => $academicYearId,
                            ],
                            [
                                'class_stream_id' => $validated['class_stream_id'] ?? null,
                                'enrollment_date' => now(),
                                'status' => 'active',
                                'enrolled_by' => Auth::id(),
                            ]
                        );

                        // Mark other enrollments for the student in this academic year as transferred
                        ClassEnrollment::where('student_id', $user->id)
                            ->where('academic_year_id', $academicYearId)
                            ->where('class_id', '!=', $validated['class_id'])
                            ->update(['status' => 'transferred']);
                    }

                    // Refresh enrollment counts for affected classes
                    if (!empty($previousClassId) && (int)$previousClassId !== (int)$validated['class_id']) {
                        ClassRoom::find($previousClassId)?->updateEnrollmentCount();
                    }
                    ClassRoom::find($validated['class_id'])?->updateEnrollmentCount();
                }
            } elseif ($user->school_id) {
                $this->runOnTenant($user->school_id, function () use ($user, $validated) {
                    // Ensure Student record exists
                    $student = \App\Models\Student::where('email', $user->email)->first();
                    if (!$student) {
                        $this->createStudentRecord($user);
                        $student = \App\Models\Student::where('email', $user->email)->first();
                    }

                    // Update class and stream
                    if ($student) {
                        $previousClassId = $student->class_id;

                        $student->update([
                            'class_id' => $validated['class_id'],
                            'class_stream_id' => $validated['class_stream_id'] ?? null,
                        ]);

                        // Sync subjects if provided
                        if (!empty($validated['subject_ids'])) {
                            // Clear existing and add new
                            $student->subjects()->detach();
                            foreach ($validated['subject_ids'] as $subjectId) {
                                $student->subjects()->attach($subjectId, [
                                    'is_core' => true,
                                    'status' => 'active',
                                ]);
                            }
                        }

                        // Ensure enrollment record exists for this class and academic year
                        $academicYearId = AcademicYear::current()->value('id');
                        if (!$academicYearId) {
                            $existingEnrollmentYear = ClassEnrollment::where('student_id', $user->id)
                                ->latest('enrollment_date')
                                ->value('academic_year_id');
                            $academicYearId = $existingEnrollmentYear ?? AcademicYear::orderByDesc('start_date')->value('id');
                        }

                        if ($academicYearId) {
                            ClassEnrollment::updateOrCreate(
                                [
                                    'student_id' => $user->id,
                                    'class_id' => $validated['class_id'],
                                    'academic_year_id' => $academicYearId,
                                ],
                                [
                                    'class_stream_id' => $validated['class_stream_id'] ?? null,
                                    'enrollment_date' => now(),
                                    'status' => 'active',
                                    'enrolled_by' => Auth::id(),
                                ]
                            );

                            // Mark other enrollments for the student in this academic year as transferred
                            ClassEnrollment::where('student_id', $user->id)
                                ->where('academic_year_id', $academicYearId)
                                ->where('class_id', '!=', $validated['class_id'])
                                ->update(['status' => 'transferred']);
                        }

                        // Refresh enrollment counts for affected classes
                        if (!empty($previousClassId) && (int)$previousClassId !== (int)$validated['class_id']) {
                            ClassRoom::find($previousClassId)?->updateEnrollmentCount();
                        }
                        ClassRoom::find($validated['class_id'])?->updateEnrollmentCount();
                    }
                });
            }
        });

        return back()->with('success', __('Student enrollment updated.'));
    }

    /**
     * Manually re-sync the student profile and enrollment data for a user.
     */
    public function syncStudentProfile(Request $request, User $user)
    {
        if (! $user->hasRole('Student') && ! $user->hasUserType(\App\Enums\UserType::STUDENT)) {
            return back()->with('error', __('Only student accounts can be synchronized.'));
        }

        if (app()->bound('currentSchool')) {
            $this->createStudentRecord($user);
        } elseif ($user->school_id) {
            $this->runOnTenant($user->school_id, function () use ($user) {
                $this->createStudentRecord($user);
            });
        }

        return back()->with('success', __('Student profile and enrollment have been synchronized.'));
    }

    /** Suspend a user and set employee status. */
    public function suspend(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->deactivate($validated['reason'] ?? 'suspended');
            if ($user->school_id) {
                $this->runOnTenant($user->school_id, function () use ($user) {
                    if ($emp = Employee::where('user_id', $user->id)->first()) {
                        $emp->update(['employment_status' => 'suspended']);
                    }
                });
            }
        });

        return back()->with('success', __('User suspended.'));
    }

    /** Reinstate a suspended user. */
    public function reinstate(Request $request, User $user)
    {
        DB::transaction(function () use ($user) {
            $user->activate();
            if ($user->school_id) {
                $this->runOnTenant($user->school_id, function () use ($user) {
                    if ($emp = Employee::where('user_id', $user->id)->first()) {
                        $emp->update(['employment_status' => 'active']);
                    }
                });
            }
        });

        return back()->with('success', __('User reinstated.'));
    }

    /** Expel/terminate a user: deactivate and clear teaching allocations. */
    public function expel(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($user, $validated) {
            // Deactivate account
            $user->deactivate($validated['reason'] ?? 'expelled');

            if ($user->school_id) {
                $this->runOnTenant($user->school_id, function () use ($user) {
                    // Update employee status
                    if ($emp = Employee::where('user_id', $user->id)->first()) {
                        $emp->update(['employment_status' => 'terminated', 'is_teacher' => false]);
                    }

                    // Remove class teacher assignments
                    try {
                        ClassRoom::where('class_teacher_id', $user->id)->update(['class_teacher_id' => null]);
                    } catch (\Throwable $e) {
                        // ignore
                    }

                    // Clear subject allocations where pivot teacher_id = user
                    try {
                        DB::table('class_subjects')->where('teacher_id', $user->id)->update(['teacher_id' => null, 'is_active' => false]);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                });
            }
        });

        return back()->with('success', __('User expelled and allocations cleared.'));
    }

    /**
     * Create Student record when approving a user with Student role
     */
    private function createStudentRecord(User $user): void
    {
        $registrationData = $user->registration_data ?? [];
        $profile = $registrationData['student_profile'] ?? $registrationData;

        [$fallbackFirst, $fallbackLast] = $this->splitName($profile['name'] ?? $user->name);

        $student = Student::firstOrNew(['email' => $user->email]);

        $student->first_name = $profile['first_name'] ?? $student->first_name ?? $fallbackFirst;
        $student->last_name = $profile['last_name'] ?? $student->last_name ?? $fallbackLast;
        $student->name = trim(($student->first_name ? $student->first_name . ' ' : '') . ($student->last_name ?? '')) ?: ($profile['name'] ?? $user->name);
        $student->admission_no = $student->admission_no
            ?? $profile['admission_no']
            ?? $profile['student_id']
            ?? 'STU-' . now()->format('Ymd') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $student->phone = $profile['phone'] ?? $student->phone ?? $user->phone ?? '';
        $student->status = $profile['status'] ?? $student->status ?? 'active';
        $student->gender = $profile['gender'] ?? $student->gender;
        $student->dob = $profile['dob'] ?? $profile['date_of_birth'] ?? $student->dob;
        $student->admission_date = $profile['admission_date'] ?? $student->admission_date ?? now();
        $student->class_id = $profile['class_id'] ?? $student->class_id;
        $student->class_stream_id = $profile['class_stream_id'] ?? $student->class_stream_id;
        $student->address = $profile['address'] ?? $student->address ?? '';
        $student->guardian_name = $profile['guardian_name'] ?? $student->guardian_name ?? '';
        $student->guardian_phone = $profile['guardian_phone'] ?? $student->guardian_phone ?? '';
        $student->guardian_email = $profile['guardian_email'] ?? $student->guardian_email ?? '';
        $student->save();

        $classId = $student->class_id ?? $profile['class_id'] ?? null;
        $streamId = $student->class_stream_id ?? $profile['class_stream_id'] ?? null;
        $academicYearId = $profile['academic_year_id']
            ?? AcademicYear::current()->value('id')
            ?? AcademicYear::orderByDesc('start_date')->value('id');

        if ($classId && $academicYearId) {
            ClassEnrollment::updateOrCreate(
                [
                    'student_id' => $user->id,
                    'class_id' => $classId,
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'class_stream_id' => $streamId,
                    'enrollment_date' => $profile['admission_date'] ?? now(),
                    'status' => 'active',
                    'notes' => $profile['enrollment_note'] ?? __('Auto-synced after approval.'),
                    'enrolled_by' => Auth::id(),
                ]
            );

            ClassRoom::find($classId)?->updateEnrollmentCount();
            if ($streamId) {
                ClassStream::find($streamId)?->updateEnrollmentCount();
            }
        }

        // Force update all enrollments for this student to active and refresh counters
        $enrollments = ClassEnrollment::where('student_id', $user->id)->get();
        foreach ($enrollments as $enrollment) {
            if ($enrollment->status !== 'active') {
                $enrollment->update(['status' => 'active']);
            }
            $enrollment->class?->updateEnrollmentCount();
            if ($enrollment->class_stream_id) {
                ClassStream::find($enrollment->class_stream_id)?->updateEnrollmentCount();
            }
        }
    }

    /**
     * Create Employee record when approving a user with Staff/Teacher role
     */
    private function createEmployeeRecord(User $user)
    {
        $registrationData = $user->registration_data ?? [];

        // Avoid duplicates by user_id or email
        if (\App\Models\Employee::where('user_id', $user->id)->orWhere('email', $user->email)->exists()) {
            return;
        }

        // Determine names
        $firstName = $registrationData['first_name'] ?? null;
        $lastName = $registrationData['last_name'] ?? null;
        if (!$firstName || !$lastName) {
            [$firstName, $lastName] = $this->splitName($user->name);
        }

        // Generate employee number if not provided
        $employeeNumber = $registrationData['employee_number']
            ?? $registrationData['employee_id']
            ?? 'EMP-' . now()->format('Ymd') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        \App\Models\Employee::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email,
            'phone' => $user->phone ?? ($registrationData['phone'] ?? null),
            'employee_number' => $employeeNumber,
            'employee_type' => $registrationData['employee_type'] ?? 'full_time',
            'hire_date' => now(),
            'birth_date' => $registrationData['dob'] ?? null,
            'employment_status' => 'active',
            'gender' => $registrationData['gender'] ?? null,
            'department_id' => $registrationData['department_id'] ?? null,
            'position_id' => $registrationData['position_id'] ?? null,
            'user_id' => $user->id,
            'is_teacher' => $user->hasRole('Teacher'),
        ]);
    }

    /**
     * Split a full name into first and last names with safe defaults.
     */
    private function splitName(?string $fullName): array
    {
        $fullName = trim((string) $fullName);
        if ($fullName === '') {
            return ['User', ''];
        }
        $parts = preg_split('/\s+/', $fullName);
        if (!$parts || count($parts) === 0) {
            return [$fullName, ''];
        }
        $first = array_shift($parts);
        $last = count($parts) ? implode(' ', $parts) : '';
        return [$first, $last];
    }

    /**
     * Normalize teacher employment type to allowed enum values.
     * Allowed: full_time, part_time, contract, visiting
     */
    private function normalizeTeacherEmploymentType(?string $value): string
    {
        $val = strtolower(trim((string) $value));
        if ($val === '') {
            return 'full_time';
        }
        // map some common aliases
        $map = [
            'full time' => 'full_time',
            'fulltime' => 'full_time',
            'permanent' => 'full_time',
            'teacher' => 'full_time',
            'part time' => 'part_time',
            'parttime' => 'part_time',
            'pt' => 'part_time',
            'contractor' => 'contract',
            'contractual' => 'contract',
            'visiting lecturer' => 'visiting',
        ];
        if (isset($map[$val])) {
            return $map[$val];
        }
        // if already in allowed set, return as-is
        if (in_array($val, ['full_time','part_time','contract','visiting'], true)) {
            return $val;
        }
        // default fallback
        return 'full_time';
    }

    /**
     * Execute a callback within the context of a tenant database connection.
     */
    protected function runOnTenant($schoolId, callable $callback)
    {
        if (!$schoolId) {
            return $callback();
        }

        $school = \App\Models\School::find($schoolId);
        if (!$school) {
            return $callback();
        }

        $originalDefault = config('database.default');

        try {
            // Configure tenant connection
            config(['database.connections.tenant.database' => $school->database]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Switch default connection to tenant for the duration of this operation
            // This ensures models without explicit connection use the tenant DB
            config(['database.default' => 'tenant']);

            return $callback();
        } finally {
            // Restore default connection
            config(['database.default' => $originalDefault]);
        }
    }
}

