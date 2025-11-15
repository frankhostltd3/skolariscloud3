<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Department;
use App\Models\Position;
use App\Models\Academic\ClassRoom;
use App\Models\ClassStream;
use App\Models\Subject;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserApprovalsController extends Controller
{
    /**
     * Display a listing of user registrations.
     */
    public function index(Request $request)
    {
    $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;
        $status = $request->get('status', 'pending');

        // Get filter parameters
        $search = $request->get('search');
        $role = $request->get('role');

        // Base query
        $query = User::where('school_id', $school->id)
            ->where('approval_status', $status);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Get users with pagination
        $users = $query->with(['roles', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get counts for status tabs
        $counts = [
            'pending' => User::where('school_id', $school->id)->where('approval_status', 'pending')->count(),
            'approved' => User::where('school_id', $school->id)->where('approval_status', 'approved')->count(),
            'rejected' => User::where('school_id', $school->id)->where('approval_status', 'rejected')->count(),
        ];

        // Get data for dropdowns in modals
        $departments = Department::orderBy('name')->get();
        $positions = Position::with('department')->orderBy('title')->get();
        $classes = ClassRoom::where('school_id', $school->id)->orderBy('name')->get();
        $streams = ClassStream::whereIn('class_id', $classes->pluck('id'))->orderBy('name')->get();
        $subjects = Subject::where('school_id', $school->id)->with('educationLevel')->orderBy('name')->get();

        // Group subjects by education level for better organization
        $subjectsByLevel = $subjects->groupBy(function ($subject) {
            return $subject->educationLevel?->name ?? 'Other';
        });

        return view('admin.user-approvals.index', compact(
            'users',
            'status',
            'counts',
            'departments',
            'positions',
            'classes',
            'streams',
            'subjects',
            'subjectsByLevel'
        ));
    }

    /**
     * Display the specified user registration.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'approver']);

        return view('admin.user-approvals.show', compact('user'));
    }

    /**
     * Approve a user registration.
     */
    public function approve(Request $request, User $user)
    {
        $user->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'is_active' => true,
        ]);

        // TODO: Send approval notification email to user

        return redirect()
            ->route('admin.user-approvals.index', ['status' => 'pending'])
            ->with('success', __('User registration approved successfully.'));
    }

    /**
     * Reject a user registration.
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'is_active' => false,
        ]);

        // TODO: Send rejection notification email to user

        return redirect()
            ->route('admin.user-approvals.index', ['status' => 'pending'])
            ->with('success', __('User registration rejected.'));
    }

    /**
     * Bulk approve user registrations.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

    $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        User::whereIn('id', $request->user_ids)
            ->where('school_id', $school->id)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'is_active' => true,
            ]);

        return redirect()
            ->route('admin.user-approvals.index', ['status' => 'pending'])
            ->with('success', __('Selected users approved successfully.'));
    }

    /**
     * Bulk reject user registrations.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'rejection_reason' => 'required|string|max:1000',
        ]);

    $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        User::whereIn('id', $request->user_ids)
            ->where('school_id', $school->id)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'is_active' => false,
            ]);

        return redirect()
            ->route('admin.user-approvals.index', ['status' => 'pending'])
            ->with('success', __('Selected users rejected.'));
    }

    /**
     * Update employment information for a user.
     */
    public function updateEmployment(Request $request, User $user)
    {
        $request->validate([
            'employment_role' => 'required|string|in:Teacher,Bursar,Nurse,Staff,Other',
            'employee_type' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'class_id' => 'nullable|exists:classes,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        DB::beginTransaction();
        try {
            // Update user role if changed
            $currentRole = $user->roles->first()?->name;
            if ($currentRole !== $request->employment_role) {
                $user->syncRoles([$request->employment_role]);
            }

            // Update or create employee record
            $employee = Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $user->school_id,
                    'employee_type' => $request->employee_type,
                    'department_id' => $request->department_id,
                    'position_id' => $request->position_id,
                ]
            );

            // If role is Teacher, update teacher-specific data
            if ($request->employment_role === 'Teacher') {
                $teacher = Teacher::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'user_id' => $user->id,
                        'school_id' => $user->school_id,
                        'name' => $user->name,
                        'employment_type' => $request->employee_type,
                        'class_id' => $request->class_id,
                    ]
                );

                // Sync subjects if provided
                if ($request->subject_ids) {
                    $teacher->subjects()->sync($request->subject_ids);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.user-approvals.index', ['status' => 'approved'])
                ->with('success', __('Employment information updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', __('Failed to update employment information: ') . $e->getMessage());
        }
    }

    /**
     * Update student enrollment information.
     */
    public function updateStudentEnrollment(Request $request, User $user)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_stream_id' => 'nullable|exists:class_streams,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        DB::beginTransaction();
        try {
            // Update or create student record
            $student = Student::updateOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'school_id' => $user->school_id,
                    'name' => $user->name,
                    'class_id' => $request->class_id,
                    'class_stream_id' => $request->class_stream_id,
                ]
            );

            // Sync subjects if provided
            if ($request->subject_ids) {
                $student->subjects()->sync($request->subject_ids);
            }

            DB::commit();

            return redirect()
                ->route('admin.user-approvals.index', ['status' => 'approved'])
                ->with('success', __('Student enrollment updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', __('Failed to update student enrollment: ') . $e->getMessage());
        }
    }

    /**
     * Suspend an approved user.
     */
    public function suspend(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'is_active' => false,
            'suspension_reason' => $request->reason,
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with('success', __('User suspended successfully.'));
    }

    /**
     * Reinstate a suspended user.
     */
    public function reinstate(User $user)
    {
        $user->update([
            'is_active' => true,
            'suspension_reason' => null,
            'suspended_at' => null,
            'suspended_by' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', __('User reinstated successfully.'));
    }

    /**
     * Expel or terminate a user.
     */
    public function expel(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // If teacher, clear teaching allocations
            if ($user->hasRole('Teacher')) {
                $teacher = Teacher::where('user_id', $user->id)->first();
                if ($teacher) {
                    $teacher->subjects()->detach();
                    $teacher->update(['class_id' => null]);
                }
            }

            // If student, clear enrollments
            if ($user->hasRole('Student')) {
                $student = Student::where('user_id', $user->id)->first();
                if ($student) {
                    $student->subjects()->detach();
                }
            }

            $user->update([
                'is_active' => false,
                'expelled_at' => now(),
                'expulsion_reason' => $request->reason,
                'expelled_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', __('User expelled/terminated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', __('Failed to expel/terminate user: ') . $e->getMessage());
        }
    }
}
