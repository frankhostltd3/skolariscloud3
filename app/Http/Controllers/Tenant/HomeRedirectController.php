<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('tenant.login');
        }

        // Check for Admin or Super Admin role
        if ($user->hasRole(['admin', 'super-admin', 'Admin', 'Super-Admin'])) {
            return redirect()->route('tenant.admin');
        }

        // Check for Teacher, Staff, or Head of Department role
        if ($user->hasRole(['teacher', 'staff', 'head-of-department', 'Teacher', 'Staff', 'Head-of-Department'])) {
            return redirect()->route('tenant.teacher.dashboard');
        }

        // Check for Student role
        if ($user->hasRole(['student', 'Student'])) {
            return redirect()->route('tenant.student');
        }

        // Check for Parent role
        if ($user->hasRole(['parent', 'Parent'])) {
            return redirect()->route('tenant.parent');
        }

        // Check for Accountant role
        if ($user->hasRole(['accountant', 'Accountant'])) {
            return redirect()->route('tenant.admin'); // Accountants use admin dashboard
        }

        // Check for Librarian role
        if ($user->hasRole(['librarian', 'Librarian'])) {
            return redirect()->route('tenant.admin'); // Librarians use admin dashboard
        }

        // If user has no roles, check user_type as fallback
        // School registrants typically have user_type ADMIN
        $userType = is_object($user->user_type) ? $user->user_type->value : $user->user_type;
        if ($userType && in_array(strtoupper($userType), ['ADMIN', 'ADMINISTRATOR'])) {
            return redirect()->route('tenant.admin')
                ->with('warning', 'Your account does not have a role assigned. Please contact your administrator to assign you a role.');
        }

        // Default fallback: redirect to login with error message
        Auth::logout();
        return redirect()->route('tenant.login')
            ->with('error', 'Your account does not have the required permissions. Please contact your administrator.');
    }
}
