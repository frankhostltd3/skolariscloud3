<?php

namespace App\Providers;

use App\Services\EnvWriter;
use App\Services\MailConfigurator;
use App\Services\MessagingConfigurator;
use App\Services\PaymentGatewayConfigurator;
use App\Services\TenantDatabaseManager;
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
        $this->app->singleton(MailConfigurator::class);
    $this->app->singleton(MessagingConfigurator::class);
        $this->app->singleton(PaymentGatewayConfigurator::class);
        $this->app->singleton(TenantDatabaseManager::class);
        $this->app->singleton(EnvWriter::class);
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
