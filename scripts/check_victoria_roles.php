<?php

use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;

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

$user = User::where('email', 'victorianileschool@example.com')->first();

if (! $user) {
    echo "User not found" . PHP_EOL;
    exit(1);
}

echo "User: {$user->name} ({$user->email})" . PHP_EOL;

echo "Roles:" . PHP_EOL;
foreach ($user->roles as $role) {
    echo " - {$role->name}" . PHP_EOL;
}

if ($user->roles->isEmpty()) {
    echo " (no roles)" . PHP_EOL;
}

