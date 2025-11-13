<?php
// Quick script to create test users
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

// Create test school first
$school = School::firstOrCreate(
    ['subdomain' => 'frankhost'],
    [
        'name' => 'FrankHost School',
        'code' => 'FRANKHOST'
    ]
);

$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@frankhost.us',
        'password' => Hash::make('admin123'),
        'user_type' => 'admin',
        'school_id' => null,
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Test Teacher',
        'email' => 'teacher@frankhost.us', 
        'password' => Hash::make('teacher123'),
        'user_type' => 'teaching_staff',
        'school_id' => $school->id,
        'email_verified_at' => now(),
    ],
    [
        'name' => 'Test Student',
        'email' => 'student@frankhost.us',
        'password' => Hash::make('student123'),
        'user_type' => 'student', 
        'school_id' => $school->id,
        'email_verified_at' => now(),
    ]
];

echo "Creating test users...\n";

foreach ($users as $userData) {
    $existing = User::where('email', $userData['email'])->first();
    if ($existing) {
        echo "User {$userData['email']} already exists, updating...\n";
        $existing->update($userData);
        $user = $existing;
    } else {
        $user = User::create($userData);
        echo "Created user: {$userData['email']}\n";
    }
}

echo "\nTest users created successfully!\n\n";
echo "Login credentials:\n";
echo "=================\n";
echo "Admin: admin@frankhost.us / admin123\n";
echo "Teacher: teacher@frankhost.us / teacher123\n"; 
echo "Student: student@frankhost.us / student123\n";
echo "\nLogin at: https://frankhost.us/login\n";