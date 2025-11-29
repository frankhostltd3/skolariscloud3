<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Central domain routes (localhost, 127.0.0.1)
            $centralDomains = ['localhost', '127.0.0.1', 'localhost:8000', '127.0.0.1:8000'];
            $currentHost = request()->getHost();
            $currentHostWithPort = request()->getHttpHost();

            $isCentralDomain = in_array($currentHost, $centralDomains) ||
                               in_array($currentHostWithPort, $centralDomains);

            if ($isCentralDomain) {
                // Load central routes for school registration
                \Illuminate\Support\Facades\Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            } else {
                // Load tenant routes for school-specific operations
                \Illuminate\Support\Facades\Route::middleware('web')
                    ->group(base_path('routes/tenant.php'));
            }

            // Load API routes for all domains
            \Illuminate\Support\Facades\Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withCommands([
        \App\Console\Commands\MigrateTenants::class,
        \App\Console\Commands\RunTenantBackups::class,
        \App\Console\Commands\SeedTenantCurrencies::class,
        \App\Console\Commands\SeedTenantPlatformIntegrations::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'user.type' => \App\Http\Middleware\EnsureUserType::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'approved' => \App\Http\Middleware\EnsureUserApproved::class,
        ]);

    // Set high priority so these run early
    $middleware->priority([
        \App\Http\Middleware\IdentifySchoolFromHost::class,
        \App\Http\Middleware\SwitchTenantDatabase::class,
        \App\Http\Middleware\SetPermissionTeamContext::class,
    ]);

    $middleware->appendToGroup('web', \App\Http\Middleware\ForceHttps::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\IdentifySchoolFromHost::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\SwitchTenantDatabase::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\SetPermissionTeamContext::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\PreserveSubdomainContext::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplySchoolMailConfiguration::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyPaymentGatewayConfiguration::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyMessagingConfiguration::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyPerformanceSettings::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\ApplyLogLevel::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\EnsureAccountVerified::class);
    $middleware->appendToGroup('web', \App\Http\Middleware\EnsureTwoFactorEnabled::class);

    $middleware->appendToGroup('api', \App\Http\Middleware\IdentifySchoolFromHost::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\SwitchTenantDatabase::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\SetPermissionTeamContext::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\ApplySchoolMailConfiguration::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\ApplyPaymentGatewayConfiguration::class);
    $middleware->appendToGroup('api', \App\Http\Middleware\ApplyMessagingConfiguration::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
