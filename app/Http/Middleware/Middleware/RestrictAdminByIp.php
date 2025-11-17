<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RestrictAdminByIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if IP whitelisting is enabled
        $ipWhitelistEnabled = (bool) setting('enable_ip_whitelist', false);

        if (!$ipWhitelistEnabled) {
            return $next($request);
        }

        // Get whitelisted IPs from settings
        $whitelistedIps = $this->getWhitelistedIps();

        if (empty($whitelistedIps)) {
            // If no IPs configured, allow access (fail open for initial setup)
            return $next($request);
        }

        $clientIp = $request->ip();

        // Check if client IP is in whitelist
        if (!$this->isIpWhitelisted($clientIp, $whitelistedIps)) {
            // Log unauthorized access attempt
            Log::warning('Admin access blocked - IP not whitelisted', [
                'ip' => $clientIp,
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'user' => auth()->user()?->email ?? 'guest',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. Your IP address is not authorized to access this resource.',
                    'ip' => $clientIp
                ], 403);
            }

            abort(403, 'Access denied. Your IP address is not authorized to access the admin panel.');
        }

        return $next($request);
    }

    /**
     * Get whitelisted IPs from settings
     */
    protected function getWhitelistedIps(): array
    {
        $whitelist = setting('admin_ip_whitelist', '');

        if (empty($whitelist)) {
            return [];
        }

        // Parse comma/newline/semicolon separated IPs
        $ips = preg_split('/[,;\n\r]+/', $whitelist);

        return array_filter(array_map('trim', $ips), function($ip) {
            return !empty($ip);
        });
    }

    /**
     * Check if IP is whitelisted (supports CIDR notation)
     */
    protected function isIpWhitelisted(string $clientIp, array $whitelist): bool
    {
        foreach ($whitelist as $whitelistedIp) {
            // Check for exact match
            if ($clientIp === $whitelistedIp) {
                return true;
            }

            // Check for CIDR notation (e.g., 192.168.1.0/24)
            if (strpos($whitelistedIp, '/') !== false) {
                if ($this->ipInCidr($clientIp, $whitelistedIp)) {
                    return true;
                }
            }

            // Check for wildcard (e.g., 192.168.1.*)
            if (strpos($whitelistedIp, '*') !== false) {
                $pattern = str_replace(['*', '.'], ['.*', '\.'], $whitelistedIp);
                if (preg_match("/^{$pattern}$/", $clientIp)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}

