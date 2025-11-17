<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>|string|null  $guards
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = $guards === [] ? [null] : $guards;

        foreach ($guards as $guard) {
            if (! Auth::guard($guard)->check()) {
                continue;
            }

            // If we're in a tenant context, send to the tenant dashboard
            if (function_exists('tenant') && tenant()) {
                if (Route::has('tenant.dashboard')) {
                    return redirect()->intended(route('tenant.dashboard'));
                }
                return redirect()->intended('/app');
            }

            // Otherwise, central landlord dashboard if available
            if (Route::has('landlord.dashboard')) {
                return redirect()->intended(route('landlord.dashboard'));
            }

            // Fallback to landing
            return redirect()->intended(Route::has('landing.redirect')
                ? route('landing.redirect')
                : '/');
        }

        return $next($request);
    }
}
