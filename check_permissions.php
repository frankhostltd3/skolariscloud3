<?php

use App\Models\LandlordUser;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set context
// \Spatie\Permission\PermissionRegistrar::$teams = true;
setPermissionsTeamId('landlord');

echo "--- Checking Permissions ---\n";
$perm = Permission::where('name', 'manage landlord billing')->where('guard_name', 'landlord')->first();
if ($perm) {
    echo "Permission 'manage landlord billing' exists. ID: {$perm->id}, Guard: {$perm->guard_name}, Tenant: {$perm->tenant_id}\n";
} else {
    echo "Permission 'manage landlord billing' NOT FOUND.\n";
}

echo "\n--- Checking Role ---\n";
$role = Role::where('name', 'landlord-admin')->where('guard_name', 'landlord')->first();
if ($role) {
    echo "Role 'landlord-admin' exists. ID: {$role->id}, Guard: {$role->guard_name}, Tenant: {$role->tenant_id}\n";

    if ($perm) {
        $hasPerm = DB::table('role_has_permissions')
            ->where('permission_id', $perm->id)
            ->where('role_id', $role->id)
            ->exists();
        echo "Role has permission: " . ($hasPerm ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "Role 'landlord-admin' NOT FOUND.\n";
}

echo "\n--- Checking Role ID 6 ---\n";
$role6 = Role::find(6);
if ($role6) {
    echo "Role 6 found. Name: {$role6->name}, Guard: {$role6->guard_name}, Tenant: {$role6->tenant_id}\n";
    if ($perm) {
        $hasPerm = DB::table('role_has_permissions')
            ->where('permission_id', $perm->id)
            ->where('role_id', $role6->id)
            ->exists();
        echo "Role 6 has permission: " . ($hasPerm ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "Role 6 NOT FOUND.\n";
}
$user = LandlordUser::where('email', 'admin@landlord.local')->first();
if ($user) {
    echo "User found. ID: {$user->id}, Email: {$user->email}\n";

    $hasRole = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', App\Models\LandlordUser::class)
        ->where('role_id', $role->id)
        ->where('tenant_id', 'landlord')
        ->exists();

    echo "User has role (via DB check for 'landlord'): " . ($hasRole ? 'YES' : 'NO') . "\n";

    $actualRole = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', App\Models\LandlordUser::class)
        ->first();

    if ($actualRole) {
        echo "Actual Role Assignment: Role ID: {$actualRole->role_id}, Tenant ID: {$actualRole->tenant_id}, Model Type: {$actualRole->model_type}\n";
    } else {
        echo "No role assignment found in DB.\n";
    }

    // Check via model
    echo "User has role (via model): " . ($user->hasRole('landlord-admin', 'landlord') ? 'YES' : 'NO') . "\n";
    echo "User has permission (via model): " . ($user->hasPermissionTo('manage landlord billing', 'landlord') ? 'YES' : 'NO') . "\n";

} else {
    echo "User 'admin@landlord.local' NOT FOUND.\n";
    // Check for the other email
    $userOld = LandlordUser::where('email', 'admin@skolaris.cloud')->first();
    if ($userOld) {
        echo "Found user with old email 'admin@skolaris.cloud'. ID: {$userOld->id}\n";
    }
}
