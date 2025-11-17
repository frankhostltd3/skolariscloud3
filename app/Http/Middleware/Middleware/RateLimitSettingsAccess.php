<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitSettingsAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only rate limit POST/PUT/PATCH/DELETE requests (actual changes)
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Get rate limit settings
        $maxChanges = (int) setting('settings_rate_limit_per_hour', 10);
        $cacheKey = "settings_changes:{$user->id}:" . now()->format('Y-m-d-H');

        // Get current change count
        $changeCount = Cache::get($cacheKey, 0);

        // Check if limit exceeded
        if ($changeCount >= $maxChanges) {
            Log::warning('Settings change rate limit exceeded', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'change_count' => $changeCount,
                'limit' => $maxChanges,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Too many settings changes. Maximum {$maxChanges} changes per hour allowed.",
                    'limit' => $maxChanges,
                    'retry_after' => 3600 - (now()->minute * 60 + now()->second),
                ], 429);
            }

            return back()->with('error', "Too many settings changes. You can make up to {$maxChanges} changes per hour. Please try again later.");
        }

        // Increment counter (expires in 1 hour)
        Cache::put($cacheKey, $changeCount + 1, now()->addHour());

        return $next($request);
    }
}
