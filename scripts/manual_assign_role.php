<?php

use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\DB;
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

$manager->runFor($school, function () use ($school) {
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

    try {
        DB::table(config('permission.table_names.model_has_roles'))->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
            'tenant_id' => $school->id,
        ]);
        echo "Successfully assigned 'super-admin' role to the user." . PHP_EOL;

    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            echo "User already has the 'super-admin' role." . PHP_EOL;
        } else {
            echo "An error occurred: " . $e->getMessage() . PHP_EOL;
        }
    }

    // Verify
    $hasRole = DB::table(config('permission.table_names.model_has_roles'))
        ->where('model_id', $user->id)
        ->where('role_id', $role->id)
        ->where('tenant_id', $school->id)
        ->exists();

    if ($hasRole) {
        echo "Verification successful: User has the role." . PHP_EOL;
    } else {
        echo "Verification failed: User does not have the role." . PHP_EOL;
    }
});
