<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->google2fa = app(Google2FA::class);
    }

    /**
     * Show the two-factor challenge page
     */
    public function show()
    {
        // Check if user is in 2FA challenge state
        if (!session()->has('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the two-factor code
     */
    public function verify(Request $request)
    {
        // Check if user is in 2FA challenge state
        if (!session()->has('two_factor_user_id')) {
            return redirect()->route('login');
        }

        $userId = session('two_factor_user_id');
        $user = User::findOrFail($userId);

        // Rate limiting
        $throttleKey = 'two-factor-verify:' . $user->id . ':' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, config('google2fa.throttle.max_attempts', 5))) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Log failed attempt
            SecurityAuditLog::log(
                'two_factor_rate_limit',
                'Too many two-factor verification attempts',
                $user,
                ['ip' => $request->ip()]
            );

            throw ValidationException::withMessages([
                'code' => [__('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])],
            ]);
        }

        // Determine if it's a recovery code or OTP
        $input = $request->input('code');
        
        if ($this->isRecoveryCode($input)) {
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
        $request->validate([
            'code' => 'required|string|digits:6',
        ]);

        // Get user's secret
        $secret = decrypt($user->two_factor_secret);

        // Verify code
        $valid = $this->google2fa->verifyKey($secret, $code, config('google2fa.window', 4));

        if (!$valid) {
            RateLimiter::hit($throttleKey, config('google2fa.throttle.decay_minutes', 1) * 60);

            // Log failed attempt
            SecurityAuditLog::log(
                'two_factor_failed',
                'Failed two-factor authentication attempt',
                $user,
                ['ip' => $request->ip(), 'type' => 'otp']
            );

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
        $request->validate([
            'code' => 'required|string',
        ]);

        // Get recovery codes
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        // Normalize input
        $code = strtoupper(str_replace(' ', '', $code));

        // Check if code exists
        $key = array_search($code, $recoveryCodes);

        if ($key === false) {
            RateLimiter::hit($throttleKey, config('google2fa.throttle.decay_minutes', 1) * 60);

            // Log failed attempt
            SecurityAuditLog::log(
                'two_factor_failed',
                'Failed two-factor authentication attempt with recovery code',
                $user,
                ['ip' => $request->ip(), 'type' => 'recovery']
            );

            throw ValidationException::withMessages([
                'code' => [__('The recovery code is invalid.')],
            ]);
        }

        // Remove used recovery code
        unset($recoveryCodes[$key]);
        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
        ]);

        // Log recovery code usage
        SecurityAuditLog::log(
            'recovery_code_used',
            'User used a two-factor recovery code',
            $user,
            ['ip' => $request->ip(), 'remaining_codes' => count($recoveryCodes)]
        );

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
        auth()->login($user, session('two_factor_remember', false));

        // Mark session as 2FA verified
        session([
            'two_factor_verified' => true,
            'two_factor_verified_at' => now(),
        ]);

        // Clear 2FA challenge session data
        session()->forget(['two_factor_user_id', 'two_factor_remember']);

        // Regenerate session
        $request->session()->regenerate();

        // Log successful authentication
        SecurityAuditLog::log(
            'two_factor_success',
            'Two-factor authentication successful',
            $user,
            [
                'ip' => $request->ip(),
                'used_recovery_code' => $usedRecoveryCode,
            ]
        );

        // Redirect based on role
        if ($user->hasRole('Admin')) {
            return redirect()->intended(route('tenant.admin'));
        } elseif ($user->hasRole('Staff')) {
            return redirect()->intended(route('tenant.teacher.dashboard'));
        } elseif ($user->hasRole('Student')) {
            return redirect()->intended(route('tenant.student.dashboard'));
        }

        return redirect()->intended('/');
    }

    /**
     * Check if input is a recovery code format
     */
    protected function isRecoveryCode(string $input): bool
    {
        // Recovery codes contain a dash or are longer than 6 characters
        return strlen($input) > 6 || str_contains($input, '-');
    }
}
