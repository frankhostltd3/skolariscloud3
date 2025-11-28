<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

$schoolId = 2;
$school = School::find($schoolId);

if (!$school) {
    echo "School not found.\n";
    exit;
}

echo "School: " . $school->name . " (ID: $schoolId)\n";

// Connect to tenant DB
$manager = app(TenantDatabaseManager::class);
$manager->connect($school);

// Set Permission Team ID
app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($schoolId);

$email = 'admin@victorianileschool.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User $email not found.\n";
    exit;
}

echo "User ID: " . $user->id . "\n";
echo "User Type: " . ($user->user_type->value ?? $user->user_type) . "\n";

// Check roles via Eloquent
echo "Roles (Eloquent): " . $user->roles->pluck('name')->implode(', ') . "\n";

// Check raw DB for roles to see tenant_id
$roles = DB::table('model_has_roles')
    ->where('model_id', $user->id)
    ->where('model_type', User::class)
    ->get();

echo "Raw model_has_roles:\n";
foreach ($roles as $role) {
    echo " - Role ID: " . $role->role_id . ", Tenant ID: " . $role->tenant_id . "\n";
}

// Check roles table
$dbRoles = Role::all();
echo "Available Roles in DB:\n";
foreach ($dbRoles as $r) {
    echo " - ID: " . $r->id . ", Name: " . $r->name . ", Guard: " . $r->guard_name . "\n";
}

// Check Role model connection
$role = new Role();
echo "Role Model Connection: " . $role->getConnectionName() . "\n";
echo "Default DB Connection: " . config('database.default') . "\n";
