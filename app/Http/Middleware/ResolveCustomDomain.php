<?php

namespace App\Http\Middleware;

use App\Models\DomainOrder;
use App\Services\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResolveCustomDomain
{
    protected TenantDatabaseManager $tenantDb;

    public function __construct(TenantDatabaseManager $tenantDb)
    {
        $this->tenantDb = $tenantDb;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Skip if it's the main domain
        if ($this->isMainDomain($host)) {
            return $next($request);
        }

        // Try to resolve custom domain
        $schoolData = $this->resolveCustomDomain($host);

        if ($schoolData) {
            try {
                // Connect to tenant database
                $this->tenantDb->connect($schoolData['database']);

                // Store school context
                app()->instance('tenant.school_id', $schoolData['school_id']);
                app()->instance('tenant.subdomain', $schoolData['subdomain']);
                app()->instance('tenant.database', $schoolData['database']);

                Log::debug('Custom domain resolved', [
                    'host' => $host,
                    'school_id' => $schoolData['school_id'],
                    'database' => $schoolData['database'],
                ]);

                return $next($request);
            } catch (\Exception $e) {
                Log::error('Custom domain resolution failed', [
                    'host' => $host,
                    'error' => $e->getMessage(),
                ]);

                return response()->view('errors.domain-not-configured', [], 503);
            }
        }

        // If not a custom domain and not main domain, return 404
        return response()->view('errors.404', [], 404);
    }

    /**
     * Resolve custom domain to school data
     */
    protected function resolveCustomDomain(string $host): ?array
    {
        // Check cache first
        $cacheKey = 'domain:' . $host;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Query database for active domain
        $order = DomainOrder::with('school')
            ->where('routing_active', true)
            ->where('status', 'active')
            ->where(function ($query) use ($host) {
                $query->whereRaw("CONCAT(domain_name, tld) = ?", [$host])
                      ->orWhereRaw("CONCAT('www.', domain_name, tld) = ?", [$host]);
            })
            ->first();

        if ($order && $order->school) {
            $schoolData = [
                'school_id' => $order->school_id,
                'database' => $order->school->database,
                'subdomain' => $order->school->subdomain,
            ];

            // Cache for 1 week
            Cache::put($cacheKey, $schoolData, now()->addWeek());

            return $schoolData;
        }

        return null;
    }

    /**
     * Check if host is main domain
     */
    protected function isMainDomain(string $host): bool
    {
        $mainDomains = [
            'skolariscloud.com',
            'www.skolariscloud.com',
            'localhost',
            '127.0.0.1',
        ];

        // Remove port if present
        $host = explode(':', $host)[0];

        return in_array($host, $mainDomains);
    }
}
