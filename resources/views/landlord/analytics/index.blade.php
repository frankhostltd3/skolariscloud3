@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-info-subtle text-info-emphasis px-3 py-2 mb-3">{{ __('Analytics') }}</span>
            <h1 class="h3 fw-semibold mb-2">{{ __('Spot expansion opportunities') }}</h1>
            <p class="text-secondary mb-0">{{ __('Track how quickly new schools are launching and which plans are winning across your regions.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-info btn-sm" type="button" disabled>
                <span class="bi bi-pie-chart me-2"></span>{{ __('Add chart') }}
            </button>
            <button class="btn btn-info btn-sm text-white" type="button" disabled>
                <span class="bi bi-share me-2"></span>{{ __('Share to stakeholders') }}
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- KPI cards -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('Overdue invoices') }}</div>
                    <div class="display-6 fw-semibold">{{ number_format($overdueInvoices ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('Open receivables') }}</div>
                    <div class="display-6 fw-semibold">${{ number_format($totalReceivables ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('Dunning (warning)') }}</div>
                    <div class="display-6 fw-semibold">{{ number_format(($dunningCounts['warning'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('Dunning (suspended)') }}</div>
                    <div class="display-6 fw-semibold">{{ number_format(($dunningCounts['suspended'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ __('New tenant momentum') }}</h2>
                        <span class="badge text-bg-light text-body border">{{ __('Last 6 months') }}</span>
                    </div>
                    <div class="mb-3">
                        <canvas id="chart-signups" height="120"></canvas>
                    </div>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        @forelse ($monthlySignups as $month => $total)
                            <li class="d-flex align-items-center justify-content-between">
                                <span class="text-secondary">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span>
                                <span class="fw-semibold">{{ $total }}</span>
                            </li>
                        @empty
                            <li class="text-secondary">{{ __('No tenant signups recorded yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ __('Plan velocity') }}</h2>
                        <span class="badge text-bg-light text-body border">{{ __('Current mix') }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-center">
                        <canvas id="chart-plans" height="180"></canvas>
                    </div>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        @forelse ($planVelocity as $plan => $total)
                            <li class="d-flex align-items-center justify-content-between">
                                <span class="text-secondary">{{ \Illuminate\Support\Str::of($plan)->headline() }}</span>
                                <span class="fw-semibold">{{ $total }}</span>
                            </li>
                        @empty
                            <li class="text-secondary">{{ __('No plan assignments yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ __('Revenue trend') }}</h2>
                        <span class="badge text-bg-light text-body border">{{ __('Last 6 months') }}</span>
                    </div>
                    <div class="mb-3">
                        <canvas id="chart-revenue" height="180"></canvas>
                    </div>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        @forelse ($revenueTrend as $month => $row)
                            <li class="d-flex align-items-center justify-content-between">
                                <span class="text-secondary">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span>
                                <span>
                                    <span class="badge text-bg-secondary-subtle me-1">{{ __('Invoiced') }}: ${{ number_format($row['invoiced'], 2) }}</span>
                                    <span class="badge text-bg-success-subtle">{{ __('Collected') }}: ${{ number_format($row['collected'], 2) }}</span>
                                </span>
                            </li>
                        @empty
                            <li class="text-secondary">{{ __('No billing activity yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-semibold mb-0">{{ __('Gateway conversion') }}</h2>
                        <span class="badge text-bg-light text-body border">{{ __('Attempts vs success') }}</span>
                    </div>
                    <div class="mb-3">
                        <canvas id="chart-gateways" height="180"></canvas>
                    </div>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        @forelse ($gatewayBreakdown as $row)
                            <li class="d-flex align-items-center justify-content-between">
                                <span class="text-secondary">{{ \Illuminate\Support\Str::headline($row['gateway'] ?? 'unknown') }}</span>
                                <span>
                                    <span class="badge text-bg-secondary-subtle me-1">{{ __('Attempts') }}: {{ $row['attempts'] }}</span>
                                    <span class="badge text-bg-success-subtle me-1">{{ __('Success') }}: {{ $row['successes'] }}</span>
                                    <span class="badge text-bg-info-subtle">{{ $row['conversion'] }}%</span>
                                </span>
                            </li>
                        @empty
                            <li class="text-secondary">{{ __('No payment transactions yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" integrity="sha384-sy0CfaI3Pq6I0bPUJg6omlJfDqC6mQxqXr9+u2k9nQkQh9lJmIb3O0jP8zI+T0uQ" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const res = await fetch(@json(route('landlord.analytics.data')), { cache: 'no-store' });
        const data = await res.json();

        // Resolve Bootstrap CSS variable colors for light/dark mode
        const css = getComputedStyle(document.body);
        const color = (name, fallback) => (css.getPropertyValue(name) || '').trim() || fallback;
        const colors = {
            text: color('--bs-body-color', '#212529'),
            grid: color('--bs-border-color', '#dee2e6'),
            primary: color('--bs-primary', '#0d6efd'),
            success: color('--bs-success', '#198754'),
            info: color('--bs-info', '#0dcaf0'),
            secondary: color('--bs-secondary', '#6c757d'),
            warning: color('--bs-warning', '#ffc107'),
            danger: color('--bs-danger', '#dc3545'),
        };

        const months = data.months;
        const invoiced = data.revenueTrend.map(r => r.invoiced);
        const collected = data.revenueTrend.map(r => r.collected);

        // Revenue line chart in fixed canvas
        const revCtx = document.getElementById('chart-revenue');
        if (revCtx) {
            new Chart(revCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        { label: 'Invoiced', data: invoiced, borderColor: colors.secondary, backgroundColor: 'transparent', tension: .3 },
                        { label: 'Collected', data: collected, borderColor: colors.success, backgroundColor: 'transparent', tension: .3 }
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: colors.text } } }, scales: { x: { ticks: { color: colors.text }, grid: { color: colors.grid } }, y: { ticks: { color: colors.text }, grid: { color: colors.grid } } } }
            });
        }

        // Signups sparkline
        const suCtx = document.getElementById('chart-signups');
        if (suCtx) {
            new Chart(suCtx, {
                type: 'line',
                data: { labels: months, datasets: [{ label: 'Signups', data: data.monthlySignups, borderColor: colors.info, backgroundColor: 'transparent', tension: .3, fill: false }] },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } }
            });
        }

        // Plan velocity doughnut
        const pvCtx = document.getElementById('chart-plans');
        if (pvCtx) {
            const plans = Object.keys(data.planVelocity);
            const planVals = Object.values(data.planVelocity);
            new Chart(pvCtx, {
                type: 'doughnut',
                data: { labels: plans, datasets: [{ data: planVals, backgroundColor: [colors.primary, colors.info, colors.secondary, colors.warning, colors.success, colors.danger, '#845ef7', '#e64980'] }] },
                options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: colors.text } } } }
            });
        }

        // Gateway conversion bar
        const gwCtx = document.getElementById('chart-gateways');
        if (gwCtx) {
            const gateways = data.gatewayBreakdown.map(g => g.gateway || 'unknown');
            const conversions = data.gatewayBreakdown.map(g => g.conversion);
            new Chart(gwCtx, {
                type: 'bar',
                data: { labels: gateways, datasets: [{ label: 'Conversion %', data: conversions, backgroundColor: colors.info }] },
                options: { responsive: true, plugins: { legend: { labels: { color: colors.text } } }, scales: { x: { ticks: { color: colors.text }, grid: { color: colors.grid } }, y: { ticks: { color: colors.text }, grid: { color: colors.grid }, beginAtZero: true, max: 100 } } }
            });
        }
    } catch (e) {
        console.error('Analytics charts failed:', e);
    }
});
</script>
@endpush
