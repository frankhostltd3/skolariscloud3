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

// Check for 'Super Admin'
$role = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
if ($role) {
    echo "Role exists: Super Admin\n";
} else {
    echo "Role does NOT exist: Super Admin\n";
}

// Check for 'Admin'
$role = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
if ($role) {
    echo "Role exists: Admin\n";
} else {
    echo "Role does NOT exist: Admin\n";
}
