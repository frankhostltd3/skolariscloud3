<?php

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$school = School::where('subdomain', 'victorianileschool')->first();

if (! $school) {
    echo "School not found" . PHP_EOL;
    exit(1);
}

/** @var TenantDatabaseManager $manager */
$manager = app(TenantDatabaseManager::class);

$manager->runFor($school, function () {
    Permission::query()->delete();
    Role::query()->delete();
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    Artisan::call('db:seed', [
        '--class' => 'PermissionsSeeder',
        '--force' => true,
    ]);

    echo Artisan::output();
});
