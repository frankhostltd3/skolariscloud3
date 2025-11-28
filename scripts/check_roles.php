<?php

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Spatie\Permission\Models\Role;

$school = School::where('subdomain', 'victorianileschool')->first();

if (!$school) {
    echo "School not found.\n";
    exit;
}

app(TenantDatabaseManager::class)->connect($school);

echo "Connected to tenant: " . $school->name . "\n";

$roles = Role::pluck('name');
echo "Roles found: " . $roles->implode(', ') . "\n";

// Check specifically for 'super-admin' and 'admin'
$superAdmin = Role::where('name', 'super-admin')->first();
$admin = Role::where('name', 'admin')->first();

if (!$superAdmin) {
    echo "Creating 'super-admin' role...\n";
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
}

if (!$admin) {
    echo "Creating 'admin' role...\n";
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
}

echo "Done.\n";
