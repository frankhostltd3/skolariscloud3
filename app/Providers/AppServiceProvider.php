<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OnlineExam;
use App\Observers\OrderObserver;
use App\Policies\OnlineExamPolicy;
use App\Services\EnvWriter;
use App\Services\MailConfigurator;
use App\Services\MessagingConfigurator;
use App\Services\PaymentGatewayConfigurator;
use App\Services\TenantDatabaseManager;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use App\Listeners\UpdateApprovalStatusOnVerification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Route;
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

        Gate::policy(OnlineExam::class, OnlineExamPolicy::class);

        Event::listen(
            Verified::class,
            UpdateApprovalStatusOnVerification::class,
        );

        ResetPassword::createUrlUsing(function ($user, string $token) {
            $baseUrl = $user->school?->url ?? config('app.url');
            $path = route('password.reset', ['token' => $token, 'email' => $user->email], false);

            if (! $baseUrl) {
                return URL::to($path);
            }

            return rtrim($baseUrl, '/') . $path;
        });

        // Configure authentication to preserve subdomain in redirects
        $this->configureAuthenticationRedirects();

        Order::observe(OrderObserver::class);
    }

    /**
     * Configure authentication redirects to preserve subdomain context.
     */
    protected function configureAuthenticationRedirects(): void
    {
        // Override the redirectTo method for unauthenticated users
        $this->app->bind(
            \Illuminate\Auth\Middleware\Authenticate::class,
            function ($app) {
                return new class($app['auth']) extends \Illuminate\Auth\Middleware\Authenticate
                {
                    protected function redirectTo($request): ?string
                    {
                        if ($request->expectsJson()) {
                            return null;
                        }

                        $path = trim($request->path(), '/');
                        if (str_starts_with($path, 'landlord')) {
                            return Route::has('landlord.login.show')
                                ? route('landlord.login.show')
                                : '/landlord/login';
                        }

                        if (function_exists('tenant') && tenant()) {
                            if (Route::has('tenant.login')) {
                                return route('tenant.login');
                            }

                            $tenantHost = $request->getHost();
                            $tenantPort = $request->getPort();
                            $tenantPortSuffix = ($tenantPort && $tenantPort != 80 && $tenantPort != 443)
                                ? ":{$tenantPort}"
                                : '';

                            return sprintf('%s://%s%s/login', $request->getScheme(), $tenantHost, $tenantPortSuffix);
                        }

                        $currentHost = $request->getHost();
                        $currentPort = $request->getPort();
                        $portSuffix = ($currentPort && $currentPort != 80 && $currentPort != 443)
                            ? ":{$currentPort}"
                            : '';

                        if (!in_array($currentHost, ['localhost', '127.0.0.1'])) {
                            return sprintf('%s://%s%s/login', $request->getScheme(), $currentHost, $portSuffix);
                        }

                        if (Route::has('landlord.login.show')) {
                            return route('landlord.login.show');
                        }

                        return Route::has('login') ? route('login') : '/';
                    }
                };
            }
        );
    }
}
