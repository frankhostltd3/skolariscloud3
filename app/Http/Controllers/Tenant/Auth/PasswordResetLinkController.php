<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('tenant.auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => ['required','email']]);

        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Log password reset request
            SecurityAuditLog::logEvent(
                SecurityAuditLog::EVENT_PASSWORD_RESET_REQUESTED,
                $request->input('email'),
                null,
                'Password reset link requested',
                [],
                SecurityAuditLog::SEVERITY_INFO
            );
            
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
