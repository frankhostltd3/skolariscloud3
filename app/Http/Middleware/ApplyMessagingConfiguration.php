<?php

namespace App\Http\Middleware;

use App\Services\MessagingConfigurator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyMessagingConfiguration
{
    public function __construct(private MessagingConfigurator $configurator)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->configurator->apply();

        return $next($request);
    }
}
