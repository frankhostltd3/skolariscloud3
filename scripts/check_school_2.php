<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Spatie\Permission\Models\Role;

$schoolId = 2;
$school = School::find($schoolId);

if (!$school) {
    echo "School not found.\n";
    exit;
}

echo "School Name: " . $school->name . "\n";
echo "Subdomain: " . $school->subdomain . "\n";

// Connect to tenant DB
$manager = app(TenantDatabaseManager::class);
$manager->connect($school);

echo "Connected to tenant DB: " . $school->database . "\n";

$users = User::all();

foreach ($users as $user) {
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "User Type: " . ($user->user_type->value ?? $user->user_type) . "\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    echo "-----------------------------------\n";
}

$roles = Role::all()->pluck('name');
echo "Available Roles: " . $roles->implode(', ') . "\n";
