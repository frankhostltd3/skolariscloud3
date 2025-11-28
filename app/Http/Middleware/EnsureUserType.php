<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        if (! $allowed) {
            Log::warning('EnsureUserType denied access', [
                'user_id' => $user->id,
                'user_type' => $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type,
                'allowed_types' => $types,
                'path' => $request->path(),
                'route_name' => optional($request->route())->getName(),
            ]);

            abort(403);
        }

        return $next($request);
    }
}
