<?php

use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Settings\AcademicSettingsController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\GeneralSettingsController;
use App\Http\Controllers\Settings\MailSettingsController;
use App\Http\Controllers\Settings\MessagingSettingsController;
use App\Http\Controllers\Settings\PaymentSettingsController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\SystemSettingsController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\Tenant\Admin\LessonPlanReviewController;
use Illuminate\Support\Facades\Route;

// Two-Factor Authentication Routes
Route::get('/security/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('/security/two-factor', [TwoFactorController::class, 'store'])->name('two-factor.store');
Route::post('/security/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
Route::delete('/security/two-factor', [TwoFactorController::class, 'destroy'])->name('two-factor.destroy');
Route::get('/security/two-factor/qr-code', [TwoFactorController::class, 'qrCode'])->name('two-factor.qr-code');
Route::get('/security/two-factor/recovery-codes', [TwoFactorController::class, 'recoveryCodes'])->name('two-factor.recovery-codes');
Route::post('/security/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes.regenerate');

// Permissions, Settings, & Access Control Routes
Route::prefix('settings/admin')->name('tenant.settings.admin.')->group(function () {
    // General Settings
    Route::get('/general', [GeneralSettingsController::class, 'edit'])->name('general');
    Route::put('/general', [GeneralSettingsController::class, 'update'])->name('general.update');
    Route::post('/general/clear-cache', [GeneralSettingsController::class, 'clearCache'])->name('general.clear-cache');

    // Academic Settings
    Route::get('/academic', [AcademicSettingsController::class, 'edit'])->name('academic');
    Route::put('/academic', [AcademicSettingsController::class, 'update'])->name('academic.update');
    Route::post('/academic/clear-cache', [AcademicSettingsController::class, 'clearCache'])->name('academic.clear-cache');

    // System Settings
    Route::get('/system', [SystemSettingsController::class, 'edit'])->name('system');
    Route::put('/system', [SystemSettingsController::class, 'update'])->name('system.update');
    Route::post('/system/clear-cache', [SystemSettingsController::class, 'clearCache'])->name('system.clear-cache');

    // Mail Settings
    Route::get('/mail', [MailSettingsController::class, 'edit'])->name('mail');
    Route::put('/mail', [MailSettingsController::class, 'update'])->name('mail.update');

    // Finance (Payment) Settings
    Route::get('/finance', [PaymentSettingsController::class, 'edit'])->name('finance');
    Route::put('/finance', [PaymentSettingsController::class, 'update'])->name('finance.update');

    // Messaging Settings
    Route::get('/messaging', [MessagingSettingsController::class, 'edit'])->name('messaging');
    Route::put('/messaging', [MessagingSettingsController::class, 'update'])->name('messaging.update');

    // Currencies
    Route::resource('currencies', CurrencyController::class)->except(['show']);
    Route::post('currencies/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('currencies.set-default');
    Route::post('currencies/{currency}/toggle-active', [CurrencyController::class, 'toggleActive'])->name('currencies.toggle-active');
    Route::post('currencies/{currency}/toggle-auto-update', [CurrencyController::class, 'toggleAutoUpdate'])->name('currencies.toggle-auto-update');
    Route::post('currencies/update-rates', [CurrencyController::class, 'updateRates'])->name('currencies.update-rates');

    // Permissions
    Route::get('/permissions', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'index'])->name('permissions');
    Route::post('/permissions', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'update'])->name('permissions.update');
    Route::post('/roles', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'storeRole'])->name('roles.store');
    Route::get('/roles/{role}/permissions', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'getRolePermissions'])->name('roles.permissions.get');
    Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'updateRolePermissions'])->name('roles.permissions.update');
    Route::delete('/roles/{role}', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'destroyRole'])->name('roles.destroy');
    Route::post('/permissions/sync-registry', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'syncRegistry'])->name('permissions.sync-registry');
    Route::post('/roles/bulk-assign', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'bulkAssignRole'])->name('roles.bulkAssign');
    Route::post('/permissions/clear-cache', [\App\Http\Controllers\Tenant\Admin\PermissionsController::class, 'clearCache'])->name('permissions.clear-cache');
});

Route::middleware('user.type:admin')->group(function (): void {
    // Academic Management Routes
    Route::prefix('tenant/academics')->name('tenant.academics.')->group(function () {
        // Core Academic Resources
        Route::resource('education-levels', \App\Http\Controllers\Tenant\Academic\EducationLevelController::class);
        Route::resource('examination-bodies', \App\Http\Controllers\Tenant\Academic\ExaminationBodyController::class);
        Route::resource('countries', \App\Http\Controllers\Tenant\Academic\CountryController::class);
        Route::resource('grading_schemes', \App\Http\Controllers\Tenant\Academic\GradingSchemeController::class);
        Route::put('grading_schemes/{gradingScheme}/set-current', [\App\Http\Controllers\Tenant\Academic\GradingSchemeController::class, 'setCurrent'])->name('grading_schemes.set_current');
        Route::get('grading_schemes/export/all', [\App\Http\Controllers\Tenant\Academic\GradingSchemeController::class, 'exportAll'])->name('grading_schemes.export_all');

        // Subjects Management
        Route::resource('subjects', \App\Http\Controllers\Tenant\Academic\SubjectController::class);
        Route::get('subjects/{subject}/assign-classes', [\App\Http\Controllers\Tenant\Academic\SubjectController::class, 'assignClasses'])->name('subjects.assign_classes');
        Route::put('subjects/{subject}/assign-classes', [\App\Http\Controllers\Tenant\Academic\SubjectController::class, 'storeClassAssignments'])->name('subjects.store_class_assignments');

        // Teacher Allocation Management
        Route::get('teacher-allocations', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'index'])->name('teacher-allocations.index');
        Route::get('teacher-allocations/create', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'create'])->name('teacher-allocations.create');
        Route::get('teacher-allocations/workload', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'workload'])->name('teacher-allocations.workload');
        Route::post('teacher-allocations/bulk-assign', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'bulkAssign'])->name('teacher-allocations.bulk-assign');
        Route::get('teacher-allocations/class-subjects/{classId}', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'getClassSubjects'])->name('teacher-allocations.class-subjects');
        Route::post('teacher-allocations', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'store'])->name('teacher-allocations.store');
        Route::delete('teacher-allocations/{id}', [\App\Http\Controllers\Tenant\Academic\TeacherAllocationController::class, 'destroy'])->name('teacher-allocations.destroy');

        // Terms Management
        Route::resource('terms', \App\Http\Controllers\Tenant\Academic\TermController::class);
        Route::put('terms/{term}/set-current', [\App\Http\Controllers\Tenant\Academic\TermController::class, 'setCurrent'])->name('terms.set-current');

        // Timetable Management
        Route::get('timetable/generate', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'generate'])->name('timetable.generate');
        Route::post('timetable/generate', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'storeGenerated'])->name('timetable.storeGenerated');
        Route::resource('timetable', \App\Http\Controllers\Tenant\Academic\TimetableController::class);
        Route::delete('timetable/bulk-delete', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'bulkDelete'])->name('timetable.bulkDelete');
        Route::post('timetable/bulk-update', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'bulkUpdate'])->name('timetable.bulkUpdate');
        Route::get('timetable/class/{class}', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'showClass'])->name('timetable.class');
        Route::get('timetable/stream/{stream}', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'showStream'])->name('timetable.stream');
        Route::get('timetable/teacher/{teacher}', [\App\Http\Controllers\Tenant\Academic\TimetableController::class, 'showTeacher'])->name('timetable.teacher');

        Route::resource('classes', \App\Http\Controllers\Tenant\Academic\ClassController::class);

        // Room Management
        Route::resource('rooms', \App\Http\Controllers\Tenant\Academic\RoomController::class);

        // Class Subjects Management
        Route::get('classes/{class}/subjects', [\App\Http\Controllers\Tenant\Academic\ClassController::class, 'manageSubjects'])->name('classes.subjects');
        Route::post('classes/{class}/subjects', [\App\Http\Controllers\Tenant\Academic\ClassController::class, 'updateSubjects'])->name('classes.subjects.update');

        // Enrollment Management
        Route::resource('enrollments', \App\Http\Controllers\Tenant\Academics\EnrollmentController::class);
        Route::get('enrollments/streams', [\App\Http\Controllers\Tenant\Academics\EnrollmentController::class, 'getClassStreams'])->name('enrollments.streams');

        // Class Streams Routes (nested under classes)
        Route::prefix('classes/{class}/streams')->name('streams.')->group(function () {
            Route::get('/list', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'list'])->name('list');
            Route::get('/', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'store'])->name('store');
            Route::post('/bulk-create', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'bulkCreate'])->name('bulk-create');
            Route::get('/{stream}', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'show'])->name('show');
            Route::get('/{stream}/edit', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'edit'])->name('edit');
            Route::put('/{stream}', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'update'])->name('update');
            Route::delete('/{stream}', [\App\Http\Controllers\Tenant\Academic\ClassStreamController::class, 'destroy'])->name('destroy');
        });
    });

        // Lesson Plan Reviews
        Route::prefix('tenant/admin/lesson-plans')->name('tenant.admin.lesson-plans.')->group(function () {
            Route::get('/', [LessonPlanReviewController::class, 'index'])->name('index');
            Route::get('/{lessonPlan}', [LessonPlanReviewController::class, 'show'])->name('show');
            Route::post('/{lessonPlan}/approve', [LessonPlanReviewController::class, 'approve'])->name('approve');
            Route::post('/{lessonPlan}/request-revision', [LessonPlanReviewController::class, 'requestRevision'])->name('request-revision');
            Route::post('/{lessonPlan}/reject', [LessonPlanReviewController::class, 'reject'])->name('reject');
            Route::post('/{lessonPlan}/reopen', [LessonPlanReviewController::class, 'reopen'])->name('reopen');
        });

    // User Approvals Routes
    Route::prefix('admin/user-approvals')->name('admin.user-approvals.')->group(function () {
        Route::get('/', [UserApprovalController::class, 'index'])->name('index');
        Route::get('/{user}', [UserApprovalController::class, 'show'])->name('show');
        Route::post('/{user}/approve', [UserApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [UserApprovalController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [UserApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [UserApprovalController::class, 'bulkReject'])->name('bulk-reject');
        Route::post('/{user}/employment', [UserApprovalController::class, 'updateEmployment'])->name('employment');
        Route::post('/{user}/student-enrollment', [UserApprovalController::class, 'updateStudentEnrollment'])->name('student-enrollment');
        Route::post('/{user}/sync-student', [UserApprovalController::class, 'syncStudentProfile'])->name('sync-student');
        Route::post('/{user}/suspend', [UserApprovalController::class, 'suspend'])->name('suspend');
        Route::post('/{user}/reinstate', [UserApprovalController::class, 'reinstate'])->name('reinstate');
        Route::post('/{user}/expel', [UserApprovalController::class, 'expel'])->name('expel');
    });

    // Staff Role Management Routes
    Route::prefix('admin/staff')->name('admin.staff.')->group(function () {
        Route::post('/{user}/change-type', [\App\Http\Controllers\Admin\StaffRoleController::class, 'changeUserType'])->name('change-type');
    });

    // Reports Routes
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
        Route::get('/export-pdf', [ReportsController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [ReportsController::class, 'exportExcel'])->name('export-excel');
        Route::get('/download/{id}', [ReportsController::class, 'download'])->name('download');
        Route::get('/academic', [ReportsController::class, 'academic'])->name('academic');
        Route::get('/attendance', [ReportsController::class, 'attendance'])->name('attendance');
        Route::get('/financial', [ReportsController::class, 'financial'])->name('financial');
        Route::get('/enrollment', [ReportsController::class, 'enrollment'])->name('enrollment');
        Route::get('/late-submissions', [ReportsController::class, 'lateSubmissions'])->name('late-submissions');
        Route::get('/late-submissions/export', [ReportsController::class, 'lateSubmissionsExport'])->name('late-submissions.export');

        // Report Card Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\ReportSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [\App\Http\Controllers\Admin\ReportSettingsController::class, 'update'])->name('settings.update');

        Route::get('/report-cards', [ReportsController::class, 'reportCards'])->name('report-cards');
        Route::post('/report-cards/export-student', [ReportsController::class, 'exportStudentReportCard'])->name('report-cards.export-student');
        Route::post('/report-cards/export-class', [ReportsController::class, 'exportClassReportCards'])->name('report-cards.export-class');
    });

    // Parent Management Routes (placeholder - TODO: Create ParentController)
    Route::prefix('tenant/users/parents')->name('tenant.users.parents.')->group(function () {
        // Route::get('/{user}/edit', [ParentController::class, 'edit'])->name('edit');
        // Route::put('/{user}', [ParentController::class, 'update'])->name('update');
    });

    // Financial Management Routes
    Route::prefix('tenant/finance')->name('tenant.finance.')->group(function () {
        Route::resource('expense-categories', \App\Http\Controllers\Tenant\Finance\ExpenseCategoryController::class);
        Route::resource('expenses', \App\Http\Controllers\Tenant\Finance\ExpenseController::class);
        Route::post('expenses/{expense}/approve', [\App\Http\Controllers\Tenant\Finance\ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('expenses/{expense}/reject', [\App\Http\Controllers\Tenant\Finance\ExpenseController::class, 'reject'])->name('expenses.reject');
        Route::resource('fee-structures', \App\Http\Controllers\Tenant\Finance\FeeStructureController::class);
        Route::resource('invoices', \App\Http\Controllers\Tenant\Finance\InvoiceController::class);
        Route::resource('payments', \App\Http\Controllers\Tenant\Finance\PaymentController::class)->except(['edit', 'update', 'destroy']);
        Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Tenant\Finance\PaymentController::class, 'receipt'])->name('payments.receipt');
    });


    // Human Resource Management Routes
    Route::prefix('tenant/modules/human-resource')->name('tenant.modules.human-resource.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\HumanResourceController::class, 'index'])->name('index');
        Route::resource('employees', \App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class);
        Route::put('employees/{employee}/update-detail', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class, 'updateDetail'])
            ->name('employees.update-detail');
        Route::put('employees/{employee}/update-photo', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class, 'updatePhoto'])
            ->name('employees.update-photo');

        // Employee IDs Management
        Route::get('employee-ids', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'index'])->name('employee-ids.index');
        Route::post('employee-ids/generate', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'generate'])->name('employee-ids.generate');
        Route::post('employee-ids/preview', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'preview'])->name('employee-ids.preview');
        Route::any('employee-ids/download-svg', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'downloadSvg'])->name('employee-ids.download-svg');
        Route::any('employee-ids/download-png', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'downloadPng'])->name('employee-ids.download-png');

        Route::resource('departments', \App\Http\Controllers\Tenant\Modules\HumanResource\DepartmentsController::class);
        Route::resource('positions', \App\Http\Controllers\Tenant\Modules\HumanResource\PositionsController::class);

        // Salary Scales Custom Routes
        Route::get('salary-scales/export-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class, 'exportTemplate'])->name('salary-scales.exportTemplate');
        Route::get('salary-scales/export', [\App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class, 'export'])->name('salary-scales.export');
        Route::post('salary-scales/import/{format}', [\App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class, 'import'])->name('salary-scales.import');
        Route::resource('salary-scales', \App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class);

        // Leave Types Custom Routes
        Route::get('leave-types/export-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'exportTemplate'])->name('leave-types.exportTemplate');
        Route::get('leave-types/export-sql-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'exportSqlTemplate'])->name('leave-types.exportSqlTemplate');
        Route::get('leave-types/export', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'export'])->name('leave-types.export');
        Route::post('leave-types/import/{format}', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'import'])->name('leave-types.import');
        Route::resource('leave-types', \App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class);

        // Leave Requests Custom Routes
        Route::get('leave-requests/financial-report', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'financialReport'])->name('leave-requests.financial-report');
        Route::get('leave-requests/export-financial', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'exportFinancialReport'])->name('leave-requests.export-financial');
        Route::get('leave-requests/employee-balance/{employeeId?}', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'employeeBalance'])->name('leave-requests.employee-balance');
        Route::post('leave-requests/{leaveRequest}/approve', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'approve'])->name('leave-requests.approve');
        Route::post('leave-requests/{leaveRequest}/reject', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'reject'])->name('leave-requests.reject');
        Route::resource('leave-requests', \App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class);
        Route::get('payroll-settings', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'index'])
            ->name('payroll-settings.index');
        Route::get('payroll-settings/edit', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'edit'])
            ->name('payroll-settings.edit');
        Route::put('payroll-settings', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'update'])
            ->name('payroll-settings.update');
        Route::post('payroll-settings/reset', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'reset'])
            ->name('payroll-settings.reset');
        Route::post('payroll-settings/export', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'export'])
            ->name('payroll-settings.export');
    });

    // Library Management Routes
    Route::prefix('tenant/modules/library')->name('tenant.modules.library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'index'])->name('index');

        // Books
        Route::get('/books', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'books'])->name('books.index');
        Route::get('/books/create', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'createBook'])->name('books.create');
        Route::post('/books', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'storeBook'])->name('books.store');
        Route::get('/books/{book}', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'showBook'])->name('books.show');
        Route::get('/books/{book}/edit', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'editBook'])->name('books.edit');
        Route::put('/books/{book}', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'updateBook'])->name('books.update');
        Route::delete('/books/{book}', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'destroyBook'])->name('books.destroy');

        // Transactions
        Route::get('/transactions', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'transactions'])->name('transactions.index');
        Route::get('/transactions/borrow', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'borrowForm'])->name('transactions.borrow');
        Route::post('/transactions/borrow', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'borrow'])->name('transactions.store');
        Route::post('/transactions/{transaction}/return', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'returnBook'])->name('transactions.return');
    });

    // Pamphlets Management Routes
    Route::prefix('tenant/modules/pamphlets')->name('tenant.modules.pamphlets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\PamphletsController::class, 'index'])->name('index');
    });

    // Bookstore Module Routes
    Route::prefix('tenant/modules/bookstore')->name('tenant.modules.bookstore.')->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\BookstoreController::class, 'index'])->name('index');

        // Books (Inventory)
        Route::resource('books', \App\Http\Controllers\Tenant\Modules\BooksController::class);

        // Orders
        Route::get('orders', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/notes', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'updateNotes'])->name('orders.update-notes');
        Route::post('orders/{order}/paid', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'markPaid'])->name('orders.mark-paid');
        Route::post('orders/{order}/cancelled', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'markCancelled'])->name('orders.mark-cancelled');
    });

    // Bookstore Management Routes
    Route::prefix('tenant/bookstore')->name('tenant.bookstore.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\BookstoreController::class, 'index'])->name('index');
    });

    Route::prefix('admin')->name('tenant.')->group(function () {
        Route::resource('profile/admin', \App\Http\Controllers\Tenant\Admin\ProfileController::class)->names('profile.admin');
    });

    // User Management Routes
    Route::prefix('tenant/users')->name('tenant.users.')->group(function () {
        // Admins
        Route::resource('admins', \App\Http\Controllers\Tenant\Users\AdminsController::class)->names([
            'index' => 'admins',
        ]);
        Route::post('admins/{user}/activate', [\App\Http\Controllers\Tenant\Users\AdminsController::class, 'activate'])->name('admins.activate');
        Route::post('admins/{user}/deactivate', [\App\Http\Controllers\Tenant\Users\AdminsController::class, 'deactivate'])->name('admins.deactivate');

        // Parents
        Route::resource('parents', \App\Http\Controllers\Tenant\Users\ParentsController::class)->names([
            'index' => 'parents',
        ]);
        Route::post('parents/{user}/activate', [\App\Http\Controllers\Tenant\Users\ParentsController::class, 'activate'])->name('parents.activate');
        Route::post('parents/{user}/deactivate', [\App\Http\Controllers\Tenant\Users\ParentsController::class, 'deactivate'])->name('parents.deactivate');
    });

    // Student & Teacher Management Routes
    Route::prefix('tenant/modules')->name('tenant.modules.')->group(function () {
        Route::resource('students', \App\Http\Controllers\Tenant\Modules\StudentsController::class);
        Route::resource('teachers', \App\Http\Controllers\Tenant\Modules\TeachersController::class);
    });
});

// Student & Parent Accessible Payment Page
Route::middleware('role:Admin|admin|Student|student|Parent|parent')->group(function (): void {
    Route::get('/finance/pay', function () {
        return view('tenant.finance.payments.pay');
    })->name('tenant.finance.payments.pay');
});

// Attendance Management Routes (accessible to admin, teaching staff, and general staff)
Route::middleware(['auth', 'user.type:admin,teaching_staff,general_staff'])->group(function (): void {
    Route::prefix('tenant/modules/attendance')->name('tenant.modules.attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('show');
        Route::get('/{id}/mark', [\App\Http\Controllers\Admin\ManualAttendanceController::class, 'mark'])->name('mark');
        Route::post('/{id}/records', [\App\Http\Controllers\Admin\ManualAttendanceController::class, 'saveManual'])->name('save-records');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/kiosk/mode', [\App\Http\Controllers\Admin\AttendanceController::class, 'kiosk'])->name('kiosk');
        Route::post('/kiosk/check-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'kioskCheckIn'])->name('kiosk.check-in');
    });

    Route::middleware('user.type:admin')->prefix('tenant/attendance/settings')->name('tenant.attendance.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'index'])->name('index');
        Route::put('/general', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'updateGeneral'])->name('update-general');
        Route::put('/student-methods', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'updateStudentMethods'])->name('update-student-methods');
        Route::put('/staff-methods', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'updateStaffMethods'])->name('update-staff-methods');
        Route::put('/qr', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'updateQrSettings'])->name('update-qr');
        Route::put('/fingerprint', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'updateFingerprintSettings'])->name('update-fingerprint');
        Route::post('/fingerprint/test', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'testFingerprintDevice'])->name('test-fingerprint');
        Route::put('/optical', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'updateOpticalSettings'])->name('update-optical');
        Route::post('/clear-cache', [\App\Http\Controllers\Admin\AttendanceSettingController::class, 'clearCache'])->name('clear-cache');
    });

    Route::prefix('admin/qr-scanner')->name('admin.qr-scanner.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\QrScannerController::class, 'index'])->name('index');
        Route::post('/scan', [\App\Http\Controllers\Admin\QrScannerController::class, 'scan'])->name('scan');
        Route::post('/get-user', [\App\Http\Controllers\Admin\QrScannerController::class, 'getUserInfo'])->name('get-user');
    });

    Route::prefix('admin/manual-attendance')->name('admin.manual-attendance.')->group(function () {
        Route::get('/{attendance}/mark', [\App\Http\Controllers\Admin\ManualAttendanceController::class, 'mark'])->name('mark');
        Route::post('/{attendance}/save', [\App\Http\Controllers\Admin\ManualAttendanceController::class, 'saveManual'])->name('save-manual');
        Route::post('/{attendance}/bulk', [\App\Http\Controllers\Admin\ManualAttendanceController::class, 'bulkMark'])->name('bulk-mark');
    });

    Route::prefix('admin/biometric')->name('admin.biometric.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BiometricEnrollmentController::class, 'index'])->name('index');
        Route::get('/{userType}/{userId}/enroll', [\App\Http\Controllers\Admin\BiometricEnrollmentController::class, 'enroll'])->name('enroll');
        Route::post('/capture', [\App\Http\Controllers\Admin\BiometricEnrollmentController::class, 'capture'])->name('capture');
        Route::delete('/template/{template}', [\App\Http\Controllers\Admin\BiometricEnrollmentController::class, 'delete'])->name('delete');
        Route::post('/test-device', [\App\Http\Controllers\Admin\BiometricEnrollmentController::class, 'testDevice'])->name('test-device');
    });

    Route::prefix('admin/omr')->name('admin.omr.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\OmrTemplateController::class, 'index'])->name('index');
        Route::post('/generate', [\App\Http\Controllers\Admin\OmrTemplateController::class, 'generate'])->name('generate');
    });

    Route::prefix('admin/attendance-analytics')->name('admin.attendance-analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AttendanceAnalyticsController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\AttendanceAnalyticsController::class, 'export'])->name('export');
    });

    Route::prefix('admin/device-monitoring')->name('admin.device-monitoring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DeviceMonitoringController::class, 'index'])->name('index');
        Route::post('/test', [\App\Http\Controllers\Admin\DeviceMonitoringController::class, 'testConnection'])->name('test');
        Route::get('/stats', [\App\Http\Controllers\Admin\DeviceMonitoringController::class, 'getStats'])->name('stats');
    });

    Route::prefix('admin/staff-attendance')->name('admin.staff-attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'update'])->name('update');
        Route::patch('/{id}/approve', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'approve'])->name('approve');
        Route::post('/bulk-mark', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'bulkMark'])->name('bulk-mark');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/exam-attendance')->name('admin.exam-attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'show'])->name('show');
        Route::get('/{id}/mark', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'mark'])->name('mark');
        Route::post('/{id}/records', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'saveRecords'])->name('save-records');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationsController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationsController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NotificationsController::class, 'store'])->name('store');
    });

    Route::prefix('admin/messages')->name('admin.messages.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\MessagesController::class, 'index'])->name('index');
    });

    // Password Management Routes
    Route::prefix('admin/users')->name('admin.users.')->group(function() {
        Route::get('password', [\App\Http\Controllers\Admin\Users\PasswordController::class, 'index'])->name('password.index');
        Route::get('{user}/password', [\App\Http\Controllers\Admin\Users\PasswordController::class, 'show'])->name('password.show');
        Route::put('{user}/password/reset', [\App\Http\Controllers\Admin\Users\PasswordController::class, 'reset'])->name('password.reset');
    });
});
