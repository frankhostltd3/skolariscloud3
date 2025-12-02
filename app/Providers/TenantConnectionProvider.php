<?php

namespace App\Providers;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

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
            Log::debug("TenantConnectionProvider: Central domain detected: {$host}");
            return;
        }

        // Extract subdomain
        $subdomain = $this->extractSubdomain($host);

        if (empty($subdomain)) {
            Log::debug("TenantConnectionProvider: No subdomain found for host: {$host}");
            return;
        }

        // Find school by subdomain using central connection
        $centralConnection = config('tenant.central_connection', config('database.default'));

        $school = School::on($centralConnection)
            ->where('subdomain', $subdomain)
            ->first();

        if (!$school) {
            Log::warning("TenantConnectionProvider: School not found for subdomain: {$subdomain}");
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
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($school->id);

            // Reset the permission registrar to ensure it uses the new cache key and reloads permissions
            // This fixes the "PermissionDoesNotExist" error by forcing a fresh load from the tenant DB
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }

        Log::debug("TenantConnectionProvider: Connected to tenant database for school: {$school->name} (ID: {$school->id})");
    }

    /**
     * Check if the host is a central domain (no subdomain).
     * This method is flexible and works with any domain configured in .env
     */
    protected function isCentralDomain(string $host): bool
    {
        // Remove port if present (e.g., localhost:8000 -> localhost)
        $host = preg_replace('/:\d+$/', '', $host);
        $host = strtolower(trim($host));

        // Always treat these as central
        $localhostVariants = ['localhost', '127.0.0.1', '::1'];
        if (in_array($host, $localhostVariants, true)) {
            return true;
        }

        // Get the primary central domain from env
        $centralDomain = $this->getPrimaryCentralDomain();

        // Exact match with central domain (with or without www)
        $hostWithoutWww = preg_replace('/^www\./', '', $host);
        $centralWithoutWww = preg_replace('/^www\./', '', $centralDomain);

        if ($host === $centralDomain || $hostWithoutWww === $centralWithoutWww) {
            return true;
        }

        // Check against all configured central domains
        $centralDomains = config('tenant.central_domains', []);
        foreach ($centralDomains as $domain) {
            $domain = strtolower(trim($domain));
            if ($host === $domain || $hostWithoutWww === preg_replace('/^www\./', '', $domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the primary central domain from configuration.
     */
    protected function getPrimaryCentralDomain(): string
    {
        // Try CENTRAL_DOMAIN env first
        $centralDomain = env('CENTRAL_DOMAIN');

        if (empty($centralDomain)) {
            // Fall back to parsing APP_URL
            $appUrl = config('app.url', 'http://localhost');
            $centralDomain = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
        }

        // Remove port if present
        $centralDomain = preg_replace('/:\d+$/', '', $centralDomain);

        return strtolower(trim($centralDomain));
    }

    /**
     * Extract subdomain from host.
     */
    protected function extractSubdomain(string $host): ?string
    {
        // Remove port if present
        $host = preg_replace('/:\d+$/', '', $host);
        $host = strtolower(trim($host));

        // Get the primary central domain
        $centralDomain = $this->getPrimaryCentralDomain();

        // Pattern: subdomain.centraldomain.com
        $centralDomainEscaped = preg_quote($centralDomain, '/');
        if (preg_match('/^([a-z0-9][a-z0-9-]*)\.' . $centralDomainEscaped . '$/i', $host, $matches)) {
            $subdomain = $matches[1];
            // Don't treat 'www' as a subdomain
            if ($subdomain !== 'www') {
                return $subdomain;
            }
        }

        // Check all configured central domains
        $centralDomains = config('tenant.central_domains', []);
        foreach ($centralDomains as $domain) {
            $domain = strtolower(trim($domain));
            $domainEscaped = preg_quote($domain, '/');
            if (preg_match('/^([a-z0-9][a-z0-9-]*)\.' . $domainEscaped . '$/i', $host, $matches)) {
                $subdomain = $matches[1];
                if ($subdomain !== 'www') {
                    return $subdomain;
                }
            }
        }

        // Fallback: check for localhost pattern (e.g., school.localhost)
        if (preg_match('/^([a-z0-9][a-z0-9-]*)\.localhost$/i', $host, $matches)) {
            return $matches[1];
        }

        // Fallback: check for 127.0.0.1 pattern (e.g., school.127.0.0.1)
        if (preg_match('/^([a-z0-9][a-z0-9-]*)\.127\.0\.0\.1$/i', $host, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
