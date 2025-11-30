<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$email = 'frankhostltd3@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found.\n";
    exit;
}

echo "User found: {$user->name} ({$user->id})\n";

// Reset password
$user->password = bcrypt('password');
$user->save();
echo "Password reset to 'password'.\n";

// Check roles
echo "Current roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";

// Check permissions
// Note: Spatie permissions are usually cached. We might need to clear cache.
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Ensure 'Super Admin' role exists for 'landlord' guard
$roleName = 'Super Admin';
$guardName = 'landlord';
$teamId = 'landlord'; // Use a specific team ID for landlord

// Set the team ID for Spatie
app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($teamId);

$role = Role::where('name', $roleName)->where('guard_name', $guardName)->where('tenant_id', $teamId)->first();
if (!$role) {
    echo "Role '$roleName' for guard '$guardName' not found. Creating...\n";
    $role = Role::create(['name' => $roleName, 'guard_name' => $guardName, 'tenant_id' => $teamId]);
} else {
    echo "Role '$roleName' for guard '$guardName' found.\n";
}

// Ensure permission exists
$permissionName = 'access landlord dashboard';
$permission = Permission::where('name', $permissionName)->where('guard_name', $guardName)->where('tenant_id', $teamId)->first();
if (!$permission) {
    echo "Permission '$permissionName' for guard '$guardName' not found. Creating...\n";
    $permission = Permission::create(['name' => $permissionName, 'guard_name' => $guardName, 'tenant_id' => $teamId]);
} else {
    echo "Permission '$permissionName' for guard '$guardName' found.\n";
}

// Assign permission to role
if (!$role->hasPermissionTo($permissionName)) {
    $role->givePermissionTo($permission);
    echo "Permission assigned to role.\n";
} else {
    echo "Role already has permission.\n";
}

// Assign role to user
// We need to make sure we are assigning the role for the correct guard.
// The user model might use 'web' guard by default.
// But we want to assign the 'landlord' role.
// Spatie handles this if we pass the role object.

if (!$user->hasRole($roleName, $guardName)) {
    $user->assignRole($role);
    echo "Role '$roleName' assigned to user.\n";
} else {
    echo "User already has role '$roleName'.\n";
}

echo "Done.\n";
