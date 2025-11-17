<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('tenant.auth.reset-password', [
            'request' => $request,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required','email'],
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
                
                // Log password reset completion
                SecurityAuditLog::logEvent(
                    SecurityAuditLog::EVENT_PASSWORD_RESET_COMPLETED,
                    $user->email,
                    $user->id,
                    'Password reset via email token',
                    [],
                    SecurityAuditLog::SEVERITY_INFO
                );
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('tenant.login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
