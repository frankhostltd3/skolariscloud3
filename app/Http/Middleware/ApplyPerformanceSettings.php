<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyPerformanceSettings
{
    /**
     * Handle an incoming request.
     *
     * Apply performance-related settings from tenant database to Laravel config.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if not in tenant context
        if (!function_exists('setting')) {
            return $next($request);
        }

        // Apply Cache Driver setting
        $cacheDriver = setting('cache_driver', config('cache.default'));
        if (in_array($cacheDriver, ['file', 'redis', 'memcached', 'database', 'array'])) {
            config(['cache.default' => $cacheDriver]);
        }

        // Apply Session Driver setting
        $sessionDriver = setting('session_driver', config('session.driver'));
        if (in_array($sessionDriver, ['file', 'database', 'redis', 'cookie', 'array'])) {
            config(['session.driver' => $sessionDriver]);
        }

        // Apply Session Lifetime setting (in minutes)
        $sessionLifetime = (int) setting('session_lifetime', config('session.lifetime'));
        if ($sessionLifetime >= 1 && $sessionLifetime <= 1440) {
            config(['session.lifetime' => $sessionLifetime]);
        }

        return $next($request);
    }
}
