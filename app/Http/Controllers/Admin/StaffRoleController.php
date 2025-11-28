<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Position;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class StaffRoleController extends Controller
{
    /**
     * Change a staff member's user type between teaching_staff, general_staff, etc.
     * Also syncs Teacher and Employee records accordingly.
     */
    public function changeUserType(Request $request, User $user)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        // Ensure user belongs to this school
        if ($user->school_id !== $school->id) {
            return redirect()->back()->with('error', __('User does not belong to this school.'));
        }

        $validated = $request->validate([
            'new_user_type' => ['required', 'in:teaching_staff,general_staff,admin'],
            'sync_role' => ['nullable', 'boolean'], // Also sync Spatie role
        ]);

        $newUserType = UserType::from($validated['new_user_type']);
        $oldUserType = $user->user_type;
        $syncRole = $validated['sync_role'] ?? true;

        DB::beginTransaction();
        try {
            // Update user_type
            $user->update(['user_type' => $newUserType]);

            // Sync Spatie role if requested
            if ($syncRole) {
                $roleName = match ($newUserType) {
                    UserType::TEACHING_STAFF => 'Teacher',
                    UserType::GENERAL_STAFF => 'Staff',
                    UserType::ADMIN => 'Admin',
                    default => null,
                };

                if ($roleName) {
                    // Ensure role exists in tenant context
                    $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                    $user->syncRoles([$role]);
                }
            }

            $employee = Employee::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            // Handle Teacher/Admin/Staff employee record sync
            if ($employee) {
                $this->syncEmployeeRoleAttributes($employee, $newUserType);
            }

            if ($newUserType === UserType::TEACHING_STAFF) {
                // Ensure teacher profile exists/synced whenever user_type is teacher
                $this->ensureTeacherRecord($user, $employee);
            }

            if ($oldUserType === UserType::TEACHING_STAFF && $newUserType !== UserType::TEACHING_STAFF) {
                // Switching FROM teacher: optionally deactivate or delete Teacher record
                $this->deactivateTeacherRecord($user);
            }

            DB::commit();

            Log::info('Staff user type changed', [
                'user_id' => $user->id,
                'from' => $oldUserType->value,
                'to' => $newUserType->value,
                'changed_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', __(
                'User type changed from :old to :new successfully.',
                ['old' => $oldUserType->label(), 'new' => $newUserType->label()]
            ));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to change user type', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', __('Failed to change user type: ') . $e->getMessage());
        }
    }

    /**
     * Create Teacher record if it doesn't exist for this user.
     */
    private function ensureTeacherRecord(User $user, ?Employee $employee = null): ?Teacher
    {
        $employee ??= Employee::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $existing = Teacher::where('email', $user->email)
            ->orWhere('user_id', $user->id)
            ->first();

        if ($existing) {
            // Reactivate if status is inactive
            if ($existing->status !== 'active') {
                $existing->update(['status' => 'active']);
            }
            if ($employee && ! $employee->teacher_id) {
                $employee->teacher_id = $existing->id;
                $employee->is_teacher = true;
                $employee->save();
            }

            return $existing;
        }

        [$first, $last] = $this->splitName($user->name);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'school_id' => $user->school_id,
            'name' => $user->name,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $user->email,
            'phone' => $user->phone ?? $employee?->phone,
            'employee_number' => $employee?->employee_number,
            'employee_record_id' => $employee?->id,
            'gender' => $employee?->gender,
            'date_of_birth' => $employee?->birth_date,
            'joining_date' => $employee?->hire_date ?? now(),
            'employment_type' => $employee?->employee_type ?? 'full_time',
            'status' => 'active',
        ]);

        if ($employee) {
            $employee->teacher_id = $teacher->id;
            $employee->is_teacher = true;
            $employee->save();
        }

        Log::info('Teacher record auto-created when switching user type', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $teacher;
    }

    /**
     * Deactivate Teacher record when switching away from teaching staff.
     */
    private function deactivateTeacherRecord(User $user): void
    {
        $teacher = Teacher::where('email', $user->email)
            ->orWhere('user_id', $user->id)
            ->first();

        if ($teacher) {
            // Mark as inactive instead of deleting to preserve history
            $teacher->update(['status' => 'inactive']);

            $employee = Employee::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();
            if ($employee) {
                $employee->is_teacher = false;
                $employee->teacher_id = null;
                $employee->save();
            }

            Log::info('Teacher record deactivated when switching user type', [
                'teacher_id' => $teacher->id,
                'user_id' => $user->id,
            ]);
        }
    }

    private function syncEmployeeRoleAttributes(?Employee $employee, UserType $userType): void
    {
        if (! $employee) {
            return;
        }

        $config = $this->roleSyncConfig()[$userType->value] ?? null;

        if (! $config) {
            return;
        }

        $department = $this->firstOrCreateDepartment($config['department']);
        $position = $this->firstOrCreatePosition($department, $config['position']);

        $employee->department_id = $department->id;
        $employee->position_id = $position->id;
        $employee->employee_type = $config['employee_type'];
        $employee->employment_status = $employee->employment_status ?: 'active';
        $employee->is_teacher = $config['is_teacher'];

        if (! $config['is_teacher']) {
            $employee->teacher_id = null;
        }

        $employee->save();
    }

    private function roleSyncConfig(): array
    {
        return [
            UserType::TEACHING_STAFF->value => [
                'employee_type' => 'teacher',
                'is_teacher' => true,
                'department' => [
                    'name' => 'Teaching Staff',
                    'code' => 'TEA',
                    'description' => 'Academic teaching staff department',
                ],
                'position' => [
                    'title' => 'Teacher',
                    'code' => 'TEACH',
                    'description' => 'Teaching staff position',
                ],
            ],
            UserType::GENERAL_STAFF->value => [
                'employee_type' => 'general_staff',
                'is_teacher' => false,
                'department' => [
                    'name' => 'Operations',
                    'code' => 'OPS',
                    'description' => 'Non-teaching school staff',
                ],
                'position' => [
                    'title' => 'Staff Member',
                    'code' => 'STAFF',
                    'description' => 'General staff role',
                ],
            ],
            UserType::ADMIN->value => [
                'employee_type' => 'admin',
                'is_teacher' => false,
                'department' => [
                    'name' => 'Administration',
                    'code' => 'ADM',
                    'description' => 'Administrative leadership',
                ],
                'position' => [
                    'title' => 'Administrator',
                    'code' => 'ADMIN',
                    'description' => 'School administrator role',
                ],
            ],
        ];
    }

    private function firstOrCreateDepartment(array $config): Department
    {
        $department = null;

        if (! empty($config['code'])) {
            $department = Department::where('code', $config['code'])->first();
        }

        if (! $department) {
            $department = Department::where('name', $config['name'])->first();
        }

        if (! $department) {
            $department = Department::create([
                'name' => $config['name'],
                'code' => $config['code'] ?? null,
                'description' => $config['description'] ?? null,
            ]);
        }

        return $department;
    }

    private function firstOrCreatePosition(Department $department, array $config): Position
    {
        $position = null;

        if (! empty($config['code'])) {
            $position = Position::where('code', $config['code'])->first();
        }

        if (! $position) {
            $position = Position::where('department_id', $department->id)
                ->where('title', $config['title'])
                ->first();
        }

        if (! $position) {
            $position = Position::create([
                'department_id' => $department->id,
                'title' => $config['title'],
                'code' => $config['code'] ?? null,
                'description' => $config['description'] ?? null,
            ]);
        } elseif ($position->department_id !== $department->id) {
            $position->department_id = $department->id;
            $position->save();
        }

        return $position;
    }

    /**
     * Split full name into first and last name.
     */
    private function splitName(?string $fullName): array
    {
        $fullName = trim((string) $fullName);
        if ($fullName === '') {
            return ['User', ''];
        }
        $parts = preg_split('/\s+/', $fullName);
        $first = array_shift($parts);
        $last = count($parts) ? implode(' ', $parts) : '';
        return [$first, $last];
    }
}
