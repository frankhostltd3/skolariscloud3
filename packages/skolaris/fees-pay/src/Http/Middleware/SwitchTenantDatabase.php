<?php

namespace Skolaris\FeesPay\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Skolaris\FeesPay\Services\TenantService;

class SwitchTenantDatabase
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function handle(Request $request, Closure $next)
    {
        $tenantId = $request->header('X-Tenant-ID');

        if ($tenantId) {
            // In a real application, you would look up the database name for the tenant ID
            // For this example, we'll assume the tenant ID is the database name
            $databaseName = 'tenant_' . $tenantId;
            
            try {
                $this->tenantService->switchToTenant($databaseName);
            } catch (\Exception $e) {
                abort(404, 'Tenant database not found.');
            }
        }

        return $next($request);
    }
}
