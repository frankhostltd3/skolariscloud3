<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionInactivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $inactivityTimeout = (int) setting('session_timeout_minutes', 30); // Default 30 minutes

        // Check if user has last_activity_at timestamp
        if ($user->last_activity_at) {
            $lastActivity = $user->last_activity_at;
            $inactiveMinutes = now()->diffInMinutes($lastActivity);

            // If inactive for longer than timeout, log them out
            if ($inactiveMinutes >= $inactivityTimeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your session has expired due to inactivity.',
                        'timeout' => true
                    ], 401);
                }

                return redirect()->route('tenant.login')
                    ->with('warning', "Your session expired after {$inactivityTimeout} minutes of inactivity. Please log in again.");
            }
        }

        // Update last activity timestamp
        $user->last_activity_at = now();
        $user->saveQuietly(); // Use saveQuietly to avoid triggering observers

        return $next($request);
    }
}

