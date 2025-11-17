<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreserveSubdomainContext
{
    /**
     * Handle an incoming request.
     * Stores the current subdomain/school context in the session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the current school from the app container (set by TenantConnectionProvider)
        if (app()->bound('currentSchool')) {
            $school = app('currentSchool');

            if ($school) {
                // Store school information in session
                $request->session()->put('tenant_school_id', $school->id);
                $request->session()->put('tenant_subdomain', $school->subdomain);
                $request->session()->put('tenant_database', $school->database);
            }
        }

        return $next($request);
    }
}
