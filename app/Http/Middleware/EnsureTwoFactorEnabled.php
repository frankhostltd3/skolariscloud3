<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip for 2FA routes to avoid redirect loop
        if ($request->is('security/two-factor*') || $request->is('logout')) {
            return $next($request);
        }

        // Check if 2FA is required by school settings
        $twoFactorRequired = (bool) setting('enable_two_factor_auth', false);

        // If 2FA is required and user hasn't confirmed it yet, redirect to setup
        if ($twoFactorRequired && !$user->two_factor_confirmed_at) {
            return redirect()->route('two-factor.show')
                ->with('error', 'Two-factor authentication is required. Please set it up to continue.');
        }

        return $next($request);
    }
}
