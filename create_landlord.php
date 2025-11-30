<?php

/**
 * A simple script to create or reset a landlord user without using Artisan.
 *
 * Usage: php create_landlord.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

$email = 'frankhostltd3@gmail.com';
$password = '5Loaves+2Fish'; // The desired password

echo "Attempting to create or reset landlord user: {$email}\n";

// Use the User model to find an existing user or create a new one
$user = User::firstOrNew(['email' => $email]);

if ($user->exists) {
    echo "User found. Resetting password and ensuring correct settings...\n";
} else {
    echo "User not found. Creating new user...\n";
    $user->name = 'Landlord Super Admin'; // Set a default name for new users
}

// Set user attributes
$user->password = Hash::make($password);
$user->user_type = UserType::ADMIN;
$user->email_verified_at = now(); // Mark as verified

// Save the user to the database
$user->save();

echo "User record saved successfully.\n";

// Assign the 'Super Admin' role for the 'landlord' guard
try {
    // Set the team context for Spatie Permissions
    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId('landlord');

    $role = Role::findOrCreate('Super Admin', 'landlord');
    $user->assignRole($role);
    echo "Successfully assigned 'Super Admin' role.\n";
} catch (\Exception $e) {
    echo "Error assigning role: " . $e->getMessage() . "\n";
    echo "Please ensure your permissions are seeded correctly for the 'landlord' guard.\n";
}

// Clear permission cache just in case
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

echo "----------------------------------------\n";
echo "Landlord user is ready.\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n";
echo "----------------------------------------\n";
