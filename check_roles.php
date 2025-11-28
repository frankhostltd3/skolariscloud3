<?php

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Spatie\Permission\Models\Role;

$school = School::where('subdomain', 'victorianileschool')->first();

if (!$school) {
    echo "School not found\n";
    exit(1);
}

echo "Connecting to school: " . $school->name . "\n";

app(TenantDatabaseManager::class)->connect($school);

$roles = Role::pluck('name')->toArray();

echo "Roles found: " . implode(', ', $roles) . "\n";
