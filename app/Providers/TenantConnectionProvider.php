<?php

namespace App\Providers;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use App\Support\CentralDomain;
use Illuminate\Support\ServiceProvider;

class TenantConnectionProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * This runs VERY early in the request lifecycle, before middleware.
     */
    public function boot(): void
    {
        // Only set up tenant connection if we're in a web context
        if (!$this->app->runningInConsole() && !$this->app->runningUnitTests()) {
            $this->setupTenantConnection();
        }
    }

    /**
     * Set up the tenant database connection based on the current hostname.
     */
    protected function setupTenantConnection(): void
    {
        $host = request()->getHost();

        // Check if this is a central domain (no subdomain)
        if ($this->isCentralDomain($host)) {
            // Central domain - no tenant connection needed
            return;
        }

        // Extract subdomain
        $subdomain = $this->extractSubdomain($host);

        if (empty($subdomain)) {
            return;
        }

        // Find school by subdomain using central connection
        $centralConnection = config('database.central_connection', config('database.default'));

        $school = School::on($centralConnection)
            ->where('subdomain', $subdomain)
            ->first();

        if (!$school) {
            return;
        }

        // Connect to tenant database
        $manager = app(TenantDatabaseManager::class);
        $manager->connect($school);

        // Store school in app container for later use
        $this->app->instance('currentSchool', $school);

        // Set unique cache key for Spatie Permissions per tenant
        config(['permission.cache.key' => 'spatie.permission.cache.tenant.' . $school->id]);

        // Set permissions team id for multi-tenant support
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($school->id);

        // Reset the permission registrar to ensure it uses the new cache key and reloads permissions
        // This fixes the "PermissionDoesNotExist" error by forcing a fresh load from the tenant DB
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Check if the host is a central domain (no subdomain).
     */
    protected function isCentralDomain(string $host): bool
    {
        // Remove port if present
        $host = parse_url('http://' . $host, PHP_URL_HOST) ?? $host;
        $host = strtolower($host);

        // Check if it's exactly localhost or 127.0.0.1
        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return true;
        }

        // Get configured central domains
        $centralDomains = config('tenant.central_domains', []);

        foreach ($centralDomains as $centralDomain) {
            if (strtolower($centralDomain) === $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract subdomain from host.
     */
    protected function extractSubdomain(string $host): ?string
    {
        // Remove port if present
        $host = parse_url('http://' . $host, PHP_URL_HOST) ?? $host;

        // Get configured central domains
        $centralDomains = config('tenant.central_domains', []);

        foreach ($centralDomains as $centralDomain) {
            $pattern = '/^(.+)\.' . preg_quote($centralDomain, '/') . '$/';

            if (preg_match($pattern, $host, $matches)) {
                return $matches[1];
            }
        }

        // Fallback: check for localhost pattern
        if (preg_match('/^(.+)\.localhost$/i', $host, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
