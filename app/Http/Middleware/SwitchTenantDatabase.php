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
        // Check if this is a central domain (no subdomain)
        $host = $request->getHost();
        $host = parse_url('http://' . $host, PHP_URL_HOST) ?? $host;
        $host = strtolower($host);

        $centralDomains = config('tenant.central_domains', []);
        $isCentral = in_array($host, ['localhost', '127.0.0.1']) || in_array($host, $centralDomains);

        if ($isCentral) {
            return $next($request);
        }

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
            $centralConnection = config('database.central_connection', config('database.default'));
            $sessionSchool = School::on($centralConnection)->find($sessionSchoolId);

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
