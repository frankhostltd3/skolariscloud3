<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TwoFactorChallengeController extends Controller
{
    /**
     * Show the two-factor challenge page
     */
    public function show()
    {
        // Check if user is in 2FA challenge state
        if (!session()->has('two_factor_user_id')) {
            return redirect()->route('tenant.login');
        }

        return view('tenant.auth.two-factor-challenge');
    }

    /**
     * Verify the two-factor code
     */
    public function verify(Request $request)
    {
        // Check if user is in 2FA challenge state
        if (!session()->has('two_factor_user_id')) {
            return redirect()->route('tenant.login');
        }

        $userId = session('two_factor_user_id');
        $user = User::findOrFail($userId);

        // Rate limiting
        $throttleKey = 'two-factor-verify:' . $user->id . ':' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'code' => [__('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])],
            ]);
        }

        // Validate code
        $request->validate([
            'code' => 'required|string',
        ]);

        $input = $request->input('code');
        
        // Determine if it's a recovery code or OTP (simple check: recovery codes are longer)
        if (strlen($input) > 6 || str_contains($input, '-')) {
            return $this->verifyRecoveryCode($request, $user, $input, $throttleKey);
        } else {
            return $this->verifyOtp($request, $user, $input, $throttleKey);
        }
    }

    /**
     * Verify OTP code
     */
    protected function verifyOtp(Request $request, User $user, string $code, string $throttleKey)
    {
        // Check if Google2FA is available
        if (!$user->two_factor_secret) {
            throw ValidationException::withMessages([
                'code' => ['Two-factor authentication is not set up.'],
            ]);
        }

        // Get user's secret
        $secret = decrypt($user->two_factor_secret);

        // Verify code using Google2FA
        $google2fa = app(\PragmaRX\Google2FA\Google2FA::class);
        $valid = $google2fa->verifyKey($secret, $code, 4); // 4 = window size

        if (!$valid) {
            RateLimiter::hit($throttleKey, 60); // 1 minute

            throw ValidationException::withMessages([
                'code' => [__('The verification code is invalid.')],
            ]);
        }

        return $this->completeAuthentication($request, $user);
    }

    /**
     * Verify recovery code
     */
    protected function verifyRecoveryCode(Request $request, User $user, string $code, string $throttleKey)
    {
        if (!$user->two_factor_recovery_codes) {
            throw ValidationException::withMessages([
                'code' => ['Recovery codes are not available.'],
            ]);
        }

        // Get recovery codes
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        // Normalize input
        $code = strtoupper(str_replace([' ', '-'], '', $code));

        // Check if code exists
        $found = false;
        $remainingCodes = [];
        
        foreach ($recoveryCodes as $recoveryCode) {
            $normalizedRecoveryCode = strtoupper(str_replace([' ', '-'], '', $recoveryCode));
            if ($normalizedRecoveryCode === $code) {
                $found = true;
            } else {
                $remainingCodes[] = $recoveryCode;
            }
        }

        if (!$found) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'code' => [__('The recovery code is invalid.')],
            ]);
        }

        // Update recovery codes (remove used one)
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($remainingCodes)),
        ]);

        return $this->completeAuthentication($request, $user, true);
    }

    /**
     * Complete the authentication process
     */
    protected function completeAuthentication(Request $request, User $user, bool $usedRecoveryCode = false)
    {
        // Clear rate limiter
        $throttleKey = 'two-factor-verify:' . $user->id . ':' . $request->ip();
        RateLimiter::clear($throttleKey);

        // Log in the user
        Auth::login($user, session('two_factor_remember', false));

        // Update last activity
        $user->update(['last_activity_at' => now()]);

        // Mark session as 2FA verified
        session([
            'two_factor_verified' => true,
            'two_factor_verified_at' => now(),
        ]);

        // Clear 2FA challenge session data
        session()->forget(['two_factor_user_id', 'two_factor_remember']);

        // Regenerate session
        $request->session()->regenerate();

        // Redirect based on role (case-insensitive)
        if ($user->hasAnyRole(['Admin', 'admin'])) {
            return redirect()->intended(route('tenant.admin'));
        } elseif ($user->hasAnyRole(['Staff', 'staff', 'Teacher', 'teacher'])) {
            return redirect()->intended(route('tenant.teacher.dashboard'));
        } elseif ($user->hasAnyRole(['Student', 'student'])) {
            return redirect()->intended(route('tenant.student'));
        } elseif ($user->hasAnyRole(['Parent', 'parent'])) {
            return redirect()->intended(route('tenant.parent'));
        }

        return redirect()->intended(route('tenant.dashboard'));
    }
}
