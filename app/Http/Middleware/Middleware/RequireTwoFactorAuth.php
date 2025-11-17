<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Check if 2FA is enabled system-wide
        if (!enable_two_factor_auth()) {
            return $next($request);
        }

        // Check if user has 2FA enabled
        if ($user->hasTwoFactorEnabled()) {
            // Check if session is 2FA verified
            if (!session()->has('two_factor_verified')) {
                // Store intended URL
                session(['url.intended' => $request->url()]);
                
                // Store user ID for challenge
                session(['two_factor_user_id' => $user->id]);
                
                // Logout user (partial authentication)
                auth()->logout();
                
                return redirect()->route('two-factor.challenge');
            }

            // Check if 2FA verification has expired (optional - 12 hours)
            $verifiedAt = session('two_factor_verified_at');
            if ($verifiedAt && now()->diffInHours($verifiedAt) > 12) {
                session()->forget(['two_factor_verified', 'two_factor_verified_at']);
                session(['two_factor_user_id' => $user->id]);
                auth()->logout();
                
                return redirect()->route('two-factor.challenge')
                    ->with('message', __('Your two-factor session has expired. Please verify again.'));
            }
        }

        return $next($request);
    }
}
