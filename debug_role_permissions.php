<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

setPermissionsTeamId('landlord');

echo "--- Diagnostic for Role ID 1 (landlord-admin) ---\n";

$role = Role::find(1);
if (!$role) {
    echo "Role ID 1 not found.\n";
    exit;
}

echo "Role: {$role->name}, Guard: {$role->guard_name}, Tenant: {$role->tenant_id}\n";

$permissions = DB::table('role_has_permissions')
    ->where('role_id', 1)
    ->get();

echo "Permissions count in DB: " . $permissions->count() . "\n";

if ($permissions->count() > 0) {
    echo "First 5 permissions:\n";
    foreach ($permissions->take(5) as $p) {
        echo "- Permission ID: {$p->permission_id}\n";
    }
}

$permName = 'manage landlord billing';
$perm = Permission::where('name', $permName)->where('guard_name', 'landlord')->first();

if ($perm) {
    echo "\nPermission '{$permName}' ID: {$perm->id}\n";

    $hasLink = DB::table('role_has_permissions')
        ->where('role_id', 1)
        ->where('permission_id', $perm->id)
        ->exists();

    echo "Link exists in DB: " . ($hasLink ? 'YES' : 'NO') . "\n";
} else {
    echo "\nPermission '{$permName}' not found.\n";
}
