<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic backups for all tenants
Schedule::command('tenants:backup daily')
    ->daily()
    ->at('02:00')
    ->description('Run daily backups for tenants with daily backup enabled');

Schedule::command('tenants:backup weekly')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->description('Run weekly backups for tenants with weekly backup enabled');

Schedule::command('tenants:backup monthly')
    ->monthly()
    ->at('04:00')
    ->description('Run monthly backups for tenants with monthly backup enabled');

// Cleanup old backups
Schedule::command('backup:clean')
    ->daily()
    ->at('05:00')
    ->description('Clean up old backups based on retention settings');

// Update exchange rates for all tenants
Schedule::command('tenants:update-exchange-rates')
    ->daily()
    ->at('06:00')
    ->description('Update exchange rates for currencies with auto-update enabled');
