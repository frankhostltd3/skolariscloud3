<?php

use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;

$schoolId = 2;
$school = School::find($schoolId);

if (!$school) {
    echo "School not found.\n";
    exit;
}

echo "Checking School: " . $school->name . " (ID: $schoolId)\n";

// Connect to tenant DB
$manager = app(TenantDatabaseManager::class);
$manager->connect($school);

// Clear cache
app(PermissionRegistrar::class)->forgetCachedPermissions();
echo "Permission cache cleared.\n";

$email = 'admin@victorianileschool.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "User Found: " . $user->name . " (ID: " . $user->id . ")\n";
    echo "User Type: " . ($user->user_type instanceof \UnitEnum ? $user->user_type->value : $user->user_type) . "\n";

    // Check roles via Eloquent
    // We need to ensure the team_id is set correctly for the relationship to work if teams are enabled
    // But let's look at the raw DB first to be sure.

    $roles = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', User::class)
        ->get();

    echo "Raw Roles in DB:\n";
    foreach ($roles as $role) {
        $roleName = DB::table('roles')->where('id', $role->role_id)->value('name');
        echo "- Role ID: " . $role->role_id . " ($roleName), Tenant ID: " . $role->tenant_id . "\n";
    }

    // Check what Spatie thinks
    app(PermissionRegistrar::class)->setPermissionsTeamId($schoolId);
    echo "Spatie Roles (Team ID $schoolId): " . $user->roles->pluck('name')->implode(', ') . "\n";

} else {
    echo "User $email not found.\n";
}
