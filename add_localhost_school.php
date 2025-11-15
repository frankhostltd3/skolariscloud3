<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\School;

echo "=== Adding localhost support for development ===\n";

$school = School::where('subdomain', 'frankhost')->first();

if (!$school) {
    echo "No school found with subdomain 'frankhost'\n";
    exit;
}

echo "Current school configuration:\n";
echo "  Name: {$school->name}\n";
echo "  Domain: {$school->domain}\n";
echo "  Subdomain: {$school->subdomain}\n";

// For development, we can create a second school record for localhost
// Or we can modify the middleware to handle localhost specially

echo "\nCreating localhost development school...\n";

$localhostSchool = School::updateOrCreate(
    ['subdomain' => 'localhost'],
    [
        'name' => 'FrankHost School (Localhost)',
        'code' => 'LOCALHOST',
        'domain' => '127.0.0.1',
        'subdomain' => 'localhost'
    ]
);

echo "Created/Updated localhost school:\n";
echo "  ID: {$localhostSchool->id}\n";
echo "  Name: {$localhostSchool->name}\n";
echo "  Domain: {$localhostSchool->domain}\n";
echo "  Subdomain: {$localhostSchool->subdomain}\n";

echo "\nNow you can access the login at both:\n";
echo "  - https://frankhost.us/login (production)\n";
echo "  - http://127.0.0.1:8000/login (development)\n";
