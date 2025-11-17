<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If no user is authenticated, let authentication middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Check if user has approval_status field (for backward compatibility)
        if (!isset($user->approval_status)) {
            return $next($request);
        }

        // Allow access for approved users
        if ($user->approval_status === 'approved') {
            return $next($request);
        }

        // Redirect pending users to waiting page
        if ($user->approval_status === 'pending') {
            if (!$request->routeIs('pending-approval')) {
                return redirect()->route('pending-approval');
            }
            return $next($request);
        }

        // Redirect rejected users to rejection notice
        if ($user->approval_status === 'rejected') {
            if (!$request->routeIs('registration-rejected')) {
                return redirect()->route('registration-rejected');
            }
            return $next($request);
        }

        return $next($request);
    }
}
