<?php

namespace App\Http\Middleware;

use App\Services\MailConfigurator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplySchoolMailConfiguration
{
    public function __construct(private MailConfigurator $configurator)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->configurator->apply();

        return $next($request);
    }
}
