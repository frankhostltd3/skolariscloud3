<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetLocaleFromRoute
{
    public function handle(Request $request, Closure $next)
    {
        $locale = (string) $request->route('locale', '');

        $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));

        if ($locale !== '' && \in_array($locale, $supportedLocales, true)) {
            LaravelLocalization::setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
