<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IdentifySchoolFromHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomain = config('tenancy.central_domain');
        $host = $request->getHost();

        $school = $this->resolveSchool($host, $centralDomain);

        if ($school) {
            app()->instance('currentSchool', $school);
            $request->attributes->set('currentSchool', $school);
        } else {
            app()->forgetInstance('currentSchool');
            $request->attributes->set('currentSchool', null);
        }

        View::share('currentSchool', $school);

        return $next($request);
    }

    private function resolveSchool(string $host, ?string $centralDomain): ?School
    {
        $query = School::query();

        $query->where('domain', $host);

        if ($centralDomain) {
            $trimmedCentral = ltrim($centralDomain, '.');

            if ($host === $trimmedCentral || $host === 'www.' . $trimmedCentral) {
                return null;
            }

            if (Str::endsWith($host, '.' . $trimmedCentral)) {
                $subdomain = Str::before($host, '.' . $trimmedCentral);
                $query->orWhere('subdomain', $subdomain);
            }
        }

        return $query->first();
    }
}
