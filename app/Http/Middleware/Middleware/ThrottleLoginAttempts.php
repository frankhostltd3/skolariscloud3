<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AccountLockout;
use App\Models\LoginAttempt;
use App\Models\SecurityAuditLog;

class ThrottleLoginAttempts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only throttle login/authentication requests
        if (!$this->isLoginRequest($request)) {
            return $next($request);
        }

        $email = $request->input('email');
        
        if (!$email) {
            return $next($request);
        }

        $tenantId = tenant('id');
        
        // Get security settings
        $maxAttempts = (int) setting('max_login_attempts', 5);
        $lockoutMinutes = (int) setting('lockout_duration', 15);

        // Check if account is locked
        $lockout = AccountLockout::getLockout($email, $tenantId);
        
        if ($lockout && $lockout->isLocked()) {
            $minutesLeft = now()->diffInMinutes($lockout->locked_until, false);
            $minutesLeft = max(1, ceil($minutesLeft));
            
            return response()->json([
                'message' => "Too many failed login attempts. Account locked for {$minutesLeft} more minute(s).",
                'locked_until' => $lockout->locked_until->toIso8601String(),
            ], 429);
        }

        // Process the request
        $response = $next($request);

        // Check if login failed (401 or validation error with password)
        if ($this->isFailedLogin($response)) {
            // Log the failed attempt
            LoginAttempt::logAttempt($email, false);
            
            // Log security audit event
            SecurityAuditLog::logEvent(
                SecurityAuditLog::EVENT_LOGIN_FAILED,
                $email,
                null,
                'Failed login attempt',
                ['status_code' => $response->getStatusCode()],
                SecurityAuditLog::SEVERITY_WARNING
            );
            
            // Get or create lockout record
            $lockout = AccountLockout::getOrCreate($email, $tenantId);
            $lockout->incrementFailedAttempts($maxAttempts, $lockoutMinutes);
            
            // If account was just locked, log it
            if ($lockout->isLocked() && $lockout->failed_attempts >= $maxAttempts) {
                SecurityAuditLog::logEvent(
                    SecurityAuditLog::EVENT_ACCOUNT_LOCKED,
                    $email,
                    null,
                    "Account locked after {$maxAttempts} failed attempts",
                    [
                        'lockout_duration_minutes' => $lockoutMinutes,
                        'locked_until' => $lockout->locked_until->toIso8601String(),
                    ],
                    SecurityAuditLog::SEVERITY_CRITICAL
                );
            }
            
            // Add remaining attempts to response if it's JSON
            if ($response->headers->get('Content-Type') === 'application/json') {
                $data = json_decode($response->getContent(), true);
                $attemptsLeft = max(0, $maxAttempts - $lockout->failed_attempts);
                $data['attempts_remaining'] = $attemptsLeft;
                
                if ($attemptsLeft === 0) {
                    $data['message'] = "Too many failed login attempts. Account locked for {$lockoutMinutes} minutes.";
                } elseif ($attemptsLeft <= 2) {
                    $data['warning'] = "Warning: {$attemptsLeft} login attempt(s) remaining before account lockout.";
                }
                
                $response->setContent(json_encode($data));
            }
        } elseif ($this->isSuccessfulLogin($response)) {
            // Log successful attempt
            LoginAttempt::logAttempt($email, true);
            
            // Log security audit event
            $user = auth()->user();
            SecurityAuditLog::logEvent(
                SecurityAuditLog::EVENT_LOGIN_SUCCESS,
                $email,
                $user?->id,
                'Successful login',
                [],
                SecurityAuditLog::SEVERITY_INFO
            );
            
            // Reset lockout
            $lockout = AccountLockout::getLockout($email, $tenantId);
            if ($lockout && $lockout->failed_attempts > 0) {
                // Log account unlock
                SecurityAuditLog::logEvent(
                    SecurityAuditLog::EVENT_ACCOUNT_UNLOCKED,
                    $email,
                    $user?->id,
                    'Account unlocked after successful login',
                    ['previous_failed_attempts' => $lockout->failed_attempts],
                    SecurityAuditLog::SEVERITY_INFO
                );
                $lockout->reset();
            }
        }

        return $response;
    }

    /**
     * Determine if this is a login request
     */
    protected function isLoginRequest(Request $request): bool
    {
        return $request->isMethod('POST') && (
            $request->is('login') ||
            $request->is('*/login') ||
            $request->is('authenticate') ||
            $request->is('*/authenticate')
        );
    }

    /**
     * Determine if login failed
     */
    protected function isFailedLogin(Response $response): bool
    {
        return in_array($response->getStatusCode(), [401, 422, 423]);
    }

    /**
     * Determine if login succeeded
     */
    protected function isSuccessfulLogin(Response $response): bool
    {
        return in_array($response->getStatusCode(), [200, 302]) && 
               !$response->headers->has('X-Failed-Login');
    }
}
