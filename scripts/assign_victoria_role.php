<?php

use App\Models\School;
use App\Models\User;
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

/** @var TenantDatabaseManager $manager */
$manager = app(TenantDatabaseManager::class);

$manager->runFor($school, function () {
    $user = User::where('email', 'victorianileschool@example.com')->first();
    if (! $user) {
        echo "User not found" . PHP_EOL;
        return;
    }

    $role = Role::where('name', 'super-admin')->first();
    if (! $role) {
        echo "Role 'super-admin' not found" . PHP_EOL;
        return;
    }

    if ($user->hasRole($role)) {
        echo "User already has the 'super-admin' role." . PHP_EOL;
    } else {
        $user->assignRole($role);
        echo "Successfully assigned 'super-admin' role to the user." . PHP_EOL;
    }

    // Verify
    $user = $user->fresh();
    if ($user->hasRole('super-admin')) {
        echo "Verification successful: User has the role." . PHP_EOL;
    } else {
        echo "Verification failed: User does not have the role." . PHP_EOL;
    }
});
