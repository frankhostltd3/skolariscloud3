@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-warning-subtle text-warning-emphasis px-3 py-2 mb-3">{{ __('Billing & plans') }}</span>
            <h1 class="h3 fw-semibold mb-2">{{ __('Forecast revenue and reconcile tenant plans') }}</h1>
            <p class="text-secondary mb-0">{{ __('Track monthly recurring revenue and quickly spot which schools need plan reviews or payment nudges.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('landlord.billing.plans.index') }}">
                <span class="bi bi-tags me-2"></span>{{ __('Open pricing catalogue') }}
            </a>
            <button class="btn btn-outline-warning btn-sm" type="button" disabled>
                <span class="bi bi-bell me-2"></span>{{ __('Configure alerts') }}
            </button>
            <button class="btn btn-warning btn-sm text-white" type="button" disabled>
                <span class="bi bi-file-earmark-bar-graph me-2"></span>{{ __('Download summary') }}
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-1">{{ __('Monthly recurring revenue') }}</p>
                    <p class="display-6 fw-semibold mb-0">${{ number_format($totals['mrr']) }}</p>
                    <p class="text-secondary small mb-0">{{ __('Modelled from current plan mix') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-1">{{ __('Enterprise tenants') }}</p>
                    <p class="display-6 fw-semibold mb-0">{{ $totals['enterpriseCount'] }}</p>
                    <p class="text-secondary small mb-0">{{ __('High-touch onboarding') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-1">{{ __('Premium tenants') }}</p>
                    <p class="display-6 fw-semibold mb-0">{{ $totals['premiumCount'] }}</p>
                    <p class="text-secondary small mb-0">{{ __('Dedicated success partner') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-secondary text-uppercase small mb-1">{{ __('Growth tenants') }}</p>
                    <p class="display-6 fw-semibold mb-0">{{ $totals['growthCount'] }}</p>
                    <p class="text-secondary small mb-0">{{ __('Scaling multi-campus groups') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="text-secondary text-uppercase small">
                    <tr>
                        <th scope="col">{{ __('Tenant') }}</th>
                        <th scope="col">{{ __('Plan') }}</th>
                        <th scope="col" class="text-end">{{ __('Modeled MRR') }}</th>
                        <th scope="col">{{ __('Region') }}</th>
                        <th scope="col" class="text-end">{{ __('Onboarded') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tenants as $tenant)
                        <tr>
                            <td class="fw-semibold">{{ $tenant['name'] }}</td>
                            <td><span class="badge text-bg-light text-body border">{{ \Illuminate\Support\Str::of($tenant['plan'])->headline() }}</span></td>
                            <td class="text-end fw-semibold">${{ number_format($tenant['mrr']) }}</td>
                            <td class="text-secondary small">{{ strtoupper($tenant['country'] ?? '—') }}</td>
                            <td class="text-end text-secondary small">{{ optional($tenant['created_at'])->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">{{ __('No billing data yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
