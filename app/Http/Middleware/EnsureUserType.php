<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>  $types
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = collect($types)
            ->map(fn (string $type) => UserType::tryFrom($type))
            ->filter()
            ->contains(fn (UserType $type) => $user->user_type === $type);

        abort_unless($allowed, 403);

        return $next($request);
    }
}
