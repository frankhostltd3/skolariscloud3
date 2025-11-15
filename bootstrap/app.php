<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\MigrateTenants::class,
        \App\Console\Commands\RunTenantBackups::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'user.type' => \App\Http\Middleware\EnsureUserType::class,
        ]);

    // Set high priority so these run early
    $middleware->priority([
        \App\Http\Middleware\IdentifySchoolFromHost::class,
        \App\Http\Middleware\SwitchTenantDatabase::class,
    ]);

    $middleware->appendToGroup('web', \App\Http\Middleware\ForceHttps::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\IdentifySchoolFromHost::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\SwitchTenantDatabase::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplySchoolMailConfiguration::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyPaymentGatewayConfiguration::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyMessagingConfiguration::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyPerformanceSettings::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyLogLevel::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\EnsureAccountVerified::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\EnsureTwoFactorEnabled::class);

    $middleware->appendToGroup('api', \App\Http\Middleware\IdentifySchoolFromHost::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\SwitchTenantDatabase::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\ApplySchoolMailConfiguration::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\ApplyPaymentGatewayConfiguration::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\ApplyMessagingConfiguration::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
