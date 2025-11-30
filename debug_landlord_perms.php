<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LandlordUser;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

// Load the landlord user (assuming ID 2 based on previous errors)
$user = LandlordUser::find(2);

if (!$user) {
    echo "User not found.\n";
    exit;
}

echo "User: " . $user->email . " (ID: " . $user->id . ")\n";
echo "Model Type: " . get_class($user) . "\n";

// Set context
app(PermissionRegistrar::class)->setPermissionsTeamId(null);
echo "Team ID set to NULL.\n";

// Check Role
$roleName = 'Landlord Admin';
$hasRole = $user->hasRole($roleName, 'landlord');
echo "Has Role '$roleName' (landlord): " . ($hasRole ? 'YES' : 'NO') . "\n";

// Check Permission
$permName = 'access landlord dashboard';
$hasPerm = $user->hasPermissionTo($permName, 'landlord');
echo "Has Permission '$permName' (landlord): " . ($hasPerm ? 'YES' : 'NO') . "\n";

// Inspect DB directly
$roles = \Illuminate\Support\Facades\DB::table('model_has_roles')
    ->where('model_id', $user->id)
    ->where('model_type', get_class($user))
    ->get();

echo "\nDB Roles:\n";
foreach ($roles as $r) {
    echo "Role ID: $r->role_id, Tenant ID: " . var_export($r->tenant_id, true) . "\n";
}

$perms = \Illuminate\Support\Facades\DB::table('model_has_permissions')
    ->where('model_id', $user->id)
    ->where('model_type', get_class($user))
    ->get();

echo "\nDB Permissions:\n";
foreach ($perms as $p) {
    echo "Perm ID: $p->permission_id, Tenant ID: " . var_export($p->tenant_id, true) . "\n";
}

// Check Permission Definition
$pDef = \Illuminate\Support\Facades\DB::table('permissions')->where('name', $permName)->first();
echo "\nPermission Definition:\n";
if ($pDef) {
    echo "ID: $pDef->id, Guard: $pDef->guard_name, Tenant ID: " . var_export($pDef->tenant_id, true) . "\n";
} else {
    echo "Permission definition not found.\n";
}
