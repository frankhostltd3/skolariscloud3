<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LandlordUser;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

$email = 'frankhostltd3@gmail.com';
$user = LandlordUser::where('email', $email)->first();

if (!$user) {
    echo "User $email not found.\n";
    exit(1);
}

echo "Found User: {$user->id}\n";

// 1. Fix Permission Definition
$permName = 'access landlord dashboard';
$perm = DB::table('permissions')->where('name', $permName)->where('guard_name', 'landlord')->first();

if ($perm) {
    echo "Updating Permission '{$permName}' tenant_id to NULL...\n";
    DB::table('permissions')
        ->where('id', $perm->id)
        ->update(['tenant_id' => null]);
} else {
    echo "Creating Permission '{$permName}'...\n";
    $permId = DB::table('permissions')->insertGetId([
        'name' => $permName,
        'guard_name' => 'landlord',
        'tenant_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $perm = (object)['id' => $permId];
}

// 2. Fix Role Definition
$roleName = 'Landlord Admin';
$role = DB::table('roles')->where('name', $roleName)->where('guard_name', 'landlord')->first();

if ($role) {
    echo "Updating Role '{$roleName}' tenant_id to NULL...\n";
    DB::table('roles')
        ->where('id', $role->id)
        ->update(['tenant_id' => null]);
} else {
    echo "Creating Role '{$roleName}'...\n";
    $roleId = DB::table('roles')->insertGetId([
        'name' => $roleName,
        'guard_name' => 'landlord',
        'tenant_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $role = (object)['id' => $roleId];
}

// 3. Assign Role to User
$hasRole = DB::table('model_has_roles')
    ->where('model_id', $user->id)
    ->where('model_type', get_class($user))
    ->where('role_id', $role->id)
    ->exists();

if (!$hasRole) {
    echo "Assigning Role to User...\n";
    DB::table('model_has_roles')->insert([
        'role_id' => $role->id,
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'tenant_id' => null,
    ]);
} else {
    echo "User already has role. Updating tenant_id to NULL just in case...\n";
    DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', get_class($user))
        ->where('role_id', $role->id)
        ->update(['tenant_id' => null]);
}

// 4. Assign Permission to Role
$roleHasPerm = DB::table('role_has_permissions')
    ->where('permission_id', $perm->id)
    ->where('role_id', $role->id)
    ->exists();

if (!$roleHasPerm) {
    echo "Assigning Permission to Role...\n";
    DB::table('role_has_permissions')->insert([
        'permission_id' => $perm->id,
        'role_id' => $role->id,
    ]);
}

// 5. Assign Permission Directly to User (Backup)
$userHasPerm = DB::table('model_has_permissions')
    ->where('model_id', $user->id)
    ->where('model_type', get_class($user))
    ->where('permission_id', $perm->id)
    ->exists();

if (!$userHasPerm) {
    echo "Assigning Permission to User...\n";
    DB::table('model_has_permissions')->insert([
        'permission_id' => $perm->id,
        'model_type' => get_class($user),
        'model_id' => $user->id,
        'tenant_id' => null,
    ]);
} else {
    echo "User already has permission. Updating tenant_id to NULL...\n";
    DB::table('model_has_permissions')
        ->where('model_id', $user->id)
        ->where('model_type', get_class($user))
        ->where('permission_id', $perm->id)
        ->update(['tenant_id' => null]);
}

// Clear Cache
app(PermissionRegistrar::class)->forgetCachedPermissions();
echo "Cache cleared.\n";
echo "Done.\n";
