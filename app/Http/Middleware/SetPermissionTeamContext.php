<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionTeamContext
{
    public function __construct(private PermissionRegistrar $registrar)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $previousTeam = $this->registrar->getPermissionsTeamId();
        $tenantId = $this->resolveTenantId($request);

        $this->registrar->setPermissionsTeamId($tenantId);

        try {
            return $next($request);
        } finally {
            if ($tenantId === config('app.landlord_team_id', 'skolaris-root')) {
                $this->registrar->setPermissionsTeamId($tenantId);
            } else {
                $this->registrar->setPermissionsTeamId($previousTeam);
            }
        }
    }

    private function resolveTenantId(Request $request): int|string|null
    {
        if ($this->isLandlordRequest($request)) {
            return config('app.landlord_team_id', 'skolaris-root');
        }

        if (! function_exists('tenant')) {
            return null;
        }

        $tenant = tenant();

        if (! $tenant) {
            return null;
        }

        if (method_exists($tenant, 'getTenantKey')) {
            return $tenant->getTenantKey();
        }

        if (method_exists($tenant, 'getKey')) {
            return $tenant->getKey();
        }

        return $tenant->id ?? null;
    }

    private function isLandlordRequest(Request $request): bool
    {
        if ($request->route() && $request->route()->named('landlord.*')) {
            return true;
        }

        $path = trim($request->path(), '/');

        return str_starts_with($path, 'landlord');
    }
}
