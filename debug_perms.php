<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\LandlordUser::find(2);
if (!$user) {
    echo "User 2 not found.\n";
    exit;
}

$registrar = app(\Spatie\Permission\PermissionRegistrar::class);
$registrar->setPermissionsTeamId('landlord');
$registrar->forgetCachedPermissions(); // Clear cache first

// FIX: Create permission if missing
try {
    $permName = 'access landlord dashboard';
    $guardName = 'landlord';

    $perm = \App\Models\Permission::where('name', $permName)->where('guard_name', $guardName)->first();
    if (!$perm) {
        echo "Permission '$permName' ($guardName) not found. Creating it...\n";
        $perm = \App\Models\Permission::create(['name' => $permName, 'guard_name' => $guardName, 'tenant_id' => 'landlord']);
    } else {
        echo "Permission '$permName' ($guardName) exists (ID: $perm->id, Tenant ID: " . ($perm->tenant_id ?? 'NULL') . ").\n";

        if ($perm->tenant_id !== 'landlord') {
            echo "Updating permission tenant_id to 'landlord'...\n";
            $perm->tenant_id = 'landlord';
            $perm->save();
        }
    }

    // Assign to Super Admin role
    $roleName = 'Super Admin';
    $role = \App\Models\Role::where('name', $roleName)->where('guard_name', $guardName)->where('tenant_id', 'landlord')->first();

    if ($role) {
        // Reload permission to be sure
        $perm = $perm->fresh();

        // Manually check DB for pivot to avoid Spatie cache issues
        $exists = \DB::table('role_has_permissions')
            ->where('permission_id', $perm->id)
            ->where('role_id', $role->id)
            ->exists();

        if (!$exists) {
            echo "Assigning permission to role '$roleName' (Manual DB Insert)...\n";
            \DB::table('role_has_permissions')->insert([
                'permission_id' => $perm->id,
                'role_id' => $role->id
            ]);
        } else {
            echo "Role '$roleName' already has permission (DB Verified).\n";
        }
    } else {
        echo "Role '$roleName' not found!\n";
    }

    // Clear cache again
    $registrar->forgetCachedPermissions();

} catch (\Exception $e) {
    echo "Error fixing permissions: " . $e->getMessage() . "\n";
}

echo "User ID: " . $user->id . "\n";
echo "Morph Class: " . $user->getMorphClass() . "\n";

$roles = \DB::table('model_has_roles')->where('model_id', 2)->get();
echo "Raw Roles:\n";
foreach($roles as $r) {
    echo "Role ID: $r->role_id, Tenant ID: " . ($r->tenant_id ?? 'NULL') . ", Model Type: $r->model_type\n";
}

try {
    $hasRole = $user->hasRole('Super Admin', 'landlord');
    echo "Has Super Admin role? " . ($hasRole ? 'Yes' : 'No') . "\n";
} catch (\Exception $e) {
    echo "Error checking role: " . $e->getMessage() . "\n";
}

try {
    $hasPerm = $user->hasPermissionTo('access landlord dashboard', 'landlord');
    echo "Has permission? " . ($hasPerm ? 'Yes' : 'No') . "\n";
} catch (\Exception $e) {
    echo "Error checking permission: " . $e->getMessage() . "\n";
}
