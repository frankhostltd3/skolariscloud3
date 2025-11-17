<?php

use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UserApprovalsController;
use App\Http\Controllers\Settings\AcademicSettingsController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\GeneralSettingsController;
use App\Http\Controllers\Settings\MailSettingsController;
use App\Http\Controllers\Settings\MessagingSettingsController;
use App\Http\Controllers\Settings\PaymentSettingsController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\SystemSettingsController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

// Two-Factor Authentication Routes
Route::get('/security/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('/security/two-factor', [TwoFactorController::class, 'store'])->name('two-factor.store');
Route::post('/security/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
Route::delete('/security/two-factor', [TwoFactorController::class, 'destroy'])->name('two-factor.destroy');
Route::get('/security/two-factor/qr-code', [TwoFactorController::class, 'qrCode'])->name('two-factor.qr-code');
Route::get('/security/two-factor/recovery-codes', [TwoFactorController::class, 'recoveryCodes'])->name('two-factor.recovery-codes');
Route::post('/security/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes.regenerate');

Route::middleware('user.type:admin')->group(function (): void {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/general', [GeneralSettingsController::class, 'edit'])->name('settings.general.edit');
    Route::put('/settings/general', [GeneralSettingsController::class, 'update'])->name('settings.general.update');
    Route::post('/settings/general/clear-cache', [GeneralSettingsController::class, 'clearCache'])->name('settings.general.clear-cache');
    Route::get('/settings/mail', [MailSettingsController::class, 'edit'])->name('settings.mail.edit');
    Route::put('/settings/mail', [MailSettingsController::class, 'update'])->name('settings.mail.update');
    Route::get('/settings/payments', [PaymentSettingsController::class, 'edit'])->name('settings.payments.edit');
    Route::put('/settings/payments', [PaymentSettingsController::class, 'update'])->name('settings.payments.update');
    Route::get('/settings/messaging', [MessagingSettingsController::class, 'edit'])->name('settings.messaging.edit');
    Route::put('/settings/messaging', [MessagingSettingsController::class, 'update'])->name('settings.messaging.update');
    Route::get('/settings/academic', [AcademicSettingsController::class, 'edit'])->name('settings.academic.edit');
    Route::put('/settings/academic', [AcademicSettingsController::class, 'update'])->name('settings.academic.update');
    Route::post('/settings/academic/clear-cache', [AcademicSettingsController::class, 'clearCache'])->name('settings.academic.clear-cache');
    Route::get('/settings/system', [SystemSettingsController::class, 'edit'])->name('settings.system.edit');
    Route::put('/settings/system', [SystemSettingsController::class, 'update'])->name('settings.system.update');
    Route::post('/settings/system/clear-cache', [SystemSettingsController::class, 'clearCache'])->name('settings.system.clear-cache');

    // Currency Routes
    Route::resource('settings/currencies', CurrencyController::class)->names('settings.currencies');
    Route::post('/settings/currencies/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('settings.currencies.set-default');
    Route::post('/settings/currencies/{currency}/toggle-active', [CurrencyController::class, 'toggleActive'])->name('settings.currencies.toggle-active');
    Route::post('/settings/currencies/{currency}/toggle-auto-update', [CurrencyController::class, 'toggleAutoUpdate'])->name('settings.currencies.toggle-auto-update');
    Route::post('/settings/currencies/update-rates', [CurrencyController::class, 'updateRates'])->name('settings.currencies.update-rates');

    // Permissions & Access Control Routes
    Route::prefix('settings/admin')->name('tenant.settings.admin.')->group(function () {
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

        // Class Streams Routes (nested under classes)
        Route::prefix('classes/{class}/streams')->name('streams.')->group(function () {
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

    // User Approvals Routes
    Route::prefix('admin/user-approvals')->name('admin.user-approvals.')->group(function () {
        Route::get('/', [UserApprovalsController::class, 'index'])->name('index');
        Route::get('/{user}', [UserApprovalsController::class, 'show'])->name('show');
        Route::post('/{user}/approve', [UserApprovalsController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [UserApprovalsController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [UserApprovalsController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [UserApprovalsController::class, 'bulkReject'])->name('bulk-reject');
        Route::post('/{user}/employment', [UserApprovalsController::class, 'updateEmployment'])->name('employment');
        Route::post('/{user}/student-enrollment', [UserApprovalsController::class, 'updateStudentEnrollment'])->name('student-enrollment');
        Route::post('/{user}/suspend', [UserApprovalsController::class, 'suspend'])->name('suspend');
        Route::post('/{user}/reinstate', [UserApprovalsController::class, 'reinstate'])->name('reinstate');
        Route::post('/{user}/expel', [UserApprovalsController::class, 'expel'])->name('expel');
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
        Route::get('employees/{employee}/id-card', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'show'])->name('employees.id-card');
        Route::resource('departments', \App\Http\Controllers\Tenant\Modules\HumanResource\DepartmentsController::class);
        Route::resource('positions', \App\Http\Controllers\Tenant\Modules\HumanResource\PositionsController::class);
        Route::resource('salary-scales', \App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class);
        Route::resource('leave-types', \App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class);
        Route::resource('leave-requests', \App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class);
        Route::resource('payroll-settings', \App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class);
        Route::resource('payroll-payslip', \App\Http\Controllers\Tenant\Modules\HumanResource\PayrollPayslipController::class);
    });

    // Library Management Routes
    Route::prefix('tenant/modules/library')->name('tenant.modules.library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'index'])->name('index');
    });

    // Pamphlets Management Routes
    Route::prefix('tenant/modules/pamphlets')->name('tenant.modules.pamphlets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\PamphletsController::class, 'index'])->name('index');
    });

    // Books Module Routes
    Route::prefix('tenant/modules/books')->name('tenant.modules.books.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Modules\BooksController::class, 'index'])->name('index');
    });

    // Bookstore Management Routes
    Route::prefix('tenant/bookstore')->name('tenant.bookstore.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\BookstoreController::class, 'index'])->name('index');
    });
});

// Attendance Management Routes (accessible to admin, teaching staff, and general staff)
Route::middleware(['auth', 'user.type:admin,teaching_staff,general_staff'])->group(function (): void {
    Route::prefix('admin/attendance')->name('admin.attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('show');
        Route::get('/{id}/mark', [\App\Http\Controllers\Admin\AttendanceController::class, 'mark'])->name('mark');
        Route::post('/{id}/records', [\App\Http\Controllers\Admin\AttendanceController::class, 'saveRecords'])->name('save-records');
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
});
