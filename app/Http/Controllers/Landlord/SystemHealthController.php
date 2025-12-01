<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

class SystemHealthController extends Controller
{
    public function __invoke(HealthCheckResultsController $controller)
    {
        return $controller();
    }
}
