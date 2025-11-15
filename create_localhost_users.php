<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\School;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;

echo "=== Creating users for localhost development ===\n";

$localhostSchool = School::where('domain', '127.0.0.1')->first();

if (!$localhostSchool) {
    echo "No localhost school found\n";
    exit;
}

echo "School: {$localhostSchool->name} (ID: {$localhostSchool->id})\n\n";

$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@localhost',
        'password' => Hash::make('admin123'),
        'user_type' => UserType::ADMIN,
    ],
    [
        'name' => 'Test Teacher',
        'email' => 'teacher@localhost',
        'password' => Hash::make('teacher123'),
        'user_type' => UserType::TEACHING_STAFF,
    ],
    [
        'name' => 'General Staff Member',
        'email' => 'staff@localhost',
        'password' => Hash::make('staff123'),
        'user_type' => UserType::GENERAL_STAFF,
    ],
    [
        'name' => 'Test Student',
        'email' => 'student@localhost',
        'password' => Hash::make('student123'),
        'user_type' => UserType::STUDENT,
    ],
    [
        'name' => 'Test Parent',
        'email' => 'parent@localhost',
        'password' => Hash::make('parent123'),
        'user_type' => UserType::PARENT,
    ],
];

foreach ($users as $userData) {
    // Delete existing user if exists
    $existing = User::where('email', $userData['email'])->where('school_id', $localhostSchool->id)->first();
    if ($existing) {
        $existing->delete();
        echo "Deleted existing user: {$userData['email']}\n";
    }

    $user = User::create([
        ...$userData,
        'school_id' => $localhostSchool->id,
        'email_verified_at' => now(),
    ]);

    echo "Created user: {$user->email}\n";
}

echo "\nLocalhost development users created successfully!\n\n";
echo "Login credentials for http://127.0.0.1:8000/login:\n";
echo "=================\n";
echo "Admin: admin@localhost / admin123\n";
echo "Teacher: teacher@localhost / teacher123\n";
echo "Staff: staff@localhost / staff123\n";
echo "Student: student@localhost / student123\n";
echo "Parent: parent@localhost / parent123\n";
