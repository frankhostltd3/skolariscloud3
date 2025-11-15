<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\School;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;

echo "=== Fixing Admin User ===\n";

$school = School::where('subdomain', 'frankhost')->first();
if (!$school) {
    echo "No school found with subdomain 'frankhost'\n";
    exit;
}

// Delete existing admin users that might conflict
$existingAdmins = User::where('email', 'admin@frankhost.us')->get();
foreach ($existingAdmins as $admin) {
    echo "Deleting existing admin user: ID {$admin->id}\n";
    $admin->delete();
}

// Also check for the default admin without school_id
$defaultAdmin = User::whereNull('school_id')->where('email', 'test@example.com')->first();
if ($defaultAdmin) {
    echo "Found default admin without school_id, deleting: {$defaultAdmin->email}\n";
    $defaultAdmin->delete();
}

// Create new admin user
$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@frankhost.us',
    'password' => Hash::make('admin123'),
    'user_type' => UserType::ADMIN,
    'school_id' => $school->id,
    'email_verified_at' => now(),
]);

echo "Created new admin user:\n";
echo "  - Email: {$admin->email}\n";
echo "  - Name: {$admin->name}\n";
echo "  - Type: {$admin->user_type->value}\n";
echo "  - School ID: {$admin->school_id}\n";

// Verify password
$passwordCheck = Hash::check('admin123', $admin->password);
echo "  - Password Check: " . ($passwordCheck ? "✓ VALID" : "✗ INVALID") . "\n";

echo "\nAdmin user fixed successfully!\n";
