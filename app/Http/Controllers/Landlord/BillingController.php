<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Contracts\View\View;

class BillingController extends Controller
{
    public function __invoke(): View
    {
        $tenants = School::query()
            ->select(['id', 'name', 'meta', 'created_at'])
            ->get()
            ->map(function (School $school) {
                $meta = $school->meta ?? [];

                $plan = $meta['plan'] ?? 'starter';
                $mrr = match ($plan) {
                    'enterprise', 'custom' => 249,
                    'premium' => 189,
                    'growth' => 129,
                    default => 79,
                };

                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'plan' => $plan,
                    'mrr' => $mrr,
                    'country' => $meta['country'] ?? null,
                    'created_at' => $school->created_at,
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
