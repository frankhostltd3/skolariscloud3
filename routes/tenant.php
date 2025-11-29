<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Modules\HumanResource\PayrollPayslipController;
use App\Http\Controllers\Tenant\Academics\TimetableController as AcademicsTimetableController;
use App\Http\Controllers\Admin\IntegrationSettingsController as AdminIntegrationSettingsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded with custom tenant middleware in bootstrap/app.php.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
])->group(function () {
    // Tenant Landing Page (Homepage)
    Route::get('/', [\App\Http\Controllers\Tenant\TenantHomeController::class, 'index'])->name('tenant.welcome');

    // Public Storefront (guest or authenticated)
    Route::prefix('store')->name('tenant.storefront.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Storefront\CatalogController::class, 'home'])->name('home');
        // Books
        Route::get('/books', [\App\Http\Controllers\Tenant\Storefront\CatalogController::class, 'books'])->name('books');
        Route::get('/books/{book}', [\App\Http\Controllers\Tenant\Storefront\CatalogController::class, 'bookShow'])->name('books.show');
        Route::get('/books/{book}/buy', [\App\Http\Controllers\Tenant\Storefront\CheckoutController::class, 'buyBook'])->name('books.buy');
        Route::post('/books/{book}/purchase', [\App\Http\Controllers\Tenant\Storefront\CheckoutController::class, 'purchaseBook'])->name('books.purchase');
        // Pamphlets
        Route::get('/pamphlets', [\App\Http\Controllers\Tenant\Storefront\CatalogController::class, 'pamphlets'])->name('pamphlets');
        Route::get('/pamphlets/{pamphlet}', [\App\Http\Controllers\Tenant\Storefront\CatalogController::class, 'pamphletShow'])->name('pamphlets.show');
        Route::get('/pamphlets/{pamphlet}/buy', [\App\Http\Controllers\Tenant\Storefront\CheckoutController::class, 'buyPamphlet'])->name('pamphlets.buy');
        Route::post('/pamphlets/{pamphlet}/purchase', [\App\Http\Controllers\Tenant\Storefront\CheckoutController::class, 'purchasePamphlet'])->name('pamphlets.purchase');
        // Thank you
        Route::get('/thanks', [\App\Http\Controllers\Tenant\Storefront\CheckoutController::class, 'thanks'])->name('thanks');

        // Payment webhooks (per-tenant). Providers send callbacks here.
        Route::prefix('webhooks')->name('webhooks.')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
            Route::post('/stripe', [\App\Http\Controllers\Tenant\Payments\PaymentWebhookController::class, 'stripe'])->name('stripe');
            Route::post('/paypal', [\App\Http\Controllers\Tenant\Payments\PaymentWebhookController::class, 'paypal'])->name('paypal');
            Route::post('/flutterwave', [\App\Http\Controllers\Tenant\Payments\PaymentWebhookController::class, 'flutterwave'])->name('flutterwave');
            Route::post('/mobile-money', [\App\Http\Controllers\Tenant\Payments\PaymentWebhookController::class, 'mobileMoney'])->name('mobile_money');
        });
    });

    // Public Bookstore (guest accessible - no auth required)
    Route::prefix('bookstore')->name('tenant.bookstore.')->group(function () {
        // Browse books
        Route::get('/', [\App\Http\Controllers\Tenant\BookstoreController::class, 'index'])->name('index');
        Route::get('/book/{book}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'show'])->name('show');

        // Shopping cart
        Route::get('/cart', [\App\Http\Controllers\Tenant\BookstoreController::class, 'cart'])->name('cart');
        Route::post('/cart/add/{book}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/update', [\App\Http\Controllers\Tenant\BookstoreController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove', [\App\Http\Controllers\Tenant\BookstoreController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [\App\Http\Controllers\Tenant\BookstoreController::class, 'clearCart'])->name('cart.clear');

        // Checkout
        Route::get('/checkout', [\App\Http\Controllers\Tenant\BookstoreController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/process', [\App\Http\Controllers\Tenant\BookstoreController::class, 'processCheckout'])->name('checkout.process');
        Route::get('/order/{order}/success', [\App\Http\Controllers\Tenant\BookstoreController::class, 'orderSuccess'])->name('order.success');
        Route::get('/payment/callback', [\App\Http\Controllers\Tenant\BookstoreController::class, 'paymentCallback'])->name('payment.callback');

        // Authenticated Routes
        Route::middleware('auth')->group(function () {
            Route::get('/account/orders', [\App\Http\Controllers\Tenant\BookstoreController::class, 'myOrders'])->name('my-orders');
            Route::get('/account/orders/{order}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'showOrder'])->name('order.show');
            Route::get('/account/orders/{order}/download/{book}', [\App\Http\Controllers\Tenant\BookstoreController::class, 'download'])->name('download');
        });
    });

    // ===================================================================
    // AUTHENTICATION ROUTES - New System
    // ===================================================================

    // GUEST ROUTES (Not Authenticated)
    Route::middleware('guest')->group(function () {
        // ADMIN REGISTRATION (Secured)
        Route::get('/admin/register', [\App\Http\Controllers\Tenant\Auth\AdminRegistrationController::class, 'create'])
            ->name('tenant.admin.register');
        Route::post('/admin/register', [\App\Http\Controllers\Tenant\Auth\AdminRegistrationController::class, 'store']);

        // GENERAL REGISTRATION (Staff, Students, Parents)
        Route::get('/register', [\App\Http\Controllers\Tenant\Auth\UserRegistrationController::class, 'create'])
            ->name('tenant.register');
        Route::post('/register', [\App\Http\Controllers\Tenant\Auth\UserRegistrationController::class, 'store']);

        // LOGIN (Unified for all user types)
        Route::get('/login', [\App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController::class, 'create'])
            ->name('tenant.login');
        Route::post('/login', [\App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController::class, 'store']);

        // PASSWORD RECOVERY
        Route::get('/forgot-password', [\App\Http\Controllers\Tenant\Auth\ForgotPasswordController::class, 'create'])
            ->name('tenant.forgot-password');
        Route::post('/forgot-password', [\App\Http\Controllers\Tenant\Auth\ForgotPasswordController::class, 'store']);

        Route::get('/reset-password/{token}', [\App\Http\Controllers\Tenant\Auth\ResetPasswordController::class, 'create'])
            ->name('tenant.reset-password');
        Route::post('/reset-password', [\App\Http\Controllers\Tenant\Auth\ResetPasswordController::class, 'store']);

        // TWO-FACTOR AUTHENTICATION CHALLENGE
        Route::prefix('2fa')->name('tenant.2fa.')->group(function () {
            Route::get('/challenge', [\App\Http\Controllers\Tenant\Auth\TwoFactorChallengeController::class, 'show'])
                ->name('challenge');
            Route::post('/challenge', [\App\Http\Controllers\Tenant\Auth\TwoFactorChallengeController::class, 'verify'])
                ->name('verify');
        });
    });

    // AUTHENTICATED ROUTES

    // 🧪 TEST ROUTE FOR BLADE DIRECTIVES (Development only)
    Route::get('/test-blade-directives', function () {
        return view('tenant.test-blade-directives');
    })->name('tenant.test.blade');

    // DEBUG ROUTE FOR ROLES
    Route::get('/debug-roles', function () {
        $user = auth()->user();
        $teamId = app(\Spatie\Permission\PermissionRegistrar::class)->getPermissionsTeamId();

        return [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->roles->pluck('name'),
            'current_team_id' => $teamId,
            'has_admin_role' => $user->hasRole('Admin'),
            'has_admin_role_lowercase' => $user->hasRole('admin'),
            'roles_relation' => $user->roles()->toSql(),
            'roles_bindings' => $user->roles()->getBindings(),
            'permissions_cache_key' => config('permission.cache.key'),
        ];
    })->middleware(['auth']);

    // DEBUG ROUTE FOR URL GENERATION
    Route::get('/debug-url', function () {
        return route('tenant.teacher.classroom.exams.questions.store', ['exam' => 1, 'section' => 1]);
    });

    // LOGOUT (Redirects to front page)
    Route::post('/logout', [\App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
        ->name('tenant.logout');

    // Email Verification Routes
    Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
        Route::get('/email/verify', function () {
            return view('auth.verify-email');
        })->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect()->intended(route('tenant.dashboard'));
        })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Request $request) {
            if ($request->user()->hasVerifiedEmail()) {
                return back()->with('status', __('Your email address is already verified.'));
            }

            $request->user()->sendEmailVerificationNotification();

            return back()->with('status', __('A new verification link has been sent to your email address.'));
        })->middleware(['throttle:6,1'])->name('verification.send');
    });

    // OTP Verification Routes
    Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
        Route::get('/verify-otp', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'show'])
            ->name('verification.otp.notice');
        Route::post('/verify-otp', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'verify'])
            ->name('verification.otp.verify');
        Route::post('/verify-otp/resend', [\App\Http\Controllers\Auth\OtpVerificationController::class, 'resend'])
            ->name('verification.otp.resend');
    });

    // APPROVAL STATUS PAGES (Accessible to authenticated users with pending/rejected status)
    Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
        Route::get('/pending-approval', function () {
            return view('tenant.auth.pending-approval');
        })->name('pending-approval');

        Route::get('/registration-rejected', function () {
            return view('auth.registration-rejected');
        })->name('registration-rejected');
    });

    // Default tenant entrypoint: redirect to a role-aware dashboard
    Route::get('/app', [\App\Http\Controllers\Tenant\HomeRedirectController::class, '__invoke'])
        ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
        ->name('tenant.dashboard');

        // Support /dashboard as an alias for /app so legacy links do not 404
        Route::get('/dashboard', function () {
                return redirect()->route('tenant.dashboard');
        })->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
            ->name('tenant.dashboard.alias');

    // ROLE-SPECIFIC DASHBOARDS
    Route::prefix('dashboard')->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
        Route::get('/admin', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, '__invoke'])
            ->middleware('role:Admin|admin|Super Admin|super-admin')
            ->name('tenant.admin');

        Route::get('/staff', [\App\Http\Controllers\Tenant\Staff\DashboardController::class, '__invoke'])
            ->middleware('role:Staff|staff|Teacher|teacher|Admin|admin')
            ->name('tenant.staff');

        Route::get('/student', [\App\Http\Controllers\Tenant\Student\DashboardController::class, '__invoke'])
            ->middleware('role:Student|student|Admin|admin')
            ->name('tenant.student');

        Route::get('/parent', [\App\Http\Controllers\Tenant\Parent\DashboardController::class, 'index'])
            ->middleware('role:Parent|parent')
            ->name('tenant.parent.dashboard');
    });

    // Shared authenticated admin & settings routes
    Route::middleware(['auth'])->group(function () {
        require base_path('routes/authenticated.php');
    });

    Route::prefix('parent')->name('tenant.parent.')->middleware([
        'auth',
        'role:Parent|parent',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Tenant\Parent\DashboardController::class, 'index'])->name('dashboard');

        // Performance
        Route::get('/performance', [\App\Http\Controllers\Tenant\Parent\PerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/{student}', [\App\Http\Controllers\Tenant\Parent\PerformanceController::class, 'show'])->name('performance.show');
        Route::get('/performance/{student}/download', [\App\Http\Controllers\Tenant\Parent\PerformanceController::class, 'downloadReport'])->name('performance.download');
        Route::post('/performance/{student}/email', [\App\Http\Controllers\Tenant\Parent\PerformanceController::class, 'emailReport'])->name('performance.email');

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Tenant\Parent\AttendanceController::class, 'index'])->name('attendance.index');

        // Fees
        Route::get('/fees', [\App\Http\Controllers\Tenant\Parent\FeesController::class, 'index'])->name('fees.index');

        // Behaviour
        Route::get('/behaviour', [\App\Http\Controllers\Tenant\Parent\BehaviourController::class, 'index'])->name('behaviour.index');

        // Announcements
        Route::get('/announcements', [\App\Http\Controllers\Tenant\Parent\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{announcement}', [\App\Http\Controllers\Tenant\Parent\AnnouncementController::class, 'show'])->name('announcements.show');

        // Meetings
        Route::get('/meetings', [\App\Http\Controllers\Tenant\Parent\MeetingController::class, 'index'])->name('meetings.index');
        Route::get('/meetings/{meeting}', [\App\Http\Controllers\Tenant\Parent\MeetingController::class, 'show'])->name('meetings.show');
    });

    // TWO-FACTOR AUTHENTICATION MANAGEMENT (Authenticated users)
    Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->prefix('user/2fa')->name('tenant.user.2fa.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\User\TwoFactorController::class, 'show'])
            ->name('show');
        Route::post('/enable', [\App\Http\Controllers\Tenant\User\TwoFactorController::class, 'enable'])
            ->name('enable');
        Route::post('/confirm', [\App\Http\Controllers\Tenant\User\TwoFactorController::class, 'confirm'])
            ->name('confirm');
        Route::delete('/disable', [\App\Http\Controllers\Tenant\User\TwoFactorController::class, 'disable'])
            ->name('disable');
        Route::get('/recovery-codes', [\App\Http\Controllers\Tenant\User\TwoFactorController::class, 'showRecoveryCodes'])
            ->name('recovery-codes');
        Route::post('/recovery-codes/regenerate', [\App\Http\Controllers\Tenant\User\TwoFactorController::class, 'regenerateRecoveryCodes'])
            ->name('regenerate-recovery-codes');
    });

    // Removed duplicate role-specific dashboard group (was causing role mismatch 403 for lowercase variants)

    // Student Module Routes
    Route::prefix('student')->name('tenant.student.')->middleware([
        'auth',
        'role:Student|student|Admin|admin',
        'approved',
        \App\Http\Middleware\PreventBackHistory::class,
        \App\Http\Middleware\EnsureFeesCleared::class,
    ])->group(function () {
        // Clearance Page
        Route::get('/clearance', [\App\Http\Controllers\Tenant\Student\ClearanceController::class, 'index'])->name('clearance');

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Tenant\Student\DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Tenant\Student\ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Tenant\Student\ProfileController::class, 'update'])->name('profile.update');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\Tenant\Student\SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Tenant\Student\SettingsController::class, 'update'])->name('settings.update');

        // Academic
        Route::get('/academic', [\App\Http\Controllers\Tenant\Student\AcademicController::class, 'progress'])->name('academic');
        Route::get('/academic/report/download', [\App\Http\Controllers\Tenant\Student\AcademicController::class, 'downloadReport'])->name('academic.report.download');
        Route::post('/academic/report/share', [\App\Http\Controllers\Tenant\Student\AcademicController::class, 'shareReport'])->name('academic.report.share');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Student\ReportsController::class, 'index'])->name('index');
            Route::get('/generate', [\App\Http\Controllers\Tenant\Student\ReportsController::class, 'generate'])->name('generate');
            Route::get('/download', [\App\Http\Controllers\Tenant\Student\ReportsController::class, 'download'])->name('download');
            Route::post('/share/email', [\App\Http\Controllers\Tenant\Student\ReportsController::class, 'shareEmail'])->name('share.email');
            Route::post('/share/whatsapp', [\App\Http\Controllers\Tenant\Student\ReportsController::class, 'shareWhatsapp'])->name('share.whatsapp');
        });

        // Assignments
        Route::get('/assignments', [\App\Http\Controllers\Tenant\Student\AssignmentsController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [\App\Http\Controllers\Tenant\Student\AssignmentsController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\Tenant\Student\AssignmentsController::class, 'submit'])->name('assignments.submit');

        // Online Classroom
        Route::prefix('classroom')->name('classroom.')->group(function () {
            // Dashboard
            Route::get('/', [\App\Http\Controllers\Tenant\Student\ClassroomController::class, 'index'])->name('index');
            Route::get('/classes', [\App\Http\Controllers\Tenant\Student\ClassroomController::class, 'classes'])->name('classes');
            Route::get('/classes/{class}', [\App\Http\Controllers\Tenant\Student\ClassroomController::class, 'showClass'])->name('classes.show');

            // Virtual Classes
            Route::prefix('virtual')->name('virtual.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Student\VirtualClassController::class, 'index'])->name('index');
                Route::get('/today', [\App\Http\Controllers\Tenant\Student\VirtualClassController::class, 'today'])->name('today');
                Route::get('/attendance', [\App\Http\Controllers\Tenant\Student\VirtualClassController::class, 'attendance'])->name('attendance');
                Route::get('/{class}', [\App\Http\Controllers\Tenant\Student\VirtualClassController::class, 'show'])->name('show');
                Route::get('/{class}/join', [\App\Http\Controllers\Tenant\Student\VirtualClassController::class, 'join'])->name('join');
                Route::get('/{class}/recording', [\App\Http\Controllers\Tenant\Student\VirtualClassController::class, 'recording'])->name('recording');
            });

            // Learning Materials
            Route::prefix('materials')->name('materials.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Student\MaterialController::class, 'index'])->name('index');
                Route::get('/recent', [\App\Http\Controllers\Tenant\Student\MaterialController::class, 'recent'])->name('recent');
                Route::get('/subject/{subject}', [\App\Http\Controllers\Tenant\Student\MaterialController::class, 'bySubject'])->name('by-subject');
                Route::get('/{material}', [\App\Http\Controllers\Tenant\Student\MaterialController::class, 'show'])->name('show');
                Route::get('/{material}/download', [\App\Http\Controllers\Tenant\Student\MaterialController::class, 'download'])->name('download');
            });

            // Exercises (Assignments)
            Route::prefix('exercises')->name('exercises.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'index'])->name('index');
                Route::get('/grades', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'grades'])->name('grades');
                Route::get('/{exercise}/download-pdf', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'downloadPdf'])->name('download-pdf');
                Route::get('/{exercise}/print', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'printView'])->name('print');
                Route::get('/{exercise}', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'show'])->name('show');
                Route::post('/{exercise}/submit', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'submit'])->name('submit');
                Route::get('/submissions/{submission}/download', [\App\Http\Controllers\Tenant\Student\ExerciseController::class, 'downloadSubmission'])->name('submissions.download');
            });
        });

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Tenant\Student\AttendanceController::class, 'index'])->name('attendance.index');

        // Attendance Reports
        Route::get('/attendance/reports', [\App\Http\Controllers\Tenant\Student\AttendanceReportsController::class, 'index'])->name('attendance.reports');

        // Documents
        Route::get('/documents', [\App\Http\Controllers\Tenant\Student\DocumentsController::class, 'index'])->name('documents.index');

        // Events
        Route::get('/events', [\App\Http\Controllers\Tenant\Student\EventsController::class, 'index'])->name('events.index');

        // Forums
        Route::prefix('forums')->name('forums.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Student\ForumsController::class, 'index'])->name('index');
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Student\Forum\CategoriesController::class, 'index'])->name('index');
            });
            Route::prefix('categories/{category}')->name('categories.')->group(function () {
                Route::prefix('threads')->name('threads.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Tenant\Student\Forum\ThreadsController::class, 'index'])->name('index');
                    Route::get('/create', [\App\Http\Controllers\Tenant\Student\Forum\ThreadsController::class, 'create'])->name('create');
                    Route::post('/', [\App\Http\Controllers\Tenant\Student\Forum\ThreadsController::class, 'store'])->name('store');
                    Route::get('/{thread}', [\App\Http\Controllers\Tenant\Student\Forum\ThreadsController::class, 'show'])->name('show');
                    Route::post('/{thread}/posts', [\App\Http\Controllers\Tenant\Student\Forum\PostsController::class, 'store'])->name('posts.store');
                });
            });
        });

        // Library
        Route::get('/library', [\App\Http\Controllers\Tenant\Student\LibraryController::class, 'index'])->name('library.index');
        Route::get('/library/my-borrows', [\App\Http\Controllers\Tenant\Student\LibraryController::class, 'myBorrows'])->name('library.my-borrows');
        Route::get('/library/{library}', [\App\Http\Controllers\Tenant\Student\LibraryController::class, 'show'])->name('library.show');
        Route::post('/library/{library}/borrow', [\App\Http\Controllers\Tenant\Student\LibraryController::class, 'borrow'])->name('library.borrow');
        Route::post('/library/transactions/{transaction}/extend', [\App\Http\Controllers\Tenant\Student\LibraryController::class, 'requestExtension'])->name('library.extend');

        // Meetings
        Route::get('/meetings', [\App\Http\Controllers\Tenant\Student\MeetingsController::class, 'index'])->name('meetings.index');

        // Messages
        Route::get('/messages', [\App\Http\Controllers\Tenant\Student\MessagesController::class, 'index'])->name('messages.index');
        Route::get('/messages/create', [\App\Http\Controllers\Tenant\Student\MessagesController::class, 'create'])->name('messages.create');
        Route::post('/messages', [\App\Http\Controllers\Tenant\Student\MessagesController::class, 'store'])->name('messages.store');
        Route::get('/messages/{id}', [\App\Http\Controllers\Tenant\Student\MessagesController::class, 'show'])->name('messages.show');
        Route::post('/messages/{id}/reply', [\App\Http\Controllers\Tenant\Student\MessagesController::class, 'reply'])->name('messages.reply');
        Route::delete('/messages/{id}', [\App\Http\Controllers\Tenant\Student\MessagesController::class, 'destroy'])->name('messages.destroy');

        // Notes - Teacher Materials
        Route::get('/notes', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'index'])->name('notes.index');
        Route::get('/notes/{note}', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'show'])->name('notes.show');
        Route::get('/notes/{note}/download', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'download'])->name('notes.download');

        // Notes - Personal Notes CRUD
        Route::get('/notes/personal/create', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'createPersonalNote'])->name('notes.personal.create');
        Route::post('/notes/personal', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'storePersonalNote'])->name('notes.personal.store');
        Route::get('/notes/personal/{personalNote}/edit', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'editPersonalNote'])->name('notes.personal.edit');
        Route::put('/notes/personal/{personalNote}', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'updatePersonalNote'])->name('notes.personal.update');
        Route::delete('/notes/personal/{personalNote}', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'destroyPersonalNote'])->name('notes.personal.destroy');
        Route::post('/notes/personal/{personalNote}/favorite', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'toggleFavorite'])->name('notes.personal.favorite');
        Route::post('/notes/ai-chat', [\App\Http\Controllers\Tenant\Student\NotesController::class, 'aiChat'])->name('notes.ai-chat');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'getUnreadCount'])->name('notifications.unreadCount');

        // Online Classes
        Route::get('/online-classes', [\App\Http\Controllers\Tenant\Student\OnlineClassesController::class, 'index'])->name('online-classes.index');
        Route::get('/online-classes/recordings', [\App\Http\Controllers\Tenant\Student\OnlineClassesController::class, 'recordings'])->name('online-classes.recordings');
        Route::get('/online-classes/{class}/join', [\App\Http\Controllers\Tenant\Student\OnlineClassesController::class, 'join'])->name('online-classes.join');
        Route::get('/online-classes/{class}/recording', [\App\Http\Controllers\Tenant\Student\OnlineClassesController::class, 'viewRecording'])->name('online-classes.recording');

        // Quizzes
        Route::get('/quizzes', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}/download-pdf', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'downloadPdf'])->name('quizzes.download-pdf');
        Route::get('/quizzes/{quiz}/print', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'print'])->name('quizzes.print');
        Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/start', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'start'])->name('quizzes.start');
        Route::get('/quizzes/{quiz}/take', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'submit'])->name('quizzes.submit');

        // Exams
        Route::get('/exams', [\App\Http\Controllers\Tenant\Student\ExamsController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [\App\Http\Controllers\Tenant\Student\ExamsController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [\App\Http\Controllers\Tenant\Student\ExamsController::class, 'start'])->name('exams.start');
        Route::get('/exams/{exam}/take', [\App\Http\Controllers\Tenant\Student\ExamsController::class, 'take'])->name('exams.take');
        Route::post('/exams/{exam}/submit', [\App\Http\Controllers\Tenant\Student\ExamsController::class, 'submit'])->name('exams.submit');

        // Schedule
        Route::get('/schedule', [\App\Http\Controllers\Tenant\Student\ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/schedule/export', [\App\Http\Controllers\Tenant\Student\ScheduleController::class, 'exportIcs'])->name('schedule.export');

        // Subjects
        Route::get('/subjects', [\App\Http\Controllers\Tenant\Student\SubjectsController::class, 'index'])->name('subjects.index');

        // Fees & Payments (Student)
        Route::prefix('fees')->name('fees.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Student\FeesController::class, 'index'])->name('index');
            Route::get('/{fee}', [\App\Http\Controllers\Tenant\Student\FeesController::class, 'show'])->name('show');
            Route::post('/{fee}/pay', [\App\Http\Controllers\Tenant\Student\FeesController::class, 'pay'])->name('pay');
            Route::get('/payments/{transactionId}/status', [\App\Http\Controllers\Tenant\Student\FeesController::class, 'paymentStatus'])->name('payment.status');
            Route::get('/{fee}/bank-slip', [\App\Http\Controllers\Tenant\Student\FeesController::class, 'bankSlip'])->name('bank_slip');
            Route::post('/{fee}/upload-proof', [\App\Http\Controllers\Tenant\Student\FeesController::class, 'uploadProof'])->name('upload_proof');
        });
    });

    // Teacher Module Routes
    Route::prefix('teacher')->name('tenant.teacher.')->middleware([
        'auth',
        'role:Teacher|teacher|Staff|staff|Admin|admin',
        'approved',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Tenant\Teacher\DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Tenant\Teacher\ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [\App\Http\Controllers\Tenant\Teacher\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/settings', [\App\Http\Controllers\Tenant\Teacher\ProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Tenant\Teacher\ProfileController::class, 'updateSettings'])->name('settings.update');
        Route::put('/password', [\App\Http\Controllers\Tenant\Teacher\ProfileController::class, 'updatePassword'])->name('password.update');

        // Classes
        Route::get('/classes', [\App\Http\Controllers\Tenant\Teacher\ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/{class}', [\App\Http\Controllers\Tenant\Teacher\ClassController::class, 'show'])->name('classes.show');
        Route::get('/classes/{class}/students', [\App\Http\Controllers\Tenant\Teacher\ClassController::class, 'students'])->name('classes.students');
        Route::get('/classes/{class}/grades', [\App\Http\Controllers\Tenant\Teacher\ClassController::class, 'grades'])->name('classes.grades');

        // Students
        Route::get('/students', [\App\Http\Controllers\Tenant\Teacher\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [\App\Http\Controllers\Tenant\Teacher\StudentController::class, 'show'])->name('students.show');

        // Subjects
        Route::get('/subjects', [\App\Http\Controllers\Tenant\Teacher\SubjectController::class, 'index'])->name('subjects.index');

        // Timetable
        Route::get('/timetable', [\App\Http\Controllers\Tenant\Teacher\TimetableController::class, 'index'])->name('timetable.index');
        Route::get('/timetable/{class}/edit', [\App\Http\Controllers\Tenant\Teacher\TimetableController::class, 'edit'])->name('timetable.edit');
        Route::put('/timetable/{class}', [\App\Http\Controllers\Tenant\Teacher\TimetableController::class, 'update'])->name('timetable.update');
        Route::get('/timetable/{class}/export', [\App\Http\Controllers\Tenant\Teacher\TimetableController::class, 'exportClassIcs'])->name('timetable.export');
        Route::get('/timetable/today', [\App\Http\Controllers\Tenant\Teacher\TimetableController::class, 'today'])->name('timetable.today');

        // Grades
        Route::get('/grades', [\App\Http\Controllers\Tenant\Teacher\GradesController::class, 'index'])->name('grades.index');
        Route::get('/grades/export', [\App\Http\Controllers\Tenant\Teacher\GradesController::class, 'exportCsv'])->name('grades.export');
        Route::get('/grades/create', [\App\Http\Controllers\Tenant\Teacher\GradesController::class, 'create'])->name('grades.create');
        Route::post('/grades', [\App\Http\Controllers\Tenant\Teacher\GradesController::class, 'store'])->name('grades.store');

        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            // Dashboard
            Route::get('/', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'index'])->name('index');

            // Take Roll Call
            Route::get('/take', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'takeRollCall'])->name('take');

            // Manual Entry
            Route::get('/manual', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'manual'])->name('manual');
            Route::post('/manual', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'store'])->name('store');

            // Biometric (Fingerprint & Iris)
            Route::get('/biometric', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'biometric'])->name('biometric');
            Route::post('/biometric/process', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'processBiometric'])->name('biometric.process');

            // Barcode/QR Scanning
            Route::get('/barcode', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'barcode'])->name('barcode');
            Route::post('/barcode/process', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'processBarcode'])->name('barcode.process');

            // Analysis & Reports
            Route::get('/patterns', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'patterns'])->name('patterns');
            Route::get('/reports', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'reports'])->name('reports');
            Route::get('/export', [\App\Http\Controllers\Tenant\Teacher\AttendanceController::class, 'exportReport'])->name('export');
        });

        // Online Classroom
        Route::prefix('classroom')->name('classroom.')->group(function () {
            // Dashboard
            Route::get('/', function() { return view('tenant.teacher.classroom.index'); })->name('index');

            // Virtual Classes
            Route::prefix('virtual')->name('virtual.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'store'])->name('store');
                Route::get('/{virtual}', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'show'])->name('show');
                Route::get('/{virtual}/edit', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'edit'])->name('edit');
                Route::put('/{virtual}', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'update'])->name('update');
                Route::delete('/{virtual}', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'destroy'])->name('destroy');
                Route::post('/{virtual}/start', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'start'])->name('start');
                Route::post('/{virtual}/end', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'end'])->name('end');
                Route::post('/{virtual}/cancel', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'cancel'])->name('cancel');
                Route::get('/{virtual}/attendance', [\App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'attendance'])->name('attendance');
            });

            // Platform Integrations
            Route::prefix('integrations')->name('integrations.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'index'])->name('index');
                Route::get('/{platform}/setup', [\App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'setup'])->name('setup');
                Route::post('/{platform}/store', [\App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'store'])->name('store');
                Route::post('/{platform}/test', [\App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'test'])->name('test');
                Route::post('/{platform}/disable', [\App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'disable'])->name('disable');
            });

            // Lesson Plans
            Route::prefix('lessons')->name('lessons.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'store'])->name('store');
                Route::get('/{lesson}', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'show'])->name('show');
                Route::get('/{lesson}/edit', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'edit'])->name('edit');
                Route::put('/{lesson}', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'update'])->name('update');
                Route::delete('/{lesson}', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'destroy'])->name('destroy');
                Route::post('/{lesson}/submit', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'submit'])->name('submit');
                Route::post('/{lesson}/mark-in-progress', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'markInProgress'])->name('mark-in-progress');
                Route::post('/{lesson}/mark-completed', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'markCompleted'])->name('mark-completed');
                Route::post('/{lesson}/duplicate', [\App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'duplicate'])->name('duplicate');
            });

            // Learning Materials
            Route::prefix('materials')->name('materials.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'store'])->name('store');
                Route::get('/{material}', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'show'])->name('show');
                Route::get('/{material}/edit', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'edit'])->name('edit');
                Route::put('/{material}', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'update'])->name('update');
                Route::delete('/{material}', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'destroy'])->name('destroy');
                Route::get('/{material}/download', [\App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'download'])->name('download');
            });

            // Exercises/Assignments
            Route::prefix('exercises')->name('exercises.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'store'])->name('store');
                Route::get('/{exercise}', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'show'])->name('show');
                Route::get('/{exercise}/edit', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'edit'])->name('edit');
                Route::put('/{exercise}', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'update'])->name('update');
                Route::delete('/{exercise}', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'destroy'])->name('destroy');

                // Submissions management
                Route::get('/{exercise}/submissions', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'submissions'])->name('submissions');
                Route::post('/{exercise}/submissions/{submission}/grade', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'grade'])->name('grade');
                Route::post('/{exercise}/bulk-grade', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'bulkGrade'])->name('bulk-grade');
                Route::get('/submissions/{submission}/download', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'downloadSubmission'])->name('download');

                // Advanced features
                Route::get('/{exercise}/analytics', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'analytics'])->name('analytics');
                Route::get('/{exercise}/export', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'export'])->name('export');
                Route::post('/{exercise}/duplicate', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'duplicate'])->name('duplicate');
                Route::post('/{exercise}/archive', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'archive'])->name('archive');
                Route::post('/{exercise}/reopen', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'reopen'])->name('reopen');
                Route::post('/{exercise}/auto-grade', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'autoGrade'])->name('auto-grade');
                Route::get('/{exercise}/plagiarism', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'checkPlagiarism'])->name('plagiarism');
                Route::post('/{exercise}/reminder', [\App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'sendReminder'])->name('reminder');
            });

            // Quizzes
            Route::prefix('quizzes')->name('quizzes.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'store'])->name('store');
                Route::get('/{quiz}', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'show'])->name('show');
                Route::get('/{quiz}/edit', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'edit'])->name('edit');
                Route::put('/{quiz}', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'update'])->name('update');
                Route::delete('/{quiz}', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'destroy'])->name('destroy');
                Route::get('/{quiz}/results', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'results'])->name('results');
                Route::post('/{quiz}/questions', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'addQuestion'])->name('questions.store');
                Route::put('/{quiz}/questions/{question}', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'updateQuestion'])->name('questions.update');
                Route::delete('/{quiz}/questions/{question}', [\App\Http\Controllers\Tenant\Teacher\QuizController::class, 'deleteQuestion'])->name('questions.destroy');
            });

            // Discussions
            Route::prefix('discussions')->name('discussions.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'store'])->name('store');
                Route::get('/{discussion}', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'show'])->name('show');
                Route::get('/{discussion}/edit', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'edit'])->name('edit');
                Route::put('/{discussion}', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'update'])->name('update');
                Route::delete('/{discussion}', [\App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'destroy'])->name('destroy');
            });

            // Online Exams
            Route::prefix('exams')->name('exams.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'store'])->name('store');
                Route::get('/{exam}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'show'])->name('show');
                Route::get('/{exam}/edit', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'edit'])->name('edit');
                Route::put('/{exam}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'update'])->name('update');
                Route::delete('/{exam}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'destroy'])->name('destroy');
                Route::post('/{exam}/publish', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'publish'])->name('publish');
                Route::post('/{exam}/generate', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'generate'])->name('generate');

                // Sections
                Route::post('/{exam}/sections', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'storeSection'])->name('sections.store');
                Route::put('/{exam}/sections/{section}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'updateSection'])->name('sections.update');
                Route::delete('/{exam}/sections/{section}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'destroySection'])->name('sections.destroy');

                // Questions
                Route::post('/{exam}/sections/{section}/questions', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'storeQuestion'])->name('questions.store');
                Route::put('/{exam}/sections/{section}/questions/{question}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'updateQuestion'])->name('questions.update');
                Route::delete('/{exam}/sections/{section}/questions/{question}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'destroyQuestion'])->name('questions.destroy');

                Route::get('/grading', function() { return view('tenant.teacher.classroom.exams.grading'); })->name('grading');
                Route::get('/results', function() { return view('tenant.teacher.classroom.exams.results'); })->name('results');
                Route::get('/{id}/grade', function() { return view('tenant.teacher.classroom.exams.grade'); })->name('grade');
            });
        });
    });

    // Exam Oversight (Admin)
    Route::prefix('admin/exams')->name('admin.exams.')->middleware([
        'auth',
        'role:Admin|admin|Super Admin|super-admin',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Admin\ExamReviewController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\Tenant\Admin\ExamReviewController::class, 'show'])->name('show');
        Route::post('/{exam}/approve', [\App\Http\Controllers\Tenant\Admin\ExamReviewController::class, 'approve'])->name('approve');
        Route::post('/{exam}/request-changes', [\App\Http\Controllers\Tenant\Admin\ExamReviewController::class, 'requestChanges'])->name('request-changes');
        Route::post('/{exam}/reject', [\App\Http\Controllers\Tenant\Admin\ExamReviewController::class, 'reject'])->name('reject');
        Route::post('/{exam}/activate', [\App\Http\Controllers\Tenant\Admin\ExamReviewController::class, 'activate'])->name('activate');
    });

    // Human Resources
    Route::prefix('human-resource')->name('tenant.modules.human-resource.')->middleware([
        'auth',
        'role:Admin|admin|Super Admin|super-admin|Staff|staff',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Departments
        Route::resource('departments', \App\Http\Controllers\Tenant\Modules\HumanResource\DepartmentsController::class)
            ->names([
                'index' => 'departments.index',
                'create' => 'departments.create',
                'store' => 'departments.store',
                'show' => 'departments.show',
                'edit' => 'departments.edit',
                'update' => 'departments.update',
                'destroy' => 'departments.destroy',
            ]);
        Route::get('departments-export', [\App\Http\Controllers\Tenant\Modules\HumanResource\DepartmentsController::class, 'export'])
            ->name('departments.export');

        // Positions
        Route::resource('positions', \App\Http\Controllers\Tenant\Modules\HumanResource\PositionsController::class)
            ->names([
                'index' => 'positions.index',
                'create' => 'positions.create',
                'store' => 'positions.store',
                'show' => 'positions.show',
                'edit' => 'positions.edit',
                'update' => 'positions.update',
                'destroy' => 'positions.destroy',
            ]);
        Route::get('positions-export', [\App\Http\Controllers\Tenant\Modules\HumanResource\PositionsController::class, 'export'])
            ->name('positions.export');
        Route::get('positions-export-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\PositionsController::class, 'exportTemplate'])
            ->name('positions.exportTemplate');
        Route::post('positions-import/{format}', [\App\Http\Controllers\Tenant\Modules\HumanResource\PositionsController::class, 'import'])
            ->name('positions.import');

        // Employees
        Route::resource('employees', \App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class)
            ->names([
                'index' => 'employees.index',
                'create' => 'employees.create',
                'store' => 'employees.store',
                'show' => 'employees.show',
                'edit' => 'employees.edit',
                'update' => 'employees.update',
                'destroy' => 'employees.destroy',
            ]);
        Route::put('employees/{employee}/update-detail', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class, 'updateDetail'])
            ->name('employees.update-detail');
        Route::put('employees/{employee}/update-photo', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class, 'updatePhoto'])
            ->name('employees.update-photo');
        Route::get('employees-export', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class, 'export'])
            ->name('employees.export');
        Route::post('employees/{employee}/create-user-account', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeesController::class, 'createUserAccount'])
            ->name('employees.create-user-account');

        // Salary Scales
        Route::resource('salary-scales', \App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class)
            ->names([
                'index' => 'salary_scales.index',
                'create' => 'salary_scales.create',
                'store' => 'salary_scales.store',
                'show' => 'salary_scales.show',
                'edit' => 'salary_scales.edit',
                'update' => 'salary_scales.update',
                'destroy' => 'salary_scales.destroy',
            ]);
        Route::get('salary-scales-export', [\App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class, 'export'])
            ->name('salary_scales.export');
        // Additional Salary Scale utilities (template + import)
        Route::get('salary-scales-export-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class, 'exportTemplate'])
            ->name('salary_scales.exportTemplate');
        Route::post('salary-scales-import/{format}', [\App\Http\Controllers\Tenant\Modules\HumanResource\SalaryScalesController::class, 'import'])
            ->name('salary_scales.import');

        // Leave Types
        Route::resource('leave-types', \App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class)
            ->names([
                'index' => 'leave_types.index',
                'create' => 'leave_types.create',
                'store' => 'leave_types.store',
                'show' => 'leave_types.show',
                'edit' => 'leave_types.edit',
                'update' => 'leave_types.update',
                'destroy' => 'leave_types.destroy',
            ]);
        Route::get('leave-types-export', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'export'])
            ->name('leave_types.export');
        Route::get('leave-types-export-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'exportTemplate'])
            ->name('leave_types.exportTemplate');
        Route::get('leave-types-export-sql-template', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'exportSqlTemplate'])
            ->name('leave_types.exportSqlTemplate');
        Route::post('leave-types-import/{format}', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveTypesController::class, 'import'])
            ->name('leave_types.import');

        // Leave Requests
        Route::resource('leave-requests', \App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class)
            ->names([
                'index' => 'leave_requests.index',
                'create' => 'leave_requests.create',
                'store' => 'leave_requests.store',
                'show' => 'leave_requests.show',
                'edit' => 'leave_requests.edit',
                'update' => 'leave_requests.update',
                'destroy' => 'leave_requests.destroy',
            ]);
        Route::post('leave-requests/{leave_request}/approve', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'approve'])
            ->name('leave_requests.approve');
        Route::post('leave-requests/{leave_request}/reject', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'reject'])
            ->name('leave_requests.reject');

        // Leave Financial Reports
        Route::get('leave-requests/reports/financial', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'financialReport'])
            ->name('leave_requests.financial_report');
        Route::get('leave-requests/reports/export', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'exportFinancialReport'])
            ->name('leave_requests.export_financial');
        Route::get('leave-requests/balance/{employee?}', [\App\Http\Controllers\Tenant\Modules\HumanResource\LeaveRequestsController::class, 'employeeBalance'])
            ->name('leave_requests.employee_balance');

        // Payroll Records
        Route::resource('payroll-records', \App\Http\Controllers\Tenant\Modules\HumanResource\PayrollRecordController::class)
            ->names([
                'index' => 'payroll-records.index',
                'create' => 'payroll-records.create',
                'store' => 'payroll-records.store',
                'show' => 'payroll-records.show',
                'edit' => 'payroll-records.edit',
                'update' => 'payroll-records.update',
                'destroy' => 'payroll-records.destroy',
            ]);

        // Payslip Download (Payroll Record)
        Route::get('payroll/{payroll}/payslip', [PayrollPayslipController::class, 'show'])
            ->name('payroll.payslip');

        // Payroll Payslip
        Route::get('payroll-payslip', [PayrollPayslipController::class, 'index'])->name('payroll-payslip.index');

        // Payroll Settings
        Route::get('payroll-settings', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'index'])->name('payroll-settings.index');
    });

    // Finance
    Route::prefix('finance')->name('tenant.finance.')->middleware([
        'auth',
        'role:Admin|admin|Super Admin|super-admin|Staff|staff',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Expense Categories
        Route::resource('expense-categories', \App\Http\Controllers\Tenant\Finance\ExpenseCategoryController::class);
        // Expenses
        Route::resource('expenses', \App\Http\Controllers\Tenant\Finance\ExpenseController::class);
        Route::post('expenses/{expense}/approve', [\App\Http\Controllers\Tenant\Finance\ExpenseController::class, 'approve'])->name('expenses.approve');
        Route::post('expenses/{expense}/reject', [\App\Http\Controllers\Tenant\Finance\ExpenseController::class, 'reject'])->name('expenses.reject');
        // Fee Structures
        Route::resource('fee-structures', \App\Http\Controllers\Tenant\Finance\FeeStructureController::class);
        // Invoices
        Route::get('invoices/generate', [\App\Http\Controllers\Tenant\Finance\InvoiceController::class, 'generateForTerm'])->name('invoices.generate');
        Route::post('invoices/generate', [\App\Http\Controllers\Tenant\Finance\InvoiceController::class, 'storeTermInvoices'])->name('invoices.store-term');
        Route::post('invoices/{invoice}/send-student', [\App\Http\Controllers\Tenant\Finance\InvoiceController::class, 'sendToStudent'])->name('invoices.send-student');
        Route::post('invoices/{invoice}/send-parent', [\App\Http\Controllers\Tenant\Finance\InvoiceController::class, 'sendToParent'])->name('invoices.send-parent');
        Route::post('invoices/{invoice}/send-both', [\App\Http\Controllers\Tenant\Finance\InvoiceController::class, 'sendToBoth'])->name('invoices.send-both');
        Route::post('invoices/bulk-send', [\App\Http\Controllers\Tenant\Finance\InvoiceController::class, 'bulkSend'])->name('invoices.bulk-send');
        Route::resource('invoices', \App\Http\Controllers\Tenant\Finance\InvoiceController::class);
        // Payments
        Route::resource('payments', \App\Http\Controllers\Tenant\Finance\PaymentController::class);
        Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Tenant\Finance\PaymentController::class, 'receipt'])->name('payments.receipt');
    });

    // Reports
    Route::prefix('reports')->name('tenant.reports.')->middleware([
        'auth',
        'role:Admin|admin|Super Admin|super-admin|Staff|staff|Teacher|teacher',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Reports\ReportsController::class, 'index'])->name('index');
        Route::get('financial', [\App\Http\Controllers\Tenant\Reports\ReportsController::class, 'financial'])->name('financial');
        Route::get('attendance', [\App\Http\Controllers\Tenant\Reports\ReportsController::class, 'attendance'])->name('attendance');
        Route::get('enrollment', [\App\Http\Controllers\Tenant\Reports\ReportsController::class, 'enrollment'])->name('enrollment');
        Route::get('late-submissions', [\App\Http\Controllers\Tenant\Reports\ReportsController::class, 'lateSubmissions'])->name('late-submissions');
        Route::get('academic', [\App\Http\Controllers\Tenant\Reports\ReportsController::class, 'academic'])->name('academic');

        Route::prefix('report-cards')->name('report-cards.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Reports\ReportCardsController::class, 'index'])->name('index');
        });
    });

    // Compatibility routes for /tenant/ prefix (to handle cached JS or misconfiguration)
    Route::group(['prefix' => 'tenant/teacher/classroom/exams', 'middleware' => ['auth', 'role:Teacher|teacher|Staff|staff|Admin|admin', 'approved']], function () {
        Route::post('/{exam}/sections/{section}/questions', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'storeQuestion']);
        Route::put('/{exam}/sections/{section}/questions/{question}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'updateQuestion']);
        Route::post('/{exam}/sections', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'storeSection']);
        Route::put('/{exam}/sections/{section}', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'updateSection']);
    });

    // Fallback for any other authenticated URL - maybe show a "not found" page or redirect to dashboard
    Route::fallback(function () {
        if (auth()->check()) {
            return redirect()->route('tenant.dashboard')->with('error', 'The page you requested could not be found.');
        }
        // For guests, the default Laravel 404 page is fine.
    })->middleware('auth');
});

    Route::middleware(['auth'])->prefix('forum')->name('tenant.forum.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'store'])->name('store');
        Route::get('/{slug}', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'show'])->name('show');
        Route::post('/{slug}/reply', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'reply'])->name('reply');
        Route::patch('/{slug}/status', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'updateStatus'])->name('status.update');
        Route::post('/ai-assist', [\App\Http\Controllers\Tenant\Forum\ForumController::class, 'aiAssist'])->name('ai-assist');
    });

