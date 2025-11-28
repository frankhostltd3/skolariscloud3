<?php

use App\Models\School;
use App\Services\TenantDatabaseManager;
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

app(TenantDatabaseManager::class)->connect($school);

$roles = Role::all();

echo "Available roles:" . PHP_EOL;
foreach ($roles as $role) {
    echo " - {$role->name}" . PHP_EOL;
}
