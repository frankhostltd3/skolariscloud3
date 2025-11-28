<?php

namespace Skolaris\AcademicReports;

use Illuminate\Support\ServiceProvider;

class SkolarisAcademicReportsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'skolaris-reports');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/skolaris_reports.php' => config_path('skolaris_reports.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/skolaris-reports'),
        ], 'views');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/skolaris_reports.php', 'skolaris_reports'
        );
    }
}