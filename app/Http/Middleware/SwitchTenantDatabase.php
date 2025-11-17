<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SwitchTenantDatabase
{
    public function __construct(private TenantDatabaseManager $manager)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $school = $request->attributes->get('currentSchool');

        if (! $school instanceof School && app()->bound('currentSchool')) {
            $bound = app('currentSchool');
            if ($bound instanceof School) {
                $school = $bound;
            }
        }

        // Fallback: check session for tenant school ID
        if (! $school instanceof School && $request->hasSession()) {
            $sessionSchoolId = $request->session()->get('tenant_school_id');

            if ($sessionSchoolId) {
                // Use central connection to fetch school
                $sessionSchool = School::on('mysql')->find($sessionSchoolId);

                if ($sessionSchool instanceof School) {
                    $school = $sessionSchool;
                    $request->attributes->set('currentSchool', $sessionSchool);
                    app()->instance('currentSchool', $sessionSchool);
                }
            }
        }

        // Connect to tenant database (and keep it connected for the entire request)
        $this->manager->connect($school);

        // Process the request
        return $next($request);
    }
}
