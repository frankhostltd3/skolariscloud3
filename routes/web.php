<?php

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
use App\Http\Controllers\Landlord\HealthController as LandlordHealthController;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use App\Http\Controllers\Landlord\SettingsController as LandlordSettingsController;
use App\Http\Controllers\Landlord\RbacController;
use App\Http\Controllers\Landlord\TenantsController;
use App\Http\Controllers\Landlord\Tenants\CreateController as TenantsCreateController;
use App\Http\Controllers\Landlord\Tenants\ImportController as TenantsImportController;
use App\Http\Controllers\Landlord\Tenants\DomainsController as TenantsDomainsController;
use App\Http\Controllers\Landlord\DomainOrderController;
use Illuminate\Support\Facades\Route;

// Home page (Landing page)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dynamic Pages
Route::get('/p/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Public Bookstore Routes
Route::prefix('bookstore')->name('bookstore.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Tenant\BookstoreController::class, 'index'])->name('index');
    Route::get('/cart', [\App\Http\Controllers\Tenant\BookstoreController::class, 'cart'])->name('cart');
    Route::post('/cart/add/{book}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [\App\Http\Controllers\Tenant\BookstoreController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/remove', [\App\Http\Controllers\Tenant\BookstoreController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [\App\Http\Controllers\Tenant\BookstoreController::class, 'clearCart'])->name('cart.clear');
    Route::get('/checkout', [\App\Http\Controllers\Tenant\BookstoreController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [\App\Http\Controllers\Tenant\BookstoreController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/payment/callback', [\App\Http\Controllers\Tenant\BookstoreController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('/order-success/{order}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'orderSuccess'])->name('order.success');
    Route::get('/{book}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'show'])->name('show');

    // Authenticated Bookstore Routes
    Route::middleware('auth')->group(function () {
        Route::get('/account/orders', [\App\Http\Controllers\Tenant\BookstoreController::class, 'myOrders'])->name('my-orders');
        Route::get('/account/orders/{order}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'showOrder'])->name('order.show');
        Route::get('/account/orders/{order}/download/{book}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'download'])->name('download');
    });
});

Route::get('/landlord/logout-now', [AuthenticatedSessionController::class, 'destroy'])->name('landlord.logout.get');

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

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'update'])->name('profile.update');
});

// ======================================================================
// LANDLORD ROUTES
// ======================================================================
// Route::middleware('web')->prefix('landlord')->name('landlord.')->group(function (): void {
// Route::middleware('guest:landlord')->group(function (): void {
// Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login.show');
// Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
// });

Route::middleware(['web', 'landlord.context'])->prefix('landlord')->name('landlord.')->group(function (): void {
Route::middleware('guest:landlord')->group(function (): void {
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login.show');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth:landlord', \App\Http\Middleware\SetLandlordContext::class])->group(function () {
    Route::get('/landlord-debug', function () {
        $user = \Illuminate\Support\Facades\Auth::guard('landlord')->user();
        $perm = 'access landlord dashboard';
        $guard = 'landlord';

        $hasPerm = $user->hasPermissionTo($perm, $guard);

        return [
            'user_id' => $user->id,
            'user_class' => get_class($user),
            'user_connection' => $user->getConnectionName(),
            'team_id' => app(\Spatie\Permission\PermissionRegistrar::class)->getPermissionsTeamId(),
            'has_permission' => $hasPerm,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->permissions->pluck('name'),
            'role_permissions' => $user->getPermissionsViaRoles()->pluck('name'),
            'default_db_connection' => config('database.default'),
            'spatie_role_connection' => (new \Spatie\Permission\Models\Role)->getConnectionName(),
            'spatie_permission_connection' => (new \Spatie\Permission\Models\Permission)->getConnectionName(),
        ];
    });
});

Route::middleware(['auth:landlord', \App\Http\Middleware\SetLandlordContext::class])->group(function (): void {
    // Temporarily removed permission check to debug 403 error
    // 'permission:access landlord dashboard,landlord'
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

// Domain Order Management Routes
Route::prefix('domains')->name('domains.')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/approve', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/reject', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'reject'])->name('orders.reject');
    Route::post('/orders/{order}/activate-routing', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'activateRouting'])->name('orders.activate-routing');
    Route::post('/check-availability', [\App\Http\Controllers\Landlord\DomainOrderController::class, 'checkAvailability'])->name('check-availability');
});Route::get('/billing', BillingController::class)->name('billing');
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
Route::resource('rbac', RbacController::class)->names([
    'index' => 'rbac.index',
    'create' => 'rbac.create',
    'store' => 'rbac.store',
    'show' => 'rbac.show',
    'edit' => 'rbac.edit',
    'update' => 'rbac.update',
    'destroy' => 'rbac.destroy',
]);

Route::get('/profile', [LandlordProfileController::class, 'edit'])->name('profile');
Route::put('/profile', [LandlordProfileController::class, 'update'])->name('profile.update');

Route::get('/users', LandlordUsersController::class)->name('users');
Route::post('/users', [LandlordUsersController::class, 'store'])->name('users.store');
Route::post('/users/{user}/roles', [LandlordUsersController::class, 'updateRoles'])->name('users.roles.update');
Route::delete('/users/{user}', [LandlordUsersController::class, 'destroy'])->name('users.destroy');
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

Route::get('/integrations', [IntegrationsController::class, 'index'])->name('integrations');
    Route::get('health', HealthCheckResultsController::class)->name('health');
    Route::post('health/refresh', [LandlordHealthController::class, 'refresh'])->name('health.refresh');

    Route::resource('hero-slides', \App\Http\Controllers\Landlord\HeroSlideController::class);
    Route::resource('landing-features', \App\Http\Controllers\Landlord\LandingFeatureController::class);
    Route::resource('landing-stats', \App\Http\Controllers\Landlord\LandingStatController::class);
    Route::resource('landing-testimonials', \App\Http\Controllers\Landlord\LandingTestimonialController::class);
    Route::resource('landing-faqs', \App\Http\Controllers\Landlord\LandingFaqController::class);
    Route::resource('landing-sections', \App\Http\Controllers\Landlord\LandingSectionController::class);
    Route::resource('landing-pages', \App\Http\Controllers\Landlord\LandingPageController::class);
});
});

Route::post('/landlord/webhooks/{gateway}', [LandlordPaymentWebhookController::class, 'handle'])->name('landlord.webhooks.handle');
