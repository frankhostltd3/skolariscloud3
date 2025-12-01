<?php

use App\Models\LandlordUser;
use Spatie\Permission\Models\Role;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set context for Spatie Permission (Teams)
// \Spatie\Permission\PermissionRegistrar::$teams = true;
setPermissionsTeamId('landlord');

$email = 'frankhostltd3@gmail.com';
$user = LandlordUser::where('email', $email)->first();

if (!$user) {
    echo "User {$email} not found in LandlordUser model (central DB).\n";
    exit(1);
}

echo "User found: {$user->name} (ID: {$user->id})\n";

$roleName = 'landlord-admin';
// Explicitly find the role for the 'landlord' tenant
$role = Role::where('name', $roleName)
    ->where('guard_name', 'landlord')
    ->where('tenant_id', 'landlord')
    ->first();

if (!$role) {
    echo "Role {$roleName} for tenant 'landlord' not found.\n";
    // Fallback to ID 6 if we know it exists
    $role = Role::find(6);
    if ($role) echo "Found Role ID 6.\n";
} else {
    echo "Found Role ID: {$role->id} (Tenant: {$role->tenant_id})\n";
}

if (!$role) exit(1);

// Remove incorrect role if exists (Role ID 1)
$badRole = Role::find(1);
if ($badRole && $user->hasRole($badRole)) {
    echo "Removing incorrect Role ID 1...\n";
    $user->removeRole($badRole);
}

if ($user->hasRole($role)) {
    echo "User already has the correct role (ID {$role->id}).\n";
} else {
    echo "Assigning role ID {$role->id} to user...\n";
    $user->assignRole($role);
    echo "Role assigned successfully.\n";
}

// Verify permission
if ($user->hasPermissionTo('manage landlord billing', 'landlord')) {
    echo "Verification: User HAS 'manage landlord billing' permission.\n";
} else {
    echo "Verification: User DOES NOT HAVE 'manage landlord billing' permission.\n";
}
