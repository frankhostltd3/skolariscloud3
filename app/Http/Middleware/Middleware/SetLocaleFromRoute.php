<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromRoute
{
    public function handle(Request $request, Closure $next)
    {
        $locale = (string) $request->route('locale', '');

        // Get supported locales from app config
        $supportedLocales = config('app.available_locales', ['en']);

        if ($locale !== '' && \in_array($locale, $supportedLocales, true)) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
