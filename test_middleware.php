<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Middleware\IdentifySchoolFromHost;
use Illuminate\Http\Request;

echo "=== Testing Middleware with HTTP Request ===\n";

// Create a test request for frankhost.us
$request = Request::create('https://frankhost.us/login', 'GET');
$request->headers->set('Host', 'frankhost.us');

echo "Request URL: {$request->getUri()}\n";
echo "Request Host: {$request->getHost()}\n";

$middleware = new IdentifySchoolFromHost();

$response = $middleware->handle($request, function ($req) {
    $school = $req->attributes->get('currentSchool');
    if ($school) {
        echo "✓ School detected: {$school->name} (ID: {$school->id})\n";
        echo "  Domain: {$school->domain}\n";
        echo "  Subdomain: {$school->subdomain}\n";
    } else {
        echo "✗ No school detected\n";
    }

    return response('Test');
});

echo "\nMiddleware test completed.\n";
