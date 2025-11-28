<?php

use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;

$school = School::find(1);

if (!$school) {
    echo "School not found.\n";
    exit;
}

echo "School Name: " . $school->name . "\n";
echo "Subdomain: " . $school->subdomain . "\n";
echo "Domain: " . $school->domain . "\n";

// Connect to tenant DB
$manager = app(TenantDatabaseManager::class);
$manager->connect($school);

$user = User::find(1);
if ($user) {
    echo "User: " . $user->name . " (" . $user->email . ")\n";
    echo "User Type: " . $user->user_type . "\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
} else {
    echo "User ID 1 not found in tenant DB.\n";
}
