<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ThrottlesLogins
{
    /**
     * Determine if the user has too many failed login attempts.
     */
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     */
    protected function incrementLoginAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    /**
     * Clear the login locks for the given user credentials.
     */
    protected function clearLoginAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     */
    protected function fireLockoutEvent(Request $request): void
    {
        event(new \Illuminate\Auth\Events\Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    /**
     * Get the rate limiter instance.
     */
    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow (from settings).
     */
    protected function maxAttempts(): int
    {
        return (int) setting('max_login_attempts', 5);
    }

    /**
     * Get the number of minutes to throttle for (from settings).
     * Returns 0 for 'forever' lockout.
     */
    protected function decayMinutes(): int
    {
        $duration = setting('lockout_duration', 1);

        // Check if lockout is set to 'forever'
        if ($duration === 'forever' || $duration === '0' || $duration === 0) {
            return 525600; // 1 year in minutes (effectively forever)
        }

        return (int) $duration;
    }

    /**
     * Get the number of seconds until the next attempt.
     */
    protected function availableIn(Request $request): int
    {
        return $this->limiter()->availableIn($this->throttleKey($request));
    }

    /**
     * Send the lockout response.
     */
    protected function sendLockoutResponse(Request $request): void
    {
        $seconds = $this->availableIn($request);
        $lockoutDuration = setting('lockout_duration', 1);

        if ($lockoutDuration === 'forever' || $lockoutDuration === '0' || $lockoutDuration === 0) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been locked permanently due to too many failed login attempts. Please contact your administrator.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ]);
    }
}
