<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Middleware\IdentifySchoolFromHost;
use Illuminate\Http\Request;

echo "=== Testing Different Hosts ===\n";

$hosts = ['127.0.0.1:8000', '127.0.0.1', 'localhost:8000', 'localhost', 'frankhost.us'];

foreach ($hosts as $host) {
    echo "\n--- Testing host: {$host} ---\n";

    $request = Request::create("http://{$host}/login", 'GET');
    $request->headers->set('Host', $host);

    echo "Request Host: {$request->getHost()}\n";
    echo "Request Port: {$request->getPort()}\n";

    $middleware = new IdentifySchoolFromHost();

    $middleware->handle($request, function ($req) {
        $school = $req->attributes->get('currentSchool');
        if ($school) {
            echo "✓ School detected: {$school->name}\n";
        } else {
            echo "✗ No school detected\n";
        }
        return response('Test');
    });
}
