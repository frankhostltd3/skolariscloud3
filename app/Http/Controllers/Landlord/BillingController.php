<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Stancl\Tenancy\Database\Models\Tenant;

class BillingController extends Controller
{
    public function __invoke(): View
    {
        $tenants = Tenant::query()
            ->select(['id', 'data', 'created_at'])
            ->get()
            ->map(function (Tenant $tenant) {
                $payload = $tenant->getAttribute('data');

                if (is_string($payload)) {
                    $payload = json_decode($payload, true) ?: [];
                }

                $plan = $payload['plan'] ?? 'starter';
                $mrr = match ($plan) {
                    'enterprise', 'custom' => 249,
                    'premium' => 189,
                    'growth' => 129,
                    default => 79,
                };

                return [
                    'id' => $tenant->id,
                    'name' => $payload['name'] ?? $tenant->id,
                    'plan' => $plan,
                    'mrr' => $mrr,
                    'country' => $payload['country'] ?? null,
                    'created_at' => $tenant->created_at,
                ];
            });

        $totals = [
            'mrr' => $tenants->sum('mrr'),
            'enterpriseCount' => $tenants->where('plan', 'enterprise')->count(),
            'growthCount' => $tenants->where('plan', 'growth')->count(),
            'premiumCount' => $tenants->where('plan', 'premium')->count(),
        ];

        return view('landlord.billing.index', [
            'tenants' => $tenants,
            'totals' => $totals,
        ]);
    }
}
