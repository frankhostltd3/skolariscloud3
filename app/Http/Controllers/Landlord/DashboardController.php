<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Use App\Models\School instead of Stancl Tenant
        $recentSchools = School::query()
            ->latest()
            ->limit(8)
            ->get();

        // Map School model to the structure expected by the view
        $recentTenants = $recentSchools->map(function (School $school) {
            // Use direct attributes from School model
            $school->setAttribute('display_name', $school->name);

            // Try to get plan/country from meta, or default
            $meta = $school->meta ?? [];
            $school->setAttribute('plan_value', $meta['plan'] ?? 'unassigned');
            $school->setAttribute('country_code', $meta['country'] ?? null);

            // Use domain column or construct from subdomain
            $primaryDomain = $school->domain;
            if (!$primaryDomain && $school->subdomain) {
                $centralDomain = config('tenancy.central_domain', 'localhost');
                $primaryDomain = $school->subdomain . '.' . $centralDomain;
            }
            $school->setAttribute('primary_domain', $primaryDomain);

            return $school;
        });

        $allSchools = School::all();

        $planBreakdown = $allSchools
            ->groupBy(function (School $school) {
                $meta = $school->meta ?? [];
                return $meta['plan'] ?? 'unassigned';
            })
            ->map(function (Collection $group, string $plan) {
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

        $newThisMonth = $allSchools
            ->filter(fn (School $school) => $school->created_at && $school->created_at->greaterThanOrEqualTo(now()->startOfMonth()))
            ->count();

        return view('landlord.dashboard', [
            'recentTenants' => $recentTenants,
            'metrics' => [
                'totalTenants' => $allSchools->count(),
                'newTenantsThisMonth' => $newThisMonth,
                // Count schools that have a custom domain set
                'activeDomains' => $allSchools->whereNotNull('domain')->where('domain', '!=', '')->count(),
            ],
            'planBreakdown' => $planBreakdown,
        ]);
    }
}
