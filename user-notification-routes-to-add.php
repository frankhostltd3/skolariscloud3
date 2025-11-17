// ============================================
// USER MANAGEMENT ROUTES - Add to tenant.php
// ============================================

// User Management Section (Admin Dashboard)
Route::prefix('tenant/admin')->name('tenant.admin.')->middleware(['auth', 'role:Admin|Staff', 'approved'])->group(function () {
    
    // Admins Management (Only Admins can manage other admins)
    Route::middleware('role:Admin')->group(function () {
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
    
    // User Password Management
    Route::get('/users/password-management', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'index'])
        ->name('users.password-management');
    Route::get('/users/{user}/reset-password', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'show'])
        ->name('users.reset-password');
    Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Tenant\Admin\UserPasswordController::class, 'reset'])
        ->name('users.reset-password.store');
        
    // Notifications Management
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
});

// ============================================
// STUDENT NOTIFICATION ROUTES
// ============================================
Route::prefix('tenant/student')->name('tenant.student.')->middleware(['auth', 'role:Student', 'approved'])->group(function () {
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Tenant\Student\NotificationsController::class, 'getUnreadCount'])->name('notifications.unreadCount');
});

// ============================================
// TWO-FACTOR AUTHENTICATION ROUTES
// ============================================
Route::middleware(['auth'])->prefix('tenant/user/2fa')->name('tenant.user.2fa.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Tenant\Users\TwoFactorController::class, 'index'])->name('index');
    Route::post('/enable', [\App\Http\Controllers\Tenant\Users\TwoFactorController::class, 'enable'])->name('enable');
    Route::post('/verify', [\App\Http\Controllers\Tenant\Users\TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/disable', [\App\Http\Controllers\Tenant\Users\TwoFactorController::class, 'disable'])->name('disable');
    Route::post('/regenerate', [\App\Http\Controllers\Tenant\Users\TwoFactorController::class, 'regenerate'])->name('regenerate');
});
