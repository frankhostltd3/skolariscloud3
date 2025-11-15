<?php
// Debug school configuration and middleware
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\School;

echo "=== Current Schools Configuration ===\n";
$schools = School::all();
foreach($schools as $school) {
    echo "ID: {$school->id}\n";
    echo "Name: {$school->name}\n";
    echo "Subdomain: {$school->subdomain}\n";
    echo "Domain: {$school->domain}\n";
    echo "URL: {$school->url}\n";
    echo "---\n";
}

echo "\n=== Current Config ===\n";
echo "CENTRAL_DOMAIN: " . config('tenancy.central_domain') . "\n";
echo "APP_URL: " . config('app.url') . "\n";

echo "\n=== Test Host Resolution ===\n";
$host = 'frankhost.us';
echo "Testing host: {$host}\n";

// Simulate the corrected middleware logic
$centralDomain = config('tenancy.central_domain');
$query = School::query();

// First, always check for exact domain match
$query->where('domain', $host);

if ($centralDomain) {
    $trimmedCentral = ltrim($centralDomain, '.');
    echo "Central domain: {$trimmedCentral}\n";

    // Also check for subdomain-based schools if host is a subdomain
    if (str_ends_with($host, '.' . $trimmedCentral)) {
        $subdomain = substr($host, 0, strpos($host, '.' . $trimmedCentral));
        echo "Extracted subdomain: {$subdomain}\n";
        $query->orWhere('subdomain', $subdomain);
    }
}

$school = $query->first();

// If we found a school by domain or subdomain, return it
if ($school) {
    echo "School found by domain/subdomain: {$school->name}\n";
} else {
    echo "No school found by domain/subdomain\n";

    // If no school found and host matches central domain, check for a default school
    if ($centralDomain) {
        $trimmedCentral = ltrim($centralDomain, '.');
        if ($host === $trimmedCentral || $host === 'www.' . $trimmedCentral) {
            echo "Host matches central domain, looking for default school...\n";
            $defaultSchool = School::where('domain', $trimmedCentral)->first();
            if ($defaultSchool) {
                echo "Default school found: {$defaultSchool->name}\n";
                $school = $defaultSchool;
            } else {
                echo "No default school found for central domain\n";
            }
        }
    }

    if (!$school) {
        echo "No school found for host: {$host}\n";
    }
}
