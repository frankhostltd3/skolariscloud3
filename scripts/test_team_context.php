<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Middleware\IdentifySchoolFromHost;
use App\Http\Middleware\SetPermissionTeamContext;
use Spatie\Permission\PermissionRegistrar;

// Mock Request
$request = Request::create('http://victorianileschool.localhost:8000/dashboard/admin', 'GET');

// Chain Middlewares
$identify = new IdentifySchoolFromHost();
$setTeam = new SetPermissionTeamContext(app(PermissionRegistrar::class));

$identify->handle($request, function ($req) use ($setTeam) {
    return $setTeam->handle($req, function ($req) {
        $registrar = app(PermissionRegistrar::class);
        echo "Permission Team ID: " . $registrar->getPermissionsTeamId() . "\n";
        return new \Illuminate\Http\Response();
    });
});
