<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Modules\HumanResource\PayrollPayslipController;
use App\Http\Controllers\Tenant\Academics\TimetableController as AcademicsTimetableController;

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

    // LOGOUT (Redirects to front page)
    Route::post('/logout', [\App\Http\Controllers\Tenant\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
        ->name('tenant.logout');

    // APPROVAL STATUS PAGES (Accessible to authenticated users with pending/rejected status)
    Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
        Route::get('/pending-approval', function () {
            return view('auth.pending-approval');
        })->name('pending-approval');

        Route::get('/registration-rejected', function () {
            return view('auth.registration-rejected');
        })->name('registration-rejected');
    });

    // Default tenant entrypoint: redirect to a role-aware dashboard
    Route::get('/app', [\App\Http\Controllers\Tenant\HomeRedirectController::class, '__invoke'])
        ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
        ->name('tenant.dashboard');

    // ROLE-SPECIFIC DASHBOARDS
    Route::prefix('dashboard')->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
        Route::get('/admin', [\App\Http\Controllers\Tenant\Admin\DashboardController::class, '__invoke'])
            ->middleware('role:Admin|admin')
            ->name('tenant.admin');

        Route::get('/staff', [\App\Http\Controllers\Tenant\Staff\DashboardController::class, '__invoke'])
            ->middleware('role:Staff|staff|Teacher|teacher|Admin|admin')
            ->name('tenant.staff');

        Route::get('/student', [\App\Http\Controllers\Tenant\Student\DashboardController::class, '__invoke'])
            ->middleware('role:Student|student|Admin|admin')
            ->name('tenant.student');

        Route::get('/parent', [\App\Http\Controllers\Tenant\Guardian\DashboardController::class, '__invoke'])
            ->middleware('role:Parent|parent')
            ->name('tenant.parent');
    });

    Route::prefix('parent')->name('tenant.parent.')->middleware([
        'auth',
        'role:Parent|parent',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        Route::get('/attendance', [\App\Http\Controllers\Tenant\Guardian\AttendanceController::class, 'index'])
            ->name('attendance.index');

        Route::get('/fees', [\App\Http\Controllers\Tenant\Guardian\FeesController::class, 'index'])
            ->name('fees.index');
        Route::post('/fees/{fee}/pay', [\App\Http\Controllers\Tenant\Guardian\FeesController::class, 'pay'])
            ->name('fees.pay');
        Route::get('/fees/download', [\App\Http\Controllers\Tenant\Guardian\FeesController::class, 'download'])
            ->name('fees.download');
        Route::get('/fees/payments/{payment}', [\App\Http\Controllers\Tenant\Guardian\FeesController::class, 'showPayment'])
            ->name('fees.payments.show');
        Route::get('/fees/invoices/{invoice}', [\App\Http\Controllers\Tenant\Guardian\FeesController::class, 'showInvoice'])
            ->name('fees.invoices.show');
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
        'role:Student|Admin',
        'approved',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
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
        Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/start', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'start'])->name('quizzes.start');
        Route::get('/quizzes/{quiz}/take', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\Tenant\Student\QuizzesController::class, 'submit'])->name('quizzes.submit');

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
        'role:Staff|Admin',
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
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'store'])->name('store');
                Route::get('/{virtual}', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'show'])->name('show');
                Route::get('/{virtual}/edit', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'edit'])->name('edit');
                Route::put('/{virtual}', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'update'])->name('update');
                Route::delete('/{virtual}', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'destroy'])->name('destroy');
                Route::post('/{virtual}/start', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'start'])->name('start');
                Route::post('/{virtual}/end', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'end'])->name('end');
                Route::post('/{virtual}/cancel', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'cancel'])->name('cancel');
                Route::get('/{virtual}/attendance', [App\Http\Controllers\Tenant\Teacher\VirtualClassController::class, 'attendance'])->name('attendance');
            });

            // Platform Integrations
            Route::prefix('integrations')->name('integrations.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'index'])->name('index');
                Route::get('/{platform}/setup', [App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'setup'])->name('setup');
                Route::post('/{platform}/store', [App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'store'])->name('store');
                Route::post('/{platform}/test', [App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'test'])->name('test');
                Route::post('/{platform}/disable', [App\Http\Controllers\Tenant\Teacher\PlatformIntegrationController::class, 'disable'])->name('disable');
            });

            // Lesson Plans
            Route::prefix('lessons')->name('lessons.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'store'])->name('store');
                Route::get('/{lesson}', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'show'])->name('show');
                Route::get('/{lesson}/edit', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'edit'])->name('edit');
                Route::put('/{lesson}', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'update'])->name('update');
                Route::delete('/{lesson}', [App\Http\Controllers\Tenant\Teacher\LessonPlanController::class, 'destroy'])->name('destroy');
            });

            // Learning Materials
            Route::prefix('materials')->name('materials.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'store'])->name('store');
                Route::get('/{material}', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'show'])->name('show');
                Route::get('/{material}/edit', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'edit'])->name('edit');
                Route::put('/{material}', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'update'])->name('update');
                Route::delete('/{material}', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'destroy'])->name('destroy');
                Route::get('/{material}/download', [App\Http\Controllers\Tenant\Teacher\LearningMaterialController::class, 'download'])->name('download');
            });

            // Exercises/Assignments
            Route::prefix('exercises')->name('exercises.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'store'])->name('store');
                Route::get('/{exercise}', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'show'])->name('show');
                Route::get('/{exercise}/edit', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'edit'])->name('edit');
                Route::put('/{exercise}', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'update'])->name('update');
                Route::delete('/{exercise}', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'destroy'])->name('destroy');
                Route::get('/{exercise}/submissions', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'submissions'])->name('submissions');
                Route::post('/{exercise}/submissions/{submission}/grade', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'grade'])->name('grade');
                Route::post('/{exercise}/bulk-grade', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'bulkGrade'])->name('bulk-grade');
                Route::get('/submissions/{submission}/download', [App\Http\Controllers\Tenant\Teacher\ExerciseController::class, 'downloadSubmission'])->name('download');
            });

            // Quizzes
            Route::prefix('quizzes')->name('quizzes.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'store'])->name('store');
                Route::get('/{quiz}', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'show'])->name('show');
                Route::get('/{quiz}/edit', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'edit'])->name('edit');
                Route::put('/{quiz}', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'update'])->name('update');
                Route::delete('/{quiz}', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'destroy'])->name('destroy');
                Route::get('/{quiz}/results', [App\Http\Controllers\Tenant\Teacher\QuizController::class, 'results'])->name('results');
            });

            // Discussions
            Route::prefix('discussions')->name('discussions.')->group(function () {
                Route::get('/', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'store'])->name('store');
                Route::get('/{discussion}', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'show'])->name('show');
                Route::get('/{discussion}/edit', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'edit'])->name('edit');
                Route::put('/{discussion}', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'update'])->name('update');
                Route::delete('/{discussion}', [App\Http\Controllers\Tenant\Teacher\DiscussionController::class, 'destroy'])->name('destroy');
            });

            // Online Exams
            Route::prefix('exams')->name('exams.')->group(function () {
                Route::get('/', function() { return view('tenant.teacher.classroom.exams.index'); })->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Teacher\ExamController::class, 'store'])->name('store');
                Route::get('/{id}', function() { return view('tenant.teacher.classroom.exams.show'); })->name('show');
                Route::get('/{id}/edit', function() { return view('tenant.teacher.classroom.exams.edit'); })->name('edit');
                Route::put('/{id}', function() { return redirect()->back()->with('success', 'Exam updated!'); })->name('update');
                Route::delete('/{id}', function() { return redirect()->back()->with('success', 'Exam deleted!'); })->name('destroy');
                Route::get('/grading', function() { return view('tenant.teacher.classroom.exams.grading'); })->name('grading');
                Route::get('/results', function() { return view('tenant.teacher.classroom.exams.results'); })->name('results');
                Route::get('/{id}/grade', function() { return view('tenant.teacher.classroom.exams.grade'); })->name('grade');
            });
        });
    });

    // Modules
    Route::prefix('modules')->name('tenant.modules.')->middleware([
        'auth',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Grades
        Route::get('/grades', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'index'])
            ->name('grades.index')
            ->middleware('permission:view grades|manage grades');
        Route::get('/grades/{grade}', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'show'])
            ->name('grades.show')
            ->middleware('permission:view grades|manage grades');
        // Fees
        Route::prefix('fees')->name('fees.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'index'])
                ->name('index')
                ->middleware('permission:view fees|manage fees');
            Route::get('/create', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'create'])
                ->name('create')
                ->middleware('permission:manage fees');
            Route::post('/', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'store'])
                ->name('store')
                ->middleware('permission:manage fees');
            Route::get('/{fee}', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'show'])
                ->name('show')
                ->middleware('permission:view fees|manage fees');
            Route::get('/{fee}/edit', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'edit'])
                ->name('edit')
                ->middleware('permission:manage fees');
            Route::put('/{fee}', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'update'])
                ->name('update')
                ->middleware('permission:manage fees');
            Route::delete('/{fee}', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:manage fees');

            // Fee Assignments
            Route::get('/{fee}/assign', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'assign'])
                ->name('assign')
                ->middleware('permission:manage fees');
            Route::post('/{fee}/assign', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'storeAssignment'])
                ->name('assign.store')
                ->middleware('permission:manage fees');
            Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'removeAssignment'])
                ->name('assignment.remove')
                ->middleware('permission:manage fees');

            // Payment Recording
            Route::get('/{fee}/record-payment', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'recordPayment'])
                ->name('record-payment')
                ->middleware('permission:manage fees');
            Route::post('/{fee}/record-payment', [\App\Http\Controllers\Tenant\Modules\FeesController::class, 'storePayment'])
                ->name('store-payment')
                ->middleware('permission:manage fees');
        });

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Tenant\Modules\AttendanceController::class, 'index'])
            ->name('attendance.index');
            // ->middleware('permission:view attendance|manage attendance');
        Route::post('/attendance/mark', [\App\Http\Controllers\Tenant\Modules\AttendanceController::class, 'mark'])
            ->name('attendance.mark');
            // ->middleware('permission:manage attendance');
        Route::post('/attendance/export', [\App\Http\Controllers\Tenant\Modules\AttendanceController::class, 'export'])
            ->name('attendance.export');
            // ->middleware('permission:view attendance|manage attendance');
        Route::get('/attendance/student/{student}/history', [\App\Http\Controllers\Tenant\Modules\AttendanceController::class, 'studentHistory'])
            ->name('attendance.student.history');
            // ->middleware('permission:view attendance|manage attendance');
        Route::post('/attendance/notify-absent', [\App\Http\Controllers\Tenant\Modules\AttendanceController::class, 'notifyAbsent'])
            ->name('attendance.notify');
            // ->middleware('permission:manage attendance');
        Route::get('/attendance/comparative-stats', [\App\Http\Controllers\Tenant\Modules\AttendanceController::class, 'comparativeStats'])
            ->name('attendance.comparative');
            // ->middleware('permission:view attendance|manage attendance');

        // Timetable
        Route::get('/timetable', [\App\Http\Controllers\Tenant\Modules\TimetableController::class, 'index'])
            ->name('timetable.index')
            ->middleware('permission:view timetable|manage timetable');

        // Library Management
        Route::prefix('library')->name('library.')->middleware(['role:Admin|Staff'])->group(function () {
            // Dashboard
            Route::get('/', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'index'])->name('index');

            // Books Management
            Route::get('/books', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'books'])->name('books.index');
            Route::get('/books/create', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'createBook'])->name('books.create');
            Route::post('/books', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'storeBook'])->name('books.store');
            Route::get('/books/{book}', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'showBook'])->name('books.show');
            Route::get('/books/{book}/edit', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'editBook'])->name('books.edit');
            Route::put('/books/{book}', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'updateBook'])->name('books.update');
            Route::delete('/books/{book}', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'destroyBook'])->name('books.destroy');

            // Transactions Management
            Route::get('/transactions', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'transactions'])->name('transactions.index');
            Route::get('/transactions/borrow', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'borrowForm'])->name('transactions.borrow');
            Route::post('/transactions/borrow', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'borrow'])->name('transactions.store');
            Route::post('/transactions/{transaction}/return', [\App\Http\Controllers\Tenant\Modules\LibraryController::class, 'returnBook'])->name('transactions.return');
        });

        // Bookstore (Books & Pamphlets)
        Route::prefix('bookstore')->name('bookstore.')->middleware(['role:Admin|Staff'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Modules\BookstoreController::class, 'index'])->name('index');
            Route::resource('books', \App\Http\Controllers\Tenant\Modules\BooksController::class)->only(['index','create','store','show','edit','update','destroy']);
            Route::resource('pamphlets', \App\Http\Controllers\Tenant\Modules\PamphletsController::class)->only(['index','create','store','show','edit','update','destroy']);
            // Orders admin
            Route::get('orders', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'show'])->name('orders.show');
            Route::post('orders/{order}/notes', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'updateNotes'])->name('orders.notes');
            Route::post('orders/{order}/paid', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'markPaid'])->name('orders.paid');
            Route::post('orders/{order}/cancel', [\App\Http\Controllers\Tenant\Modules\OrdersController::class, 'markCancelled'])->name('orders.cancel');
        });

        // Financials
        Route::prefix('financials')->name('financials.')->middleware(['role:Admin|Staff'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'overview'])->name('overview');
            Route::get('/fees', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'fees'])->name('fees');
            Route::get('/fees/create', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'createFee'])->name('fees.create');
            Route::post('/fees', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'storeFee'])->name('fees.store');
            Route::get('/fees/assign', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'assignFees'])->name('fees.assign');
            Route::post('/fees/assign', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'storeFeeAssignment'])->name('fees.assign.store');
            Route::get('/fees/reminders', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'sendReminders'])->name('fees.reminders');
            Route::post('/fees/reminders', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'processReminders'])->name('fees.reminders.process');
            Route::get('/expenses', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'expenses'])->name('expenses');
            Route::get('/expenses/create', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'createExpense'])->name('expenses.create');
            Route::post('/expenses', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'storeExpense'])->name('expenses.store');
            Route::get('/expenses/{expense}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'showExpense'])->name('expenses.show');
            Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'editExpense'])->name('expenses.edit');
            Route::put('/expenses/{expense}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'updateExpense'])->name('expenses.update');
            Route::delete('/expenses/{expense}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'destroyExpense'])->name('expenses.destroy');
            Route::post('/expenses/{expense}/approve', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'approveExpense'])->name('expenses.approve');
            Route::post('/expenses/{expense}/reject', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'rejectExpense'])->name('expenses.reject');
            Route::get('/expenses-export', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'exportExpenses'])->name('expenses.export');
            Route::get('/expense-categories', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'expenseCategories'])->name('expense_categories');
            Route::get('/expense-categories/create', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'createExpenseCategory'])->name('expense_categories.create');
            Route::post('/expense-categories', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'storeExpenseCategory'])->name('expense_categories.store');
            Route::get('/expense-categories/{expenseCategory}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'showExpenseCategory'])->name('expense_categories.show');
            Route::get('/expense-categories/{expenseCategory}/edit', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'editExpenseCategory'])->name('expense_categories.edit');
            Route::put('/expense-categories/{expenseCategory}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'updateExpenseCategory'])->name('expense_categories.update');
            Route::delete('/expense-categories/{expenseCategory}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'destroyExpenseCategory'])->name('expense_categories.destroy');
            Route::get('/expense-categories-export', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'exportExpenseCategories'])->name('expense_categories.export');
            Route::get('/tuition-plans', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'tuitionPlans'])->name('tuition_plans');
            Route::get('/tuition-plans/create', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'createTuitionPlan'])->name('tuition_plans.create');
            Route::post('/tuition-plans', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'storeTuitionPlan'])->name('tuition_plans.store');
            Route::get('/tuition-plans/{tuitionPlan}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'showTuitionPlan'])->name('tuition_plans.show');
            Route::get('/tuition-plans/{tuitionPlan}/edit', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'editTuitionPlan'])->name('tuition_plans.edit');
            Route::put('/tuition-plans/{tuitionPlan}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'updateTuitionPlan'])->name('tuition_plans.update');
            Route::delete('/tuition-plans/{tuitionPlan}', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'destroyTuitionPlan'])->name('tuition_plans.destroy');
            Route::post('/tuition-plans/{tuitionPlan}/duplicate', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'duplicateTuitionPlan'])->name('tuition_plans.duplicate');
            Route::get('/invoices', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'invoices'])->name('invoices');
            Route::get('/invoices/create', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'createInvoice'])->name('invoices.create');
            Route::post('/invoices', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'storeInvoice'])->name('invoices.store');

            // Payments management (verify bank proofs)
            Route::get('/payments', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'payments'])->name('payments');
            Route::post('/payments/{payment}/confirm', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'confirmPayment'])->name('payments.confirm');
            Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Tenant\Modules\FinancialsController::class, 'rejectPayment'])->name('payments.reject');
        });

        // Quick actions
        Route::get('/teachers', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'index'])
            ->name('teachers.index');
        Route::get('/teachers/create', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'create'])
            ->name('teachers.create')
            ->middleware('role:Admin|Staff');
        Route::post('/teachers', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'store'])
            ->name('teachers.store')
            ->middleware('role:Admin|Staff');
        Route::get('/teachers/{teacher}', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'show'])
            ->name('teachers.show');
        Route::get('/teachers/{teacher}/edit', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'edit'])
            ->name('teachers.edit')
            ->middleware('role:Admin|Staff');
        Route::put('/teachers/{teacher}', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'update'])
            ->name('teachers.update')
            ->middleware('role:Admin|Staff');
        Route::delete('/teachers/{teacher}', [\App\Http\Controllers\Tenant\Modules\TeachersController::class, 'destroy'])
            ->name('teachers.destroy')
            ->middleware('role:Admin|Staff');
        Route::get('/students', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'index'])
            ->name('students.index');
        Route::get('/students/create', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'create'])
            ->name('students.create')
            ->middleware('role:Admin|Staff');
        Route::post('/students', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'store'])
            ->name('students.store')
            ->middleware('role:Admin|Staff');
        Route::get('/students/{student}', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'show'])
            ->name('students.show');
        Route::get('/students/{student}/edit', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'edit'])
            ->name('students.edit')
            ->middleware('role:Admin|Staff');
        Route::put('/students/{student}', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'update'])
            ->name('students.update')
            ->middleware('role:Admin|Staff');
        Route::delete('/students/{student}', [\App\Http\Controllers\Tenant\Modules\StudentsController::class, 'destroy'])
            ->name('students.destroy')
            ->middleware('role:Admin|Staff');
        Route::get('/subjects', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'index'])
            ->name('subjects.index');
        Route::get('/subjects/create', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'create'])
            ->name('subjects.create')
            ->middleware('role:Admin|Staff');
        Route::post('/subjects', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'store'])
            ->name('subjects.store')
            ->middleware('role:Admin|Staff');
        Route::get('/subjects/{subject}', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'show'])
            ->name('subjects.show');
        Route::get('/subjects/{subject}/edit', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'edit'])
            ->name('subjects.edit')
            ->middleware('role:Admin|Staff');
        Route::put('/subjects/{subject}', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'update'])
            ->name('subjects.update')
            ->middleware('role:Admin|Staff');
        Route::delete('/subjects/{subject}', [\App\Http\Controllers\Tenant\Modules\SubjectsController::class, 'destroy'])
            ->name('subjects.destroy')
            ->middleware('role:Admin|Staff');
        Route::get('/classes', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'index'])
            ->name('classes.index');
        Route::get('/classes/create', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'create'])
            ->name('classes.create')
            ->middleware('role:Admin|Staff');
        Route::post('/classes', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'store'])
            ->name('classes.store')
            ->middleware('role:Admin|Staff');
        Route::get('/classes/{class}', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'show'])
            ->name('classes.show');
        Route::get('/classes/{class}/edit', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'edit'])
            ->name('classes.edit')
            ->middleware('role:Admin|Staff');
        Route::put('/classes/{class}', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'update'])
            ->name('classes.update')
            ->middleware('role:Admin|Staff');
        Route::delete('/classes/{class}', [\App\Http\Controllers\Tenant\Modules\ClassesController::class, 'destroy'])
            ->name('classes.destroy')
            ->middleware('role:Admin|Staff');
        Route::get('/class-streams', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'index'])
            ->name('class_streams.index');
        Route::get('/class-streams/create', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'create'])
            ->name('class_streams.create')
            ->middleware('role:Admin|Staff');
        Route::post('/class-streams', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'store'])
            ->name('class_streams.store')
            ->middleware('role:Admin|Staff');
        Route::get('/class-streams/{class_stream}', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'show'])
            ->name('class_streams.show');
        Route::get('/class-streams/{class_stream}/edit', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'edit'])
            ->name('class_streams.edit')
            ->middleware('role:Admin|Staff');
        Route::put('/class-streams/{class_stream}', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'update'])
            ->name('class_streams.update')
            ->middleware('role:Admin|Staff');
        Route::delete('/class-streams/{class_stream}', [\App\Http\Controllers\Tenant\Modules\ClassStreamsController::class, 'destroy'])
            ->name('class_streams.destroy')
            ->middleware('role:Admin|Staff');
        Route::get('/grades/enter', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'enter'])
            ->name('grades.enter')
            ->middleware('permission:manage grades');
        Route::post('/grades', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'store'])
            ->name('grades.store')
            ->middleware('permission:manage grades');
    });

    // User Management (CRUD)
    Route::prefix('users')->name('tenant.users.')->middleware([
        'auth',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Admins: only Admins can manage
        Route::middleware('role:Admin|admin')->group(function () {
            Route::resource('admins', \App\Http\Controllers\Tenant\Users\AdminsController::class)
                ->parameters(['admins' => 'user'])
                ->names([
                    'index' => 'admins',
                    'create' => 'admins.create',
                    'store' => 'admins.store',
                    'show' => 'admins.show',
                    'edit' => 'admins.edit',
                    'update' => 'admins.update',
                    'destroy' => 'admins.destroy',
                ]);

            // User Activation/Deactivation
            Route::post('admins/{user}/activate', [\App\Http\Controllers\Tenant\Users\AdminsController::class, 'activate'])
                ->name('admins.activate');
            Route::post('admins/{user}/deactivate', [\App\Http\Controllers\Tenant\Users\AdminsController::class, 'deactivate'])
                ->name('admins.deactivate');
        });
        // Staff/Students/Parents: Admin or Staff can manage
        Route::middleware('role:Admin|Staff')->group(function () {
            Route::resource('staff', \App\Http\Controllers\Tenant\Users\StaffController::class)
                ->parameters(['staff' => 'user'])
                ->names([
                    'index' => 'staff',
                    'create' => 'staff.create',
                    'store' => 'staff.store',
                    'show' => 'staff.show',
                    'edit' => 'staff.edit',
                    'update' => 'staff.update',
                    'destroy' => 'staff.destroy',
                ]);
            Route::post('staff/{user}/activate', [\App\Http\Controllers\Tenant\Users\StaffController::class, 'activate'])
                ->name('staff.activate');
            Route::post('staff/{user}/deactivate', [\App\Http\Controllers\Tenant\Users\StaffController::class, 'deactivate'])
                ->name('staff.deactivate');

            Route::resource('students', \App\Http\Controllers\Tenant\Users\StudentsController::class)
                ->parameters(['students' => 'user'])
                ->names([
                    'index' => 'students',
                    'create' => 'students.create',
                    'store' => 'students.store',
                    'show' => 'students.show',
                    'edit' => 'students.edit',
                    'update' => 'students.update',
                    'destroy' => 'students.destroy',
                ]);
            Route::post('students/{user}/activate', [\App\Http\Controllers\Tenant\Users\StudentsController::class, 'activate'])
                ->name('students.activate');
            Route::post('students/{user}/deactivate', [\App\Http\Controllers\Tenant\Users\StudentsController::class, 'deactivate'])
                ->name('students.deactivate');

            Route::resource('parents', \App\Http\Controllers\Tenant\Users\ParentsController::class)
                ->parameters(['parents' => 'user'])
                ->names([
                    'index' => 'parents',
                    'create' => 'parents.create',
                    'store' => 'parents.store',
                    'show' => 'parents.show',
                    'edit' => 'parents.edit',
                    'update' => 'parents.update',
                    'destroy' => 'parents.destroy',
                ]);
            Route::post('parents/{user}/activate', [\App\Http\Controllers\Tenant\Users\ParentsController::class, 'activate'])
                ->name('parents.activate');
            Route::post('parents/{user}/deactivate', [\App\Http\Controllers\Tenant\Users\ParentsController::class, 'deactivate'])
                ->name('parents.deactivate');
        });
    });

    // Academics
    Route::prefix('academics')->name('tenant.academics.')->middleware([
        'auth',
        'role:Admin|Staff',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Classes
        Route::resource('classes', \App\Http\Controllers\Tenant\Academics\ClassesController::class)
            ->parameters(['classes' => 'class'])
            ->names([
                'index' => 'classes.index',
                'create' => 'classes.create',
                'store' => 'classes.store',
                'show' => 'classes.show',
                'edit' => 'classes.edit',
                'update' => 'classes.update',
                'destroy' => 'classes.destroy',
            ]);
        // Class Streams
        Route::resource('class-streams', \App\Http\Controllers\Tenant\Academics\ClassStreamsController::class)
            ->parameters(['class-streams' => 'class_stream'])
            ->names([
                'index' => 'class_streams.index',
                'create' => 'class_streams.create',
                'store' => 'class_streams.store',
                'show' => 'class_streams.show',
                'edit' => 'class_streams.edit',
                'update' => 'class_streams.update',
                'destroy' => 'class_streams.destroy',
            ]);
        Route::get('class-streams/options', [\App\Http\Controllers\Tenant\Academics\ClassStreamsController::class, 'options'])
            ->name('class_streams.options');
        // Terms
        Route::resource('terms', \App\Http\Controllers\Tenant\Academics\TermsController::class)
            ->names([
                'index' => 'terms.index',
                'create' => 'terms.create',
                'store' => 'terms.store',
                'show' => 'terms.show',
                'edit' => 'terms.edit',
                'update' => 'terms.update',
                'destroy' => 'terms.destroy',
            ]);

        // Subjects
        Route::resource('subjects', \App\Http\Controllers\Tenant\Academics\SubjectsController::class)
            ->names([
                'index' => 'subjects.index',
                'create' => 'subjects.create',
                'store' => 'subjects.store',
                'show' => 'subjects.show',
                'edit' => 'subjects.edit',
                'update' => 'subjects.update',
                'destroy' => 'subjects.destroy',
            ]);

        // Education Levels
        Route::resource('education-levels', \App\Http\Controllers\Tenant\Academics\EducationLevelsController::class)
            ->parameters(['education-levels' => 'education_level'])
            ->names([
                'index' => 'education_levels.index',
                'create' => 'education_levels.create',
                'store' => 'education_levels.store',
                'show' => 'education_levels.show',
                'edit' => 'education_levels.edit',
                'update' => 'education_levels.update',
                'destroy' => 'education_levels.destroy',
            ]);

        // Grading Systems
        Route::resource('grading-schemes', \App\Http\Controllers\Tenant\Academics\GradingSchemesController::class)
            ->parameters(['grading-schemes' => 'grading_scheme'])
            ->names([
                'index' => 'grading_schemes.index',
                'create' => 'grading_schemes.create',
                'store' => 'grading_schemes.store',
                'show' => 'grading_schemes.show',
                'edit' => 'grading_schemes.edit',
                'update' => 'grading_schemes.update',
                'destroy' => 'grading_schemes.destroy',
            ]);
        Route::post('grading-schemes/create-from-template', [\App\Http\Controllers\Tenant\Academics\GradingSchemesController::class, 'createFromTemplate'])
            ->name('grading_schemes.create_from_template')
            ->middleware(['permission:manage grading systems']);
        Route::get('grading-schemes/{grading_scheme}/export', [\App\Http\Controllers\Tenant\Academics\GradingSchemesController::class, 'export'])
            ->name('grading_schemes.export')
            ->middleware(['permission:view grading systems']);
        Route::get('grading-schemes-export', [\App\Http\Controllers\Tenant\Academics\GradingSchemesController::class, 'exportAll'])
            ->name('grading_schemes.export_all')
            ->middleware(['permission:view grading systems']);

        // Grading Bands (nested under grading schemes)
        Route::prefix('grading-schemes/{grading_scheme}/bands')->name('grading_schemes.bands.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Tenant\Academics\GradingSchemesController::class, 'storeBand'])
                ->name('store')
                ->middleware(['permission:manage grading systems']);
            Route::put('/{band}', [\App\Http\Controllers\Tenant\Academics\GradingSchemesController::class, 'updateBand'])
                ->name('update')
                ->middleware(['permission:manage grading systems']);
            Route::delete('/{band}', [\App\Http\Controllers\Tenant\Academics\GradingSchemesController::class, 'deleteBand'])
                ->name('destroy')
                ->middleware(['permission:manage grading systems']);
        });

        // Countries
        Route::resource('countries', \App\Http\Controllers\Tenant\Academics\CountriesController::class)
            ->names([
                'index' => 'countries.index',
                'create' => 'countries.create',
                'store' => 'countries.store',
                'show' => 'countries.show',
                'edit' => 'countries.edit',
                'update' => 'countries.update',
                'destroy' => 'countries.destroy',
            ]);

        // Examination Bodies
        Route::resource('examination-bodies', \App\Http\Controllers\Tenant\Academics\ExaminationBodiesController::class)
            ->parameters(['examination-bodies' => 'examination_body'])
            ->names([
                'index' => 'examination_bodies.index',
                'create' => 'examination_bodies.create',
                'store' => 'examination_bodies.store',
                'show' => 'examination_bodies.show',
                'edit' => 'examination_bodies.edit',
                'update' => 'examination_bodies.update',
                'destroy' => 'examination_bodies.destroy',
            ])
            ->middleware(['permission:view examination bodies|manage examination bodies']);
        Route::post('examination-bodies/{examination_body}/set-current', [\App\Http\Controllers\Tenant\Academics\ExaminationBodiesController::class, 'setCurrent'])
            ->name('examination_bodies.set_current')
            ->middleware(['permission:manage examination bodies']);



        // Grades shortcuts under Academics (mirror Modules)
        Route::get('/grades', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'index'])
            ->name('grades.index')
            ->middleware('permission:view grades|manage grades');
        Route::get('/grades/{grade}', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'show'])
            ->name('grades.show')
            ->middleware('permission:view grades|manage grades');
        Route::get('/grades/enter', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'enter'])
            ->name('grades.enter')
            ->middleware('permission:manage grades');
        Route::post('/grades', [\App\Http\Controllers\Tenant\Modules\GradesController::class, 'store'])
            ->name('grades.store')
            ->middleware('permission:manage grades');
        // Timetable
        Route::prefix('timetable')->name('timetable.')->group(function () {
            Route::get('/', [AcademicsTimetableController::class, 'index'])->name('index')->middleware('permission:view timetable|manage timetable');
            Route::get('/create', [AcademicsTimetableController::class, 'create'])->name('create')->middleware('permission:manage timetable');
            Route::post('/', [AcademicsTimetableController::class, 'store'])->name('store')->middleware('permission:manage timetable');
            Route::get('/{timetable}/edit', [AcademicsTimetableController::class, 'edit'])->name('edit')->middleware('permission:manage timetable');
            Route::put('/{timetable}', [AcademicsTimetableController::class, 'update'])->name('update')->middleware('permission:manage timetable');
            Route::delete('/{timetable}', [AcademicsTimetableController::class, 'destroy'])->name('destroy')->middleware('permission:manage timetable');
            Route::get('/generate', [AcademicsTimetableController::class, 'generate'])->name('generate')->middleware('permission:manage timetable');
            Route::post('/generate', [AcademicsTimetableController::class, 'storeGenerated'])->name('storeGenerated')->middleware('permission:manage timetable');
            Route::delete('/bulk-delete', [AcademicsTimetableController::class, 'bulkDelete'])->name('bulkDelete')->middleware('permission:manage timetable');
            Route::post('/bulk-update', [AcademicsTimetableController::class, 'bulkUpdate'])->name('bulkUpdate')->middleware('permission:manage timetable');
        });

        // Teacher Allocations
        Route::prefix('allocations/teachers')->name('allocations.teachers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'index'])->name('index');
            Route::get('/{teacher}', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'show'])->name('show');
            Route::post('/{teacher}/allocate-classes', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'allocateClasses'])->name('allocate_classes');
            Route::post('/{teacher}/allocate-subjects', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'allocateSubjects'])->name('allocate_subjects');
            Route::delete('/{teacher}/classes/{class}', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'removeClassAllocation'])->name('remove_class');
            Route::delete('/{teacher}/subjects/{subject}', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'removeSubjectAllocation'])->name('remove_subject');
            Route::post('/classes/{class}/set-main-teacher', [\App\Http\Controllers\Tenant\Academics\TeacherAllocationController::class, 'setMainClassTeacher'])->name('set_main_teacher');
        });

        // Student Allocations
        Route::prefix('allocations/students')->name('allocations.students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'index'])->name('index');
            Route::get('/{student}', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'show'])->name('show');
            Route::post('/{student}/allocate-class', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'allocateClass'])->name('allocate_class');
            Route::post('/{student}/allocate-subjects', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'allocateSubjects'])->name('allocate_subjects');
            Route::delete('/{student}/subjects/{subject}', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'removeSubjectAllocation'])->name('remove_subject');
            Route::post('/bulk-allocate-class', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'bulkAllocateClass'])->name('bulk_allocate_class');
            Route::post('/bulk-allocate-subjects', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'bulkAllocateSubjects'])->name('bulk_allocate_subjects');
            Route::post('/promote', [\App\Http\Controllers\Tenant\Academics\StudentAllocationController::class, 'promoteStudents'])->name('promote');
        });
    });

    // Human Resources
    Route::prefix('human-resources')->name('tenant.modules.human_resources.')->middleware([
        'auth',
        'role:Admin|Staff',
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

        // Payslip Download (Payroll Record)
        Route::get('payroll/{payroll}/payslip', [PayrollPayslipController::class, 'show'])
            ->name('payroll.payslip');

        // Payroll Settings
        Route::get('payroll-settings', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'index'])
            ->name('payroll-settings.index');
        Route::get('payroll-settings/edit', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'edit'])
            ->name('payroll-settings.edit');
        Route::put('payroll-settings', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'update'])
            ->name('payroll-settings.update');
        Route::post('payroll-settings/reset', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'reset'])
            ->name('payroll-settings.reset');
        Route::get('payroll-settings/export/json', [\App\Http\Controllers\Tenant\Modules\HumanResource\PayrollSettingsController::class, 'export'])
            ->name('payroll-settings.export');

        // Employee IDs
        Route::get('employee-ids', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'index'])
            ->name('employee-ids.index');
        Route::post('employee-ids/generate', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'generate'])
            ->name('employee-ids.generate');
        Route::post('employee-ids/preview', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'preview'])
            ->name('employee-ids.preview');
        Route::get('employee-ids/download/svg', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'downloadSvg'])
            ->name('employee-ids.download.svg');
        Route::get('employee-ids/download/png', [\App\Http\Controllers\Tenant\Modules\HumanResource\EmployeeIdController::class, 'downloadPng'])
            ->name('employee-ids.download.png');
    });

    // Student IDs
    Route::prefix('modules/academic')->name('modules.academic.')->middleware([
        'auth',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        Route::get('student-ids', [\App\Http\Controllers\Tenant\Modules\Academic\StudentIdController::class, 'index'])
            ->name('student-ids.index');
        Route::post('student-ids/generate', [\App\Http\Controllers\Tenant\Modules\Academic\StudentIdController::class, 'generate'])
            ->name('student-ids.generate');
        Route::post('student-ids/preview', [\App\Http\Controllers\Tenant\Modules\Academic\StudentIdController::class, 'preview'])
            ->name('student-ids.preview');
        Route::get('student-ids/download/svg', [\App\Http\Controllers\Tenant\Modules\Academic\StudentIdController::class, 'downloadSvg'])
            ->name('student-ids.download.svg');
        Route::get('student-ids/download/png', [\App\Http\Controllers\Tenant\Modules\Academic\StudentIdController::class, 'downloadPng'])
            ->name('student-ids.download.png');
    });

    // Settings Routes
    Route::prefix('settings')->name('tenant.settings.')->middleware([
        'auth',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Admin Settings
        Route::get('/admin', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'index'])
            ->name('admin.index')
            ->middleware('role:Admin|admin');
        Route::put('/admin', [\App\Http\Controllers\Tenant\Admin\SettingsController::class, 'update'])
            ->name('admin.update')
            ->middleware('role:Admin|admin');

        // Enhanced Settings Routes (Admin only)
        Route::middleware('role:Admin|admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
            Route::get('/general', [\App\Http\Controllers\Admin\SettingsController::class, 'general'])->name('general');
            Route::match(['PUT', 'POST'], '/general', [\App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('general.update');
            Route::get('/academic', [\App\Http\Controllers\Admin\SettingsController::class, 'academic'])->name('academic');
            Route::match(['PUT', 'POST'], '/academic', [\App\Http\Controllers\Admin\SettingsController::class, 'updateAcademic'])->name('academic.update');
            Route::get('/system', [\App\Http\Controllers\Admin\SettingsController::class, 'system'])->name('system');
            Route::match(['PUT', 'POST'], '/system', [\App\Http\Controllers\Admin\SettingsController::class, 'updateSystem'])->name('system.update');
            Route::get('/finance', [\App\Http\Controllers\Admin\SettingsController::class, 'finance'])->name('finance');
            Route::match(['PUT', 'POST'], '/finance', [\App\Http\Controllers\Admin\SettingsController::class, 'updateFinance'])->name('finance.update');
            Route::get('/permissions', [\App\Http\Controllers\Admin\SettingsController::class, 'permissions'])->name('permissions');
            Route::match(['PUT', 'POST'], '/permissions', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePermissions'])->name('permissions.update');
            // Role Management Routes
            Route::post('/roles', [\App\Http\Controllers\Admin\SettingsController::class, 'storeRole'])->name('roles.store');
            Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\SettingsController::class, 'destroyRole'])->name('roles.destroy');
            Route::get('/roles/{role}/permissions', [\App\Http\Controllers\Admin\SettingsController::class, 'getRolePermissions'])->name('roles.permissions');
            Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Admin\SettingsController::class, 'syncRolePermissions'])->name('roles.permissions.sync');
            Route::post('/roles/bulk-assign', [\App\Http\Controllers\Admin\SettingsController::class, 'bulkAssignRole'])->name('roles.bulkAssign');
            // Email Settings Routes
            Route::get('/email', [\App\Http\Controllers\Admin\SettingsController::class, 'email'])->name('email');
            Route::match(['PUT', 'POST'], '/email', [\App\Http\Controllers\Admin\SettingsController::class, 'updateEmail'])->name('email.update');
            Route::get('/test-email', [\App\Http\Controllers\Admin\SettingsController::class, 'testEmail'])->name('test-email');
            Route::post('/test-email', [\App\Http\Controllers\Admin\SettingsController::class, 'sendTestEmail'])->name('test-email.send');
            // Cache Management
            Route::post('/clear-cache', [\App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');
            // Backup Management
            Route::post('/backup', [\App\Http\Controllers\Admin\SettingsController::class, 'createBackup'])->name('backup');
            Route::get('/backups/list', [\App\Http\Controllers\Admin\SettingsController::class, 'listBackups'])->name('backups.list');
            // Currency Management
            Route::resource('currencies', \App\Http\Controllers\Admin\CurrencyController::class);
            Route::post('/currencies/{currency}/set-default', [\App\Http\Controllers\Admin\CurrencyController::class, 'setDefault'])->name('currencies.set-default');
            Route::post('/currencies/{currency}/toggle', [\App\Http\Controllers\Admin\CurrencyController::class, 'toggle'])->name('currencies.toggle');
            Route::post('/refresh-exchange-rates', [\App\Http\Controllers\Admin\SettingsController::class, 'refreshExchangeRates'])->name('refresh-exchange-rates');
            // Additional Permission Management
            Route::post('/permissions/sync-registry', [\App\Http\Controllers\Admin\SettingsController::class, 'syncPermissionsRegistry'])->name('permissions.sync-registry');
            // User Search & Assignment Routes
            Route::get('/users/search', [\App\Http\Controllers\Admin\SettingsController::class, 'usersSearch'])->name('users.search');
            Route::get('/users/{user}/assignments', [\App\Http\Controllers\Admin\SettingsController::class, 'getUserAssignments'])->name('users.assignments.get');
            Route::post('/users/{user}/assignments', [\App\Http\Controllers\Admin\SettingsController::class, 'syncUserAssignments'])->name('users.assignments.sync');
        });

        // Payment Gateway Configuration (Admin only)
        Route::middleware('role:Admin')->prefix('payment-gateways')->name('payment-gateways.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Settings\PaymentGatewaysController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\Settings\PaymentGatewaysController::class, 'store'])->name('store');
            Route::post('/{gateway}/toggle', [\App\Http\Controllers\Tenant\Settings\PaymentGatewaysController::class, 'toggle'])->name('toggle');
            Route::delete('/{gateway}', [\App\Http\Controllers\Tenant\Settings\PaymentGatewaysController::class, 'destroy'])->name('destroy');
        });

        // Staff Settings
        Route::get('/staff', [\App\Http\Controllers\Tenant\Staff\SettingsController::class, 'index'])
            ->name('staff.index')
            ->middleware('role:Staff');
        Route::put('/staff', [\App\Http\Controllers\Tenant\Staff\SettingsController::class, 'update'])
            ->name('staff.update')
            ->middleware('role:Staff');

        // Student Settings
        Route::get('/student', [\App\Http\Controllers\Tenant\Student\SettingsController::class, 'index'])
            ->name('student.index')
            ->middleware('role:Student');
        Route::put('/student', [\App\Http\Controllers\Tenant\Student\SettingsController::class, 'update'])
            ->name('student.update')
            ->middleware('role:Student');

        // Parent Settings
        Route::get('/parent', [\App\Http\Controllers\Tenant\Parent\SettingsController::class, 'index'])
            ->name('parent.index')
            ->middleware('role:Parent');
        Route::put('/parent', [\App\Http\Controllers\Tenant\Parent\SettingsController::class, 'update'])
            ->name('parent.update')
            ->middleware('role:Parent');
    });

    // Reports Routes
    Route::prefix('reports')->name('tenant.reports.')->middleware([
        'auth',
        'role:Admin',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
        Route::get('/academic', [\App\Http\Controllers\Admin\ReportsController::class, 'academic'])->name('academic');
        Route::get('/attendance', [\App\Http\Controllers\Admin\ReportsController::class, 'attendance'])->name('attendance');
        Route::get('/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'financial'])->name('financial');
        Route::get('/enrollment', [\App\Http\Controllers\Admin\ReportsController::class, 'enrollment'])->name('enrollment');
        Route::get('/late-submissions', [\App\Http\Controllers\Admin\ReportsController::class, 'lateSubmissions'])->name('late-submissions');
        Route::get('/late-submissions/export/csv', [\App\Http\Controllers\Admin\ReportsController::class, 'exportLateSubmissionsCsv'])->name('late-submissions.export.csv');

        // Export routes
        Route::get('/export-pdf', [\App\Http\Controllers\Admin\ReportsController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [\App\Http\Controllers\Admin\ReportsController::class, 'exportExcel'])->name('export-excel');
        Route::post('/generate', [\App\Http\Controllers\Admin\ReportsController::class, 'generate'])->name('generate');
        Route::get('/download/{reportLog}', [\App\Http\Controllers\Admin\ReportsController::class, 'download'])->name('download');

        // Report Cards
        Route::prefix('report-cards')->name('report-cards.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'reportCards'])->name('index');
            Route::post('/export-student', [\App\Http\Controllers\Admin\ReportsController::class, 'exportStudentReportCard'])->name('export-student');
            Route::post('/export-class', [\App\Http\Controllers\Admin\ReportsController::class, 'exportClassReportCards'])->name('export-class');
        });
    });

    // Admin Attendance Routes
    Route::prefix('admin')->name('admin.')->middleware([
        'auth',
        'role:Admin|admin',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // User Approval Management
        Route::prefix('user-approvals')->name('user-approvals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\UserApprovalController::class, 'index'])->name('index');
            // Bulk actions must come BEFORE parameterized routes
            Route::post('/bulk-approve', [\App\Http\Controllers\Admin\UserApprovalController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [\App\Http\Controllers\Admin\UserApprovalController::class, 'bulkReject'])->name('bulk-reject');
            // Individual user actions
            Route::get('/{user}', [\App\Http\Controllers\Admin\UserApprovalController::class, 'show'])->name('show');
            Route::post('/{user}/approve', [\App\Http\Controllers\Admin\UserApprovalController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [\App\Http\Controllers\Admin\UserApprovalController::class, 'reject'])->name('reject');
                // New management actions for approved users
                Route::post('/{user}/employment', [\App\Http\Controllers\Admin\UserApprovalController::class, 'updateEmployment'])->name('employment');
                Route::post('/{user}/student-enrollment', [\App\Http\Controllers\Admin\UserApprovalController::class, 'updateStudentEnrollment'])->name('student-enrollment');
                Route::post('/{user}/suspend', [\App\Http\Controllers\Admin\UserApprovalController::class, 'suspend'])->name('suspend');
                Route::post('/{user}/reinstate', [\App\Http\Controllers\Admin\UserApprovalController::class, 'reinstate'])->name('reinstate');
                Route::post('/{user}/expel', [\App\Http\Controllers\Admin\UserApprovalController::class, 'expel'])->name('expel');
        });

        // Attendance (admin)
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/mark', [\App\Http\Controllers\Admin\AttendanceController::class, 'mark'])->name('attendance.mark');
        Route::post('/attendance/export', [\App\Http\Controllers\Admin\AttendanceController::class, 'export'])->name('attendance.export');
        Route::get('/attendance/statistics', [\App\Http\Controllers\Admin\AttendanceController::class, 'statistics'])->name('attendance.statistics');
        Route::get('/attendance/student/{student}/history', [\App\Http\Controllers\Admin\AttendanceController::class, 'studentHistory'])->name('attendance.student.history');
        Route::post('/attendance/notify-absent', [\App\Http\Controllers\Admin\AttendanceController::class, 'notifyAbsent'])->name('attendance.notify.absent');
        Route::get('/attendance/comparative-stats', [\App\Http\Controllers\Admin\AttendanceController::class, 'comparativeStats'])->name('attendance.comparative.stats');

        // School-wide Attendance Analytics
        Route::get('/attendance/analytics', [\App\Http\Controllers\Admin\AttendanceController::class, 'analytics'])->name('attendance.analytics');
        Route::get('/attendance/trends', [\App\Http\Controllers\Admin\AttendanceController::class, 'trends'])->name('attendance.trends');
        Route::get('/attendance/defaulters', [\App\Http\Controllers\Admin\AttendanceController::class, 'defaulters'])->name('attendance.defaulters');
        Route::get('/attendance/compliance', [\App\Http\Controllers\Admin\AttendanceController::class, 'compliance'])->name('attendance.compliance');
        Route::get('/attendance/bulk-reports', [\App\Http\Controllers\Admin\AttendanceController::class, 'bulkReports'])->name('attendance.bulk-reports');
        Route::post('/attendance/bulk-export', [\App\Http\Controllers\Admin\AttendanceController::class, 'exportBulkReport'])->name('attendance.bulk-export');

        // Staff Attendance
        Route::get('/staff-attendance', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'index'])->name('staff-attendance.index');
        Route::post('/staff-attendance/mark', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'mark'])->name('staff-attendance.mark');
        Route::get('/staff-attendance/export-csv', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'exportCsv'])->name('staff-attendance.export-csv');
        Route::get('/staff-attendance/export-pdf', [\App\Http\Controllers\Admin\StaffAttendanceController::class, 'exportPdf'])->name('staff-attendance.export-pdf');

        // Exam Attendance
        Route::get('/exam-attendance', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'index'])->name('exam-attendance.index');
        Route::get('/exam-attendance/create', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'create'])->name('exam-attendance.create');
        Route::post('/exam-attendance', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'store'])->name('exam-attendance.store');
        Route::get('/exam-attendance/{session}', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'show'])->name('exam-attendance.show');
        Route::post('/exam-attendance/{session}/mark', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'mark'])->name('exam-attendance.mark');
        Route::get('/exam-attendance/{session}/export-csv', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'exportCsv'])->name('exam-attendance.export-csv');
        Route::get('/exam-attendance/{session}/export-pdf', [\App\Http\Controllers\Admin\ExamAttendanceController::class, 'exportPdf'])->name('exam-attendance.export-pdf');

        // Attendance Kiosk
        Route::get('/attendance/kiosk', [\App\Http\Controllers\Admin\AttendanceKioskController::class, 'index'])->name('attendance.kiosk');
        Route::get('/attendance/kiosk/student-classes', [\App\Http\Controllers\Admin\AttendanceKioskController::class, 'studentClasses'])->name('attendance.kiosk.student-classes');
        Route::post('/attendance/kiosk/punch', [\App\Http\Controllers\Admin\AttendanceKioskController::class, 'punch'])->name('attendance.kiosk.punch');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'store'])->name('store');
            Route::get('/{notification}', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'show'])->name('show');
            Route::get('/{notification}/edit', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'edit'])->name('edit');
            Route::put('/{notification}', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'update'])->name('update');
            Route::delete('/{notification}', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'destroy'])->name('destroy');
            Route::post('/{notification}/send', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'send'])->name('send');
            Route::get('/logs', [\App\Http\Controllers\Tenant\Admin\NotificationsController::class, 'logs'])->name('logs');
        });

        // Messages
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'store'])->name('store');
            Route::get('/{thread}', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'show'])->name('show');
            Route::post('/{thread}/reply', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'reply'])->name('reply');
            Route::post('/{thread}/mark-read', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'markRead'])->name('mark-read');
            Route::delete('/{thread}', [\App\Http\Controllers\Tenant\Admin\MessagesController::class, 'destroy'])->name('destroy');
        });

        // Bookstore Management
        Route::prefix('bookstore')->name('bookstore.')->group(function () {
            // Dashboard
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'index'])->name('index');

            // Inventory Management
            Route::get('/inventory', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'inventory'])->name('inventory');
            Route::post('/books/{book}/stock', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'updateStock'])->name('books.stock');
            Route::post('/books/{book}/featured', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'toggleFeatured'])->name('books.featured');

            // Orders Management
            Route::get('/orders', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'orders'])->name('orders');
            Route::get('/orders/{order}', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'orderShow'])->name('orders.show');
            Route::post('/orders/{order}/status', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'updateOrderStatus'])->name('orders.status');
            Route::post('/orders/{order}/payment', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'updatePaymentStatus'])->name('orders.payment');
            Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Tenant\Admin\BookstoreManagementController::class, 'cancelOrder'])->name('orders.cancel');
        });

        // User Password Management (Admin only)
        Route::prefix('users/password')->name('users.password.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'index'])->name('index');
            Route::get('/{user}', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'show'])->name('show');
            Route::put('/{user}', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'reset'])->name('reset');
            Route::post('/{user}/temporary', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'generateTemporary'])->name('temp');
        });

        // Teacher Management (register staff as teachers, assign classes/subjects)
        Route::prefix('academics/teachers')->name('academics.teachers.')->group(function () {
            Route::post('/{user}/register', [\App\Http\Controllers\Tenant\Admin\TeacherManagementController::class, 'register'])->name('register');
            Route::post('/{user}/assign-class', [\App\Http\Controllers\Tenant\Admin\TeacherManagementController::class, 'assignClass'])->name('assign_class');
            Route::post('/{user}/assign-subjects', [\App\Http\Controllers\Tenant\Admin\TeacherManagementController::class, 'assignSubjects'])->name('assign_subjects');
        });

        // Student Enrollment (assign class/stream from Admin panel)
        Route::prefix('academics/students')->name('academics.students.')->group(function () {
            Route::post('/{user}/enroll', [\App\Http\Controllers\Tenant\Admin\TeacherManagementController::class, 'enrollStudent'])->name('enroll');
        });
    });

    // Profile Routes
    Route::prefix('profile')->name('tenant.profile.')->middleware([
        'auth',
        \App\Http\Middleware\PreventBackHistory::class,
    ])->group(function () {
        // Universal Password Change Route (for all authenticated users)
        Route::get('/change-password', function () {
            $user = auth()->user();
            $roles = $user->getRoleNames();

            if ($roles->contains('Admin') || $roles->contains('admin')) {
                return app(\App\Http\Controllers\Tenant\Admin\ProfileController::class)->changePassword();
            } elseif ($roles->contains('Teacher') || $roles->contains('teacher')) {
                return app(\App\Http\Controllers\Tenant\Teacher\ProfileController::class)->changePassword();
            } elseif ($roles->contains('Staff') || $roles->contains('staff')) {
                return app(\App\Http\Controllers\Tenant\Staff\ProfileController::class)->changePassword();
            } elseif ($roles->contains('Student') || $roles->contains('student')) {
                return app(\App\Http\Controllers\Tenant\Student\ProfileController::class)->changePassword();
            } elseif ($roles->contains('Parent') || $roles->contains('parent')) {
                return app(\App\Http\Controllers\Tenant\Parent\ProfileController::class)->changePassword();
            }

            abort(403, 'Unauthorized');
        })->name('password.change');

        Route::put('/password', function (\Illuminate\Http\Request $request) {
            $user = auth()->user();
            $roles = $user->getRoleNames();

            if ($roles->contains('Admin') || $roles->contains('admin')) {
                return app(\App\Http\Controllers\Tenant\Admin\ProfileController::class)->updatePassword($request);
            } elseif ($roles->contains('Teacher') || $roles->contains('teacher')) {
                return app(\App\Http\Controllers\Tenant\Teacher\ProfileController::class)->updatePassword($request);
            } elseif ($roles->contains('Staff') || $roles->contains('staff')) {
                return app(\App\Http\Controllers\Tenant\Staff\ProfileController::class)->updatePassword($request);
            } elseif ($roles->contains('Student') || $roles->contains('student')) {
                return app(\App\Http\Controllers\Tenant\Student\ProfileController::class)->updatePassword($request);
            } elseif ($roles->contains('Parent') || $roles->contains('parent')) {
                return app(\App\Http\Controllers\Tenant\Parent\ProfileController::class)->updatePassword($request);
            }

            abort(403, 'Unauthorized');
        })->name('password.update');

        // Admin Profile
        Route::get('/admin', [\App\Http\Controllers\Tenant\Admin\ProfileController::class, 'index'])
            ->name('admin.index')
            ->middleware('role:Admin');
        Route::put('/admin', [\App\Http\Controllers\Tenant\Admin\ProfileController::class, 'update'])
            ->name('admin.update')
            ->middleware('role:Admin');

        // Staff Profile
        Route::get('/staff', [\App\Http\Controllers\Tenant\Staff\ProfileController::class, 'index'])
            ->name('staff.index')
            ->middleware('role:Staff');
        Route::put('/staff', [\App\Http\Controllers\Tenant\Staff\ProfileController::class, 'update'])
            ->name('staff.update')
            ->middleware('role:Staff');

        // Student Profile
        Route::get('/student', [\App\Http\Controllers\Tenant\Student\ProfileController::class, 'index'])
            ->name('student.index')
            ->middleware('role:Student');
        Route::put('/student', [\App\Http\Controllers\Tenant\Student\ProfileController::class, 'update'])
            ->name('student.update')
            ->middleware('role:Student');

        // Parent Profile
        Route::get('/parent', [\App\Http\Controllers\Tenant\Parent\ProfileController::class, 'index'])
            ->name('parent.index')
            ->middleware('role:Parent');
        Route::put('/parent', [\App\Http\Controllers\Tenant\Parent\ProfileController::class, 'update'])
            ->name('parent.update')
            ->middleware('role:Parent');
        Route::get('/parent/settings', [\App\Http\Controllers\Tenant\Parent\ProfileController::class, 'settings'])
            ->name('parent.settings')
            ->middleware('role:Parent');
        Route::put('/parent/settings', [\App\Http\Controllers\Tenant\Parent\ProfileController::class, 'updateSettings'])
            ->name('parent.updateSettings')
            ->middleware('role:Parent');
        Route::get('/parent/change-password', [\App\Http\Controllers\Tenant\Parent\ProfileController::class, 'changePassword'])
            ->name('parent.changePassword')
            ->middleware('role:Parent');
        Route::put('/parent/change-password', [\App\Http\Controllers\Tenant\Parent\ProfileController::class, 'updatePassword'])
            ->name('parent.updatePassword')
            ->middleware('role:Parent');
    });
});
