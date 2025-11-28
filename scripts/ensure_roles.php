<?php

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Spatie\Permission\Models\Role;

// Find the school
$school = School::where('subdomain', 'victorianileschool')->first();

if (!$school) {
    echo "School 'victorianileschool' not found.\n";
    exit;
}

echo "Connecting to tenant: " . $school->name . "\n";
app(TenantDatabaseManager::class)->connect($school);

// Roles to ensure
$rolesToEnsure = ['admin', 'super-admin'];

foreach ($rolesToEnsure as $roleName) {
    $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
    if (!$role) {
        echo "Creating role: $roleName\n";
        Role::create(['name' => $roleName, 'guard_name' => 'web']);
    } else {
        echo "Role exists: $roleName\n";
    }
}

echo "Done.\n";
