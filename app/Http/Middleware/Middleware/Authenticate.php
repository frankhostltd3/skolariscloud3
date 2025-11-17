<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // If we're in a tenant context, send users to the tenant login
        if (function_exists('tenant') && tenant()) {
            if (Route::has('tenant.login')) {
                return route('tenant.login');
            }
            // Fallback for tenant context if route name isn't bound for some reason
            return '/login';
        }

        // Otherwise, default to landlord/central login
        if (Route::has('landlord.login.show')) {
            return route('landlord.login.show');
        }

        return Route::has('landing.redirect')
            ? route('landing.redirect')
            : '/';
    }
}
