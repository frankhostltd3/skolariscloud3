<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

echo "=== Checking Users and Passwords ===\n";

$school = School::where('subdomain', 'frankhost')->first();
if (!$school) {
    echo "No school found with subdomain 'frankhost'\n";
    exit;
}

echo "School: {$school->name} (ID: {$school->id})\n";
echo "Domain: {$school->domain}\n";
echo "Subdomain: {$school->subdomain}\n\n";

$testCredentials = [
    'admin@frankhost.us' => 'admin123',
    'teacher@frankhost.us' => 'teacher123',
    'staff@frankhost.us' => 'staff123',
    'student@frankhost.us' => 'student123',
    'parent@frankhost.us' => 'parent123'
];

foreach ($testCredentials as $email => $password) {
    $user = User::where('email', $email)->where('school_id', $school->id)->first();

    if ($user) {
        $passwordCheck = Hash::check($password, $user->password);
        echo "✓ User: {$email}\n";
        echo "  - ID: {$user->id}\n";
        echo "  - Name: {$user->name}\n";
        echo "  - Type: {$user->user_type->value}\n";
        echo "  - School ID: {$user->school_id}\n";
        echo "  - Password Check: " . ($passwordCheck ? "✓ VALID" : "✗ INVALID") . "\n";
        if (!$passwordCheck) {
            echo "  - Stored Hash: " . substr($user->password, 0, 30) . "...\n";
        }
        echo "\n";
    } else {
        echo "✗ User not found: {$email}\n\n";
    }
}

echo "=== Testing Login Logic ===\n";
$testEmail = 'admin@frankhost.us';
$testPassword = 'admin123';

$user = User::where('email', $testEmail)->first();
if ($user) {
    echo "User found globally: {$user->name}\n";
    echo "User school_id: {$user->school_id}\n";
    echo "Expected school_id: {$school->id}\n";
    echo "School match: " . ($user->school_id == $school->id ? "✓ YES" : "✗ NO") . "\n";
    echo "Password check: " . (Hash::check($testPassword, $user->password) ? "✓ VALID" : "✗ INVALID") . "\n";
} else {
    echo "User not found globally: {$testEmail}\n";
}
