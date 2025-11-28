<?php

use App\Models\School;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

$subdomain = 'victorianileschool';
$school = School::where('subdomain', $subdomain)->first();

if (!$school) {
    echo "School not found for subdomain: $subdomain\n";
    exit(1);
}

echo "Found school: {$school->name} (ID: {$school->id})\n";

// Configure tenant connection
Config::set('database.connections.tenant.database', $school->database);
DB::purge('tenant');
DB::reconnect('tenant');

// Bind currentSchool for User model logic
app()->instance('currentSchool', $school);

// Find users with Teacher role but wrong user_type
$users = User::on('tenant')
    ->where('school_id', $school->id)
    ->get();

$count = 0;
foreach ($users as $user) {
    $roles = $user->getRoleNames();
    echo "User: {$user->name} (ID: {$user->id}, Type: {$user->user_type->value}, Roles: " . $roles->implode(', ') . ")\n";

    // Check if user has Teacher role
    if ($user->hasRole('Teacher')) {
        echo "Checking User: {$user->name} (ID: {$user->id}, Type: {$user->user_type->value})\n";

        if ($user->user_type !== UserType::TEACHING_STAFF) {
            echo "  - Fixing user_type to teaching_staff...\n";
            $user->user_type = UserType::TEACHING_STAFF;
            $user->save();
            $count++;
        }
    }
}

echo "Fixed $count users.\n";
