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

        // Check for Admin role (case-insensitive)
        if ($user && ($user->hasRole('Admin') || $user->hasRole('admin'))) {
            return redirect()->route('tenant.admin');
        }
        
        // Check for Staff/Teacher role (case-insensitive)
        if ($user && ($user->hasRole('Staff') || $user->hasRole('staff') || $user->hasRole('Teacher') || $user->hasRole('teacher'))) {
            return redirect()->route('tenant.teacher.dashboard');
        }
        
        // Check for Student role (case-insensitive)
        if ($user && ($user->hasRole('Student') || $user->hasRole('student'))) {
            return redirect()->route('tenant.student');
        }
        
        // Check for Parent role (case-insensitive)
        if ($user && ($user->hasRole('Parent') || $user->hasRole('parent'))) {
            return redirect()->route('tenant.parent');
        }

        // Default to student dashboard
        return redirect()->route('tenant.student');
    }
}
