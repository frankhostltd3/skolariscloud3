<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\BillingPlanRequest;
use App\Models\BillingPlan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PlansController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Ensure landlord context is set for permissions
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId('landlord');
            return $next($request);
        });

        $this->middleware('permission:manage landlord billing,landlord');
    }

    public function index(): View
    {
        $plans = BillingPlan::query()->ordered()->paginate(12);

        return view('landlord.billing.plans.index', [
            'plans' => $plans,
        ]);
    }

    public function create(): View
    {
        return view('landlord.billing.plans.create', [
            'plan' => new BillingPlan([
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'billing_period_label' => __('per month'),
                'cta_label' => __('Talk to us'),
                'is_active' => true,
                'is_highlighted' => false,
            ]),
            'currencies' => $this->currencyOptions(),
        ]);
    }

    public function store(BillingPlanRequest $request): RedirectResponse
    {
        $payload = $this->preparePayload($request);

        BillingPlan::query()->create($payload);

        return redirect()
            ->route('landlord.billing.plans.index')
            ->with('status', __('Plan created successfully.'));
    }

    public function edit(BillingPlan $plan): View
    {
        return view('landlord.billing.plans.edit', [
            'plan' => $plan,
            'currencies' => $this->currencyOptions(),
        ]);
    }

    public function update(BillingPlanRequest $request, BillingPlan $plan): RedirectResponse
    {
        $payload = $this->preparePayload($request, $plan);

        $plan->update($payload);

        return redirect()
            ->route('landlord.billing.plans.index')
            ->with('status', __('Plan updated successfully.'));
    }

    public function destroy(BillingPlan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()
            ->route('landlord.billing.plans.index')
            ->with('status', __('Plan deleted.'));
    }

    /**
     * @return array<int, array{code: string, label: string}>
     */
    private function currencyOptions(): array
    {
        return [
            ['code' => 'USD', 'label' => 'USD — United States Dollar'],
            ['code' => 'KES', 'label' => 'KES — Kenyan Shilling'],
            ['code' => 'NGN', 'label' => 'NGN — Nigerian Naira'],
            ['code' => 'UGX', 'label' => 'UGX — Ugandan Shilling'],
            ['code' => 'TZS', 'label' => 'TZS — Tanzanian Shilling'],
            ['code' => 'ZAR', 'label' => 'ZAR — South African Rand'],
            ['code' => 'GHS', 'label' => 'GHS — Ghanaian Cedi'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function preparePayload(BillingPlanRequest $request, ?BillingPlan $existing = null): array
    {
        $data = $request->validated();

        $slug = Arr::get($data, 'slug');
        $name = Arr::get($data, 'name', '');

        if (! $slug) {
            $slug = Str::slug($name);
        }

        $slug = $this->ensureUniqueSlug($slug, $existing);

        $data['slug'] = $slug;
        $data['position'] = Arr::get($data, 'position', 0) ?? 0;
        $data['is_highlighted'] = (bool) ($data['is_highlighted'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['features'] = $this->splitFeatures(Arr::get($data, 'features'));

        if ($data['features'] === []) {
            $data['features'] = null;
        }

        return $data;
    }

    private function splitFeatures(?string $features): array
    {
        if ($features === null || trim($features) === '') {
            return [];
        }

        $lines = preg_split('/\r?\n/', $features) ?: [];

        return array_values(array_filter(array_map(static fn (string $line): string => trim($line), $lines), static fn ($value): bool => $value !== ''));
    }

    private function ensureUniqueSlug(string $desiredSlug, ?BillingPlan $existing = null): string
    {
        $baseSlug = Str::slug($desiredSlug);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $existing)) {
            $slug = $baseSlug . '-' . (++$counter);
        }

        return $slug;
    }

    private function slugExists(string $slug, ?BillingPlan $existing = null): bool
    {
        $query = BillingPlan::query()->where('slug', $slug);

        if ($existing !== null) {
            $query->where('id', '!=', $existing->getKey());
        }

        return $query->exists();
    }
}
