<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IpBlock;
use Illuminate\Support\Facades\Log;

class CheckIpBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $ipAddress = $request->ip();
            $tenantId = tenant('id');

            // Check if IP is blocked
            $block = IpBlock::getBlock($ipAddress, $tenantId);

            if ($block) {
                // Return 403 Forbidden response
                $message = $block->is_permanent
                    ? 'Your IP address has been permanently blocked.'
                    : 'Your IP address has been temporarily blocked until ' . $block->expires_at->format('Y-m-d H:i:s');

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'reason' => $block->reason,
                        'blocked_at' => $block->blocked_at->toIso8601String(),
                        'expires_at' => $block->expires_at?->toIso8601String(),
                        'is_permanent' => $block->is_permanent,
                    ], 403);
                }

                abort(403, $message);
            }

            // Check if IP should be auto-blocked
            if ($request->isMethod('POST') && $this->isAuthenticationRoute($request)) {
                IpBlock::checkAutoBlock($ipAddress, $tenantId);
            }
        } catch (\Exception $e) {
            // If the table doesn't exist or there's a database error, allow the request to continue
            // This handles cases where migrations haven't been run yet
            Log::debug('CheckIpBlocked middleware error: ' . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Determine if this is an authentication route
     */
    protected function isAuthenticationRoute(Request $request): bool
    {
        return $request->is('login') ||
               $request->is('*/login') ||
               $request->is('authenticate') ||
               $request->is('*/authenticate');
    }
}
