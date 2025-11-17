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
use App\Http\Controllers\Landlord\AnalyticsController;
use App\Http\Controllers\Landlord\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Landlord\BillingController;
use App\Http\Controllers\Landlord\Billing\InvoicesController as LandlordInvoicesController;
use App\Http\Controllers\Landlord\Billing\PaymentMethodsController as LandlordPaymentMethodsController;
use App\Http\Controllers\Landlord\Billing\InvoicePaymentController as LandlordInvoicePaymentController;
use App\Http\Controllers\Landlord\Billing\DunningController as LandlordDunningController;
use App\Http\Controllers\Landlord\Billing\PlansController as LandlordPlansController;
use App\Http\Controllers\Landlord\Webhooks\PaymentWebhookController as LandlordPaymentWebhookController;
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboardController;
use App\Http\Controllers\Landlord\ProfileController as LandlordProfileController;
use App\Http\Controllers\Landlord\UsersController as LandlordUsersController;
use App\Http\Controllers\Landlord\AuditLogsController;
use App\Http\Controllers\Landlord\NotificationsController as LandlordNotificationsController;
use App\Http\Controllers\Landlord\IntegrationsController;
use App\Http\Controllers\Landlord\SystemHealthController;
use App\Http\Controllers\Landlord\SettingsController as LandlordSettingsController;
use App\Http\Controllers\Landlord\RbacController;
use App\Http\Controllers\Landlord\TenantsController;
use App\Http\Controllers\Landlord\Tenants\CreateController as TenantsCreateController;
use App\Http\Controllers\Landlord\Tenants\ImportController as TenantsImportController;
use App\Http\Controllers\Landlord\Tenants\DomainsController as TenantsDomainsController;
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

    require base_path('routes/authenticated.php');
});

// ======================================================================
// LANDLORD ROUTES
// ======================================================================
Route::middleware('web')->prefix('landlord')->name('landlord.')->group(function (): void {
Route::middleware('guest:landlord')->group(function (): void {
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login.show');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth:landlord', 'permission:access landlord dashboard,landlord'])->group(function (): void {
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/dashboard', LandlordDashboardController::class)->name('dashboard');

Route::prefix('tenants')->name('tenants.')->group(function (): void {
Route::get('/', [TenantsController::class, 'index'])->name('index');
Route::get('/create', TenantsCreateController::class)->name('create');
Route::post('/create', [TenantsCreateController::class, 'store'])->name('store');
Route::get('/{tenant}/edit', [TenantsCreateController::class, 'edit'])->name('edit');
Route::put('/{tenant}', [TenantsCreateController::class, 'update'])->name('update');
Route::delete('/{tenant}', [TenantsCreateController::class, 'destroy'])->name('destroy');
Route::get('/import', TenantsImportController::class)->name('import');
Route::post('/import', [TenantsImportController::class, 'store'])->name('import.store');
Route::get('/export/excel', [TenantsController::class, 'exportExcel'])->name('export.excel');
Route::get('/export/sql', [TenantsController::class, 'exportSql'])->name('export.sql');
Route::get('/export/odata', [TenantsController::class, 'exportOdata'])->name('export.odata');
Route::get('/domains', TenantsDomainsController::class)->name('domains');
});

Route::get('/billing', BillingController::class)->name('billing');
Route::prefix('billing')->name('billing.')->group(function (): void {
Route::get('/invoices', [LandlordInvoicesController::class, 'index'])->name('invoices');
Route::get('/invoices/{invoice}', [LandlordInvoicesController::class, 'show'])->name('invoices.show');
Route::post('/invoices', [LandlordInvoicesController::class, 'store'])->name('invoices.store');
Route::get('/payment-methods', [LandlordPaymentMethodsController::class, 'index'])->name('payment-methods');
Route::post('/payment-methods', [LandlordPaymentMethodsController::class, 'store'])->name('payment-methods.store');
Route::post('/payment-methods/{gateway}/toggle', [LandlordPaymentMethodsController::class, 'toggle'])->name('payment-methods.toggle');
Route::delete('/payment-methods/{gateway}', [LandlordPaymentMethodsController::class, 'destroy'])->name('payment-methods.destroy');
Route::get('/dunning', [LandlordDunningController::class, 'index'])->name('dunning');
Route::post('/dunning', [LandlordDunningController::class, 'save'])->name('dunning.save');
Route::get('/dunning/preview', [LandlordDunningController::class, 'preview'])->name('dunning.preview');
Route::resource('plans', LandlordPlansController::class)->except(['show']);
});

Route::post('/invoices/{invoice}/pay', [LandlordInvoicePaymentController::class, 'initiate'])->name('invoices.pay');
Route::get('/payment/success/{invoice}', [LandlordInvoicePaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel/{invoice}', [LandlordInvoicePaymentController::class, 'cancel'])->name('payment.cancel');
Route::get('/payment/waiting/{transaction}', [LandlordInvoicePaymentController::class, 'waiting'])->name('payment.waiting');
Route::get('/api/payment/status/{transaction}', [LandlordInvoicePaymentController::class, 'checkStatus'])->name('api.payment.status');

Route::get('/analytics', AnalyticsController::class)->name('analytics');
Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');
Route::get('/settings', LandlordSettingsController::class)->name('settings');
Route::get('/rbac', RbacController::class)->name('rbac');

Route::get('/profile', [LandlordProfileController::class, 'edit'])->name('profile');
Route::put('/profile', [LandlordProfileController::class, 'update'])->name('profile.update');

Route::get('/users', LandlordUsersController::class)->name('users');
Route::get('/audit-logs', AuditLogsController::class)->name('audit');
Route::get('/audit-logs/export', [AuditLogsController::class, 'export'])->name('audit.export');

Route::prefix('notifications')->name('notifications.')->group(function (): void {
Route::get('/', [LandlordNotificationsController::class, 'index'])->name('index');
Route::get('/create', [LandlordNotificationsController::class, 'create'])->name('create');
Route::post('/', [LandlordNotificationsController::class, 'store'])->name('store');
Route::get('/{notification}/edit', [LandlordNotificationsController::class, 'edit'])->name('edit');
Route::put('/{notification}', [LandlordNotificationsController::class, 'update'])->name('update');
Route::delete('/{notification}', [LandlordNotificationsController::class, 'destroy'])->name('destroy');
Route::post('/{notification}/dispatch', [LandlordNotificationsController::class, 'dispatchNow'])->name('dispatch');
});

Route::get('/integrations', IntegrationsController::class)->name('integrations');
Route::get('/system-health', SystemHealthController::class)->name('health');
});
});

Route::post('/landlord/webhooks/{gateway}', [LandlordPaymentWebhookController::class, 'handle'])->name('landlord.webhooks.handle');
