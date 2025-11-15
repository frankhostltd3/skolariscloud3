<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ApplyLogLevel
{
    /**
     * Handle an incoming request.
     *
     * Apply the log level from tenant settings to Laravel logging configuration.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if running in console (artisan commands)
        if (app()->runningInConsole()) {
            return $next($request);
        }

        // Get log level from tenant settings
        $logLevel = setting('log_level', 'error');

        // Validate log level (must be one of the PSR-3 log levels)
        $validLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

        if (!in_array($logLevel, $validLevels)) {
            $logLevel = 'error'; // Fallback to default
        }

        // Apply to all logging channels dynamically
        Config::set('logging.channels.single.level', $logLevel);
        Config::set('logging.channels.daily.level', $logLevel);
        Config::set('logging.channels.stack.level', $logLevel);
        Config::set('logging.channels.stderr.level', $logLevel);
        Config::set('logging.channels.syslog.level', $logLevel);

        return $next($request);
    }
}
