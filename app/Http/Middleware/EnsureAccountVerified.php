<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip for verification routes to avoid redirect loop
        if ($request->is('account/verify*') || $request->is('logout') || $request->is('profile*')) {
            return $next($request);
        }

        // Check if account verification is required by school settings
        $accountStatus = setting('account_status', 'unverified');

        // If account verification is required and user is not verified, restrict access
        if ($accountStatus === 'verified' && !$user->email_verified_at) {
            // If AJAX request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Your account must be verified to access this resource.',
                ], 403);
            }

            // For regular requests, redirect to verification notice page
            return redirect()->route('verification.notice')
                ->with('error', 'Your account must be verified to access this page. Please check your email for the verification link.');
        }

        return $next($request);
    }
}
