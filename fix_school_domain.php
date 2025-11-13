<?php
// Fix school domain mapping
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\School;

$school = School::where('subdomain', 'frankhost')->first();

if ($school) {
    $school->domain = 'frankhost.us';
    $school->save();
    echo "Updated FrankHost school domain to: frankhost.us\n";
    echo "You can now login at: https://frankhost.us/login\n";
} else {
    echo "FrankHost school not found\n";
}