<?php

namespace Skolaris\FeesPay;

use Illuminate\Support\ServiceProvider;

class SkolarisFeesPayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/fees-pay.php', 'fees-pay'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'fees-pay');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/fees-pay.php' => config_path('fees-pay.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/fees-pay'),
            ], 'views');
        }
    }
}
