<?php

use App\Http\Controllers\Admin\UserApprovalsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
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

// Home page (Landing page)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

// Email Verification Routes (must be authenticated but not verified)
Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Your email has been verified successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent! Please check your email.');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/logout', [LoginController::class, 'destroy'])->name('logout.get'); // Temporary: allow GET logout

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

        // Attendance Management Routes
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

        // Staff Attendance Routes
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

        // Exam Attendance Routes
        Route::prefix('admin/exam-attendance')->name('admin.exam-attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'show'])->name('show');
            Route::get('/{id}/mark', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'mark'])->name('mark');
            Route::post('/{id}/records', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'saveRecords'])->name('save-records');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'destroy'])->name('destroy');
        });

        // Parent Management Routes (placeholder - TODO: Create ParentController)
        Route::prefix('tenant/users/parents')->name('tenant.users.parents.')->group(function () {
            // Route::get('/{user}/edit', [ParentController::class, 'edit'])->name('edit');
            // Route::put('/{user}', [ParentController::class, 'update'])->name('update');
        });
    });
});
