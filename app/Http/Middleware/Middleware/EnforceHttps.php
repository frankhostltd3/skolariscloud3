<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if HTTPS enforcement is enabled in settings
        $forceHttps = (bool) setting('force_https', false);
        
        // Skip in local development
        if (app()->environment('local')) {
            $forceHttps = false;
        }
        
        // If HTTPS is enforced and request is not secure, redirect
        if ($forceHttps && !$request->secure() && !$request->is('health-check')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }
        
        $response = $next($request);
        
        // Add security headers when HTTPS is enabled
        if ($forceHttps && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }
        
        return $response;
    }
}
