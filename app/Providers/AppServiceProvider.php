<?php

namespace App\Providers;

use App\Services\MailConfigurator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->singleton(\App\Services\MailConfigurator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        ResetPassword::createUrlUsing(function ($user, string $token) {
            $baseUrl = $user->school?->url ?? config('app.url');
            $path = route('password.reset', ['token' => $token, 'email' => $user->email], false);

            if (! $baseUrl) {
                return URL::to($path);
            }

            return rtrim($baseUrl, '/') . $path;
        });
    }
}
