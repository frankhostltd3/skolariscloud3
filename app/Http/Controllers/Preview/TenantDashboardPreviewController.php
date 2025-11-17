<?php

namespace App\Http\Controllers\Preview;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use App\Models\User;

class TenantDashboardPreviewController extends Controller
{
    public function __invoke(string $tenant, string $role): View
    {
        // Initialize tenancy without switching DBs so views can resolve tenant() and team context
        /** @var Tenancy $tenancy */
        $tenancy = app(Tenancy::class);
        $original = $tenancy->getBootstrappersUsing;
        $tenancy->getBootstrappersUsing = function () {
            return array_values(array_filter(config('tenancy.bootstrappers'), function ($b) {
                return $b !== DatabaseTenancyBootstrapper::class;
            }));
        };

        // Resolve tenant by ID or by domain
        $model = Tenant::query()->find($tenant);
        if (! $model) {
            // Try exact domain match first (e.g., starlight-academy.localhost or foo.example.com)
            $domainRecord = Domain::query()->where('domain', $tenant)->first();
            if (! $domainRecord) {
                // If a slug-like value is given, try to append the APP_URL host (e.g., slug + .localhost)
                $host = parse_url(config('app.url'), PHP_URL_HOST);
                if ($host && ! str_contains($tenant, '.')) {
                    $maybeFqdn = $tenant . '.' . $host;
                    $domainRecord = Domain::query()->where('domain', $maybeFqdn)->first();
                }
            }

            $model = $domainRecord?->tenant;
        }

        abort_unless($model, 404);
        $tenancy->initialize($model);

        // Fake an authenticated user instance implementing Authenticatable
        $fake = new User([
            'name' => Str::title($role) . ' Preview',
            'email' => $role . '@preview.local',
        ]);
        // Avoid hitting guards/DB: mark as existing in memory for auth layer
        $fake->exists = true;
        // Optionally add a transient property to hint role checks in blade if needed
        $fake->preview_role = strtolower($role);
        Auth::setUser($fake);

        try {
            return match (strtolower($role)) {
                'admin' => view('tenant.admin.dashboard'),
                'staff' => view('tenant.staff.dashboard'),
                'student' => view('tenant.student.dashboard'),
                'parent' => view('tenant.parent.dashboard'),
                default => abort(404),
            };
        } finally {
            $tenancy->end();
            $tenancy->getBootstrappersUsing = $original;
        }
    }
}
