<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for CLI/console commands
        if (app()->runningInConsole()) {
            return $next($request);
        }

        // Check if HTTPS enforcement is enabled in settings
        $forceHttps = (bool) setting('force_https', false);

        // If HTTPS is enforced and request is not secure, redirect to HTTPS
        if ($forceHttps && !$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
