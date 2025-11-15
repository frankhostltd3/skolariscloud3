<?php

namespace App\Http\Middleware;

use App\Services\PaymentGatewayConfigurator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyPaymentGatewayConfiguration
{
    public function __construct(private PaymentGatewayConfigurator $configurator)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->configurator->apply();

        return $next($request);
    }
}
