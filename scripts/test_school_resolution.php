<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Middleware\IdentifySchoolFromHost;

// Mock Request
$request = Request::create('http://victorianileschool.localhost:8000/dashboard/admin', 'GET');

// Run Middleware
$middleware = new IdentifySchoolFromHost();
$middleware->handle($request, function ($req) {
    $school = app('currentSchool');
    if ($school) {
        echo "Resolved School: " . $school->name . " (ID: " . $school->id . ")\n";
    } else {
        echo "School NOT resolved.\n";
    }
    return new \Illuminate\Http\Response();
});
