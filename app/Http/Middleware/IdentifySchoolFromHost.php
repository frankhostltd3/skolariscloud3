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
        if (app()->environment('testing')) {
            View::share('currentSchool', null);

            return $next($request);
        }

        $centralDomain = config('tenancy.central_domain');
        $host = $request->getHost();

        $school = $this->resolveSchool($host, $centralDomain);

        if (! $school && $request->hasSession()) {
            $sessionSchoolId = $request->session()->get('tenant_school_id');

            if ($sessionSchoolId) {
                $sessionSchool = School::query()->find($sessionSchoolId);

                if ($sessionSchool instanceof School) {
                    $school = $sessionSchool;
                }
            }
        }

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

        // Normalise common development hostnames (e.g. localhost vs 127.0.0.1)
        $candidateDomains = [$host];
        if ($host === 'localhost') {
            $candidateDomains[] = '127.0.0.1';
        }

        if ($host === '127.0.0.1') {
            $candidateDomains[] = 'localhost';
        }

        if (in_array($host, ['127.0.0.1', 'localhost'], true)) {
            $appUrlHost = parse_url(config('app.url'), PHP_URL_HOST);

            if ($appUrlHost) {
                $candidateDomains[] = $appUrlHost;
            }

            if ($centralDomain) {
                $candidateDomains[] = ltrim($centralDomain, '.');
            }
        }

        // First, always check for domain matches (including local aliases)
        $query->whereIn('domain', array_unique($candidateDomains));

        if ($centralDomain) {
            $trimmedCentral = ltrim($centralDomain, '.');

            // Also check for subdomain-based schools if host is a subdomain
            if (Str::endsWith($host, '.' . $trimmedCentral)) {
                $subdomain = Str::before($host, '.' . $trimmedCentral);
                $query->orWhere('subdomain', $subdomain);
            }
        }

        // Local development support: treat *.localhost (and 127.0.0.1) as subdomains
        if (preg_match('/^(.+)\.(localhost|127\.0\.0\.1)$/i', $host, $matches)) {
            $query->orWhere('subdomain', $matches[1]);
        }

        $school = $query->first();

        // If we found a school by domain or subdomain, return it
        if ($school) {
            return $school;
        }

        // If no school found and host matches central domain, check for a default school
        if ($centralDomain) {
            $trimmedCentral = ltrim($centralDomain, '.');
            if ($host === $trimmedCentral || $host === 'www.' . $trimmedCentral) {
                // Look for a school that uses the central domain as its domain
                return School::where('domain', $trimmedCentral)->first();
            }
        }

        return null;
    }
}
