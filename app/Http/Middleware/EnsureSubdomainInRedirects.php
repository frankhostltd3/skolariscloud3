<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubdomainInRedirects
{
    /**
     * Handle an incoming request.
     * Ensures authentication redirects preserve the subdomain.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (AuthenticationException $e) {
            // Get subdomain from session or current request
            $subdomain = $request->session()->get('tenant_subdomain');

            if (!$subdomain) {
                // Try to extract from current host
                $host = $request->getHost();
                if (preg_match('/^(.+)\.localhost$/i', $host, $matches)) {
                    $subdomain = $matches[1];
                }
            }

            // Build login URL with subdomain preserved
            if ($subdomain) {
                $port = $request->getPort();
                $portSuffix = ($port && $port != 80 && $port != 443) ? ":{$port}" : '';
                $loginUrl = $request->getScheme() . "://{$subdomain}.localhost{$portSuffix}/login";

                return redirect()->guest($loginUrl);
            }

            // Fallback to default behavior
            return redirect()->guest(route('login'));
        }
    }
}
