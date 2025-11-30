<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLandlordContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set the team ID to 'landlord' for Spatie Permission
        // The database column is varchar(36) NOT NULL, so we must use a string value
        $registrar = app(\Spatie\Permission\PermissionRegistrar::class);
        $registrar->setPermissionsTeamId('landlord');

        // Force clear cache to ensure we use the new team ID for this request
        $registrar->forgetCachedPermissions();

        return $next($request);
    }
}
