<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeesCleared
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only check for students
        if (!$user || !$user->hasRole('student')) {
            return $next($request);
        }

        // Check for overdue invoices
        // Assuming 'overdue' status is managed or we check due_date < now() and balance > 0
        $hasOverdue = $user->invoices()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now()->startOfDay())
            ->exists();

        if ($hasOverdue) {
            // Allow access to fees page and payment routes
            if ($request->routeIs('tenant.student.fees.*') || $request->routeIs('tenant.student.clearance')) {
                return $next($request);
            }

            return redirect()->route('tenant.student.clearance');
        }

        return $next($request);
    }
}
