<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions grouped by module
        $permissions = [
            // User Management
            'users.view' => 'View users',
            'users.create' => 'Create users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
            'users.approve' => 'Approve user registrations',
            'users.suspend' => 'Suspend users',
            'users.export' => 'Export user data',

            // Role & Permission Management
            'roles.view' => 'View roles',
            'roles.create' => 'Create roles',
            'roles.edit' => 'Edit roles',
            'roles.delete' => 'Delete roles',
            'permissions.assign' => 'Assign permissions to roles',

            // Student Management
            'students.view' => 'View students',
            'students.create' => 'Create students',
            'students.edit' => 'Edit students',
            'students.delete' => 'Delete students',
            'students.enroll' => 'Enroll students',
            'students.transfer' => 'Transfer students',
            'students.graduate' => 'Graduate students',

            // Teacher Management
            'teachers.view' => 'View teachers',
            'teachers.create' => 'Create teachers',
            'teachers.edit' => 'Edit teachers',
            'teachers.delete' => 'Delete teachers',
            'teachers.assign' => 'Assign teachers to classes',

            // Class Management
            'classes.view' => 'View classes',
            'classes.create' => 'Create classes',
            'classes.edit' => 'Edit classes',
            'classes.delete' => 'Delete classes',
            'classes.assign' => 'Assign students to classes',

            // Subject Management
            'subjects.view' => 'View subjects',
            'subjects.create' => 'Create subjects',
            'subjects.edit' => 'Edit subjects',
            'subjects.delete' => 'Delete subjects',

            // Attendance
            'attendance.view' => 'View attendance',
            'attendance.mark' => 'Mark attendance',
            'attendance.edit' => 'Edit attendance',
            'attendance.report' => 'Generate attendance reports',

            // Grades & Assessments
            'grades.view' => 'View grades',
            'grades.create' => 'Create grades',
            'grades.edit' => 'Edit grades',
            'grades.delete' => 'Delete grades',
            'grades.approve' => 'Approve grades',
            'grades.report' => 'Generate grade reports',

            // Assignments
            'assignments.view' => 'View assignments',
            'assignments.create' => 'Create assignments',
            'assignments.edit' => 'Edit assignments',
            'assignments.delete' => 'Delete assignments',
            'assignments.grade' => 'Grade assignments',

            // Exams
            'exams.view' => 'View exams',
            'exams.create' => 'Create exams',
            'exams.edit' => 'Edit exams',
            'exams.delete' => 'Delete exams',
            'exams.schedule' => 'Schedule exams',

            // Timetable
            'timetable.view' => 'View timetable',
            'timetable.create' => 'Create timetable',
            'timetable.edit' => 'Edit timetable',
            'timetable.delete' => 'Delete timetable',

            // Finance & Fees
            'finance.view' => 'View financial records',
            'finance.create' => 'Create financial records',
            'finance.edit' => 'Edit financial records',
            'finance.delete' => 'Delete financial records',
            'fees.manage' => 'Manage fee structures',
            'payments.process' => 'Process payments',
            'payments.refund' => 'Process refunds',
            'invoices.generate' => 'Generate invoices',

            // Library
            'library.view' => 'View library',
            'library.manage' => 'Manage library books',
            'library.issue' => 'Issue books',
            'library.return' => 'Return books',

            // Reports
            'reports.view' => 'View reports',
            'reports.generate' => 'Generate reports',
            'reports.export' => 'Export reports',
            'reports.custom' => 'Create custom reports',

            // Settings
            'settings.view' => 'View settings',
            'settings.edit' => 'Edit settings',
            'settings.general' => 'Manage general settings',
            'settings.academic' => 'Manage academic settings',
            'settings.system' => 'Manage system settings',
            'settings.mail' => 'Manage mail settings',
            'settings.payment' => 'Manage payment settings',
            'settings.messaging' => 'Manage messaging settings',

            // Communication
            'messages.send' => 'Send messages',
            'messages.view' => 'View messages',
            'announcements.create' => 'Create announcements',
            'announcements.edit' => 'Edit announcements',
            'notifications.send' => 'Send notifications',

            // Documents
            'documents.view' => 'View documents',
            'documents.upload' => 'Upload documents',
            'documents.download' => 'Download documents',
            'documents.delete' => 'Delete documents',

            // Departments
            'departments.view' => 'View departments',
            'departments.create' => 'Create departments',
            'departments.edit' => 'Edit departments',
            'departments.delete' => 'Delete departments',

            // Positions
            'positions.view' => 'View positions',
            'positions.create' => 'Create positions',
            'positions.edit' => 'Edit positions',
            'positions.delete' => 'Delete positions',
        ];

        // Create all permissions
        foreach ($permissions as $name => $description) {
            Permission::create([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Create roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions except super admin privileges
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::whereNotIn('name', [
            'settings.system',
            'roles.delete',
            'users.delete'
        ])->get());

        // Teacher - Teaching and student management
        $teacher = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher->givePermissionTo([
            'students.view', 'students.edit',
            'classes.view', 'classes.assign',
            'subjects.view',
            'attendance.view', 'attendance.mark', 'attendance.edit',
            'grades.view', 'grades.create', 'grades.edit',
            'assignments.view', 'assignments.create', 'assignments.edit', 'assignments.grade',
            'exams.view',
            'timetable.view',
            'reports.view', 'reports.generate',
            'messages.send', 'messages.view',
            'documents.view', 'documents.upload', 'documents.download',
        ]);

        // Student - View only permissions
        $student = Role::create(['name' => 'student', 'guard_name' => 'web']);
        $student->givePermissionTo([
            'classes.view',
            'subjects.view',
            'attendance.view',
            'grades.view',
            'assignments.view',
            'exams.view',
            'timetable.view',
            'messages.view',
            'documents.view', 'documents.download',
            'library.view',
        ]);

        // Parent - View student progress
        $parent = Role::create(['name' => 'parent', 'guard_name' => 'web']);
        $parent->givePermissionTo([
            'students.view',
            'attendance.view',
            'grades.view',
            'reports.view',
            'messages.view', 'messages.send',
            'finance.view',
            'invoices.generate',
        ]);

        // Accountant - Financial management
        $accountant = Role::create(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->givePermissionTo([
            'finance.view', 'finance.create', 'finance.edit',
            'fees.manage',
            'payments.process', 'payments.refund',
            'invoices.generate',
            'reports.view', 'reports.generate', 'reports.export',
        ]);

        // Librarian - Library management
        $librarian = Role::create(['name' => 'librarian', 'guard_name' => 'web']);
        $librarian->givePermissionTo([
            'library.view', 'library.manage', 'library.issue', 'library.return',
            'students.view',
            'teachers.view',
            'reports.view', 'reports.generate',
        ]);

        // Head of Department
        $hod = Role::create(['name' => 'head-of-department', 'guard_name' => 'web']);
        $hod->givePermissionTo([
            'students.view', 'students.edit',
            'teachers.view', 'teachers.assign',
            'classes.view', 'classes.create', 'classes.edit',
            'subjects.view', 'subjects.create', 'subjects.edit',
            'attendance.view', 'attendance.report',
            'grades.view', 'grades.approve',
            'timetable.view', 'timetable.create', 'timetable.edit',
            'reports.view', 'reports.generate', 'reports.custom',
            'departments.view', 'departments.edit',
        ]);

        $this->command->info('Permissions and roles created successfully!');
        $this->command->info('Created roles: super-admin, admin, teacher, student, parent, accountant, librarian, head-of-department');
        $this->command->info('Created ' . Permission::count() . ' permissions');
    }
}
