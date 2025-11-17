<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentTenants = Tenant::query()
            ->latest()
            ->limit(8)
            ->get();

        $tenantDomains = Domain::query()
            ->whereIn('tenant_id', $recentTenants->pluck('id'))
            ->get()
            ->groupBy('tenant_id');

        $payloadResolver = static function (Tenant $tenant): array {
            $data = $tenant->getAttribute('data');

            if (is_array($data) && $data !== []) {
                return $data;
            }

            $raw = $tenant->getRawOriginal('data');

            if (is_array($raw)) {
                return $raw;
            }

            if (is_string($raw)) {
                $decoded = json_decode($raw, true, flags: JSON_INVALID_UTF8_SUBSTITUTE);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }

            return [
                'name' => $tenant->getAttribute('name'),
                'plan' => $tenant->getAttribute('plan'),
                'country' => $tenant->getAttribute('country'),
            ];
        };

        $recentTenants = $recentTenants->map(function (Tenant $tenant) use ($tenantDomains, $payloadResolver) {
            $payload = $payloadResolver($tenant);
            $tenant->setAttribute('display_name', $payload['name'] ?? $tenant->id);
            $tenant->setAttribute('plan_value', $payload['plan'] ?? 'unassigned');
            $tenant->setAttribute('country_code', $payload['country'] ?? null);

            $primaryDomain = $tenantDomains->get($tenant->id)?->first();
            $tenant->setAttribute('primary_domain', $primaryDomain?->domain);

            return $tenant;
        });

        $allTenants = Tenant::query()->select(['id', 'data', 'created_at'])->get();

        $planBreakdown = $allTenants
            ->groupBy(static function (Tenant $tenant) use ($payloadResolver): string {
                $payload = $payloadResolver($tenant);

                return $payload['plan'] ?? 'unassigned';
            })
            ->map(static function (Collection $group, string $plan): array {
                $label = $plan === 'unassigned'
                    ? __('Unassigned')
                    : Str::of($plan)->headline();

                return [
                    'key' => $plan,
                    'label' => (string) $label,
                    'count' => $group->count(),
                ];
            })
            ->values();

        $newThisMonth = $allTenants
            ->filter(static fn (Tenant $tenant): bool => $tenant->created_at instanceof Carbon
                && $tenant->created_at->greaterThanOrEqualTo(now()->startOfMonth()))
            ->count();

        return view('landlord.dashboard', [
            'recentTenants' => $recentTenants,
            'metrics' => [
                'totalTenants' => $allTenants->count(),
                'newTenantsThisMonth' => $newThisMonth,
                'activeDomains' => Domain::query()->count(),
            ],
            'planBreakdown' => $planBreakdown,
        ]);
    }
}
