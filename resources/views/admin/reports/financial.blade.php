@extends('tenant.layouts.app')

@section('title', 'Financial Reports')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-chart-pie me-2" style="color: #667eea;"></i>
                            Financial Reports
                        </h2>
                        <p class="text-muted mb-0">Monitor revenue, expenses, and financial performance</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success" onclick="exportFinancial()">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </button>
                        <button class="btn btn-outline-secondary" onclick="exportFinancialCsv()">
                            <i class="fas fa-file-csv me-2"></i>Export CSV
                        </button>
                        <button class="btn btn-outline-primary" onclick="exportFinancialExcel()">
                            <i class="fas fa-file-excel me-2"></i>Export Excel (XLSX)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Total Revenue</h6>
                                <h3 class="mb-0">{{ formatMoney($revenue ?? 0) }}</h3>
                                <small>In selected period</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Total Expenses</h6>
                                <h3 class="mb-0">{{ formatMoney($expenses ?? 0) }}</h3>
                                <small>In selected period</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-credit-card fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Net Profit</h6>
                                <h3 class="mb-0">{{ formatMoney($net ?? 0) }}</h3>
                                <small>&nbsp;</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Pending Fees</h6>
                                <h3 class="mb-0">{{ formatMoney($pendingFeesAmount ?? 0) }}</h3>
                                <small>{{ $pendingStudents ?? 0 }} students</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.financial') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="period" class="form-label">Period</label>
                            <select class="form-select" id="period" name="period">
                                <option value="this_month"
                                    {{ request('period', 'this_month') === 'this_month' ? 'selected' : '' }}>This Month
                                </option>
                                <option value="last_month" {{ request('period') === 'last_month' ? 'selected' : '' }}>Last
                                    Month</option>
                                <option value="this_quarter" {{ request('period') === 'this_quarter' ? 'selected' : '' }}>This
                                    Quarter</option>
                                <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>This Year
                                </option>
                                <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Custom Range
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="" {{ request('category') === '' ? 'selected' : '' }}>All Categories
                                </option>
                                @foreach ($expenseCategories ?? [] as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="" {{ request('payment_method') === '' ? 'selected' : '' }}>All Methods
                                </option>
                                <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>
                                    Credit/Debit Card</option>
                                <option value="bank_transfer"
                                    {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="check" {{ request('payment_method') === 'check' ? 'selected' : '' }}>Check
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3" id="date_from_group" style="display: none;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ request('start_date') }}" />
                        </div>
                        <div class="col-md-3 mb-3" id="date_to_group" style="display: none;">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ request('end_date') }}" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Revenue vs Expenses Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-area me-2 text-primary"></i>
                            Revenue vs Expenses Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueExpensesChart" width="400" height="200"
                            aria-label="Revenue versus expenses over time" role="img"
                            data-labels='@json($labels ?? [])' data-rev='@json($revSeries ?? [])'
                            data-exp='@json($expSeries ?? [])'></canvas>
                        @if (empty($labels))
                            <div class="text-muted small mt-3">No revenue/expense data for the selected filters.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Methods Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2 text-success"></i>
                            Payment Methods
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentMethodsChart" width="400" height="300"
                            aria-label="Payment methods distribution" role="img"
                            data-labels="{{ implode('|', array_keys($paymentMethods ?? [])) }}"
                            data-values="{{ implode('|', array_values($paymentMethods ?? [])) }}"></canvas>
                        @if (empty($paymentMethods))
                            <div class="text-muted small mt-3">No payments recorded in this period.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Fee Collection Status -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-check-alt me-2 text-info"></i>
                            Fee Collection Status
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse(($classCollection ?? []) as $class)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-medium">{{ $class['class'] }}</span>
                                    <div class="text-end">
                                        <span class="fw-bold">{{ formatMoney($class['amount']) }}</span>
                                        <small class="text-muted d-block">{{ $class['collected'] }}% collected</small>
                                    </div>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success fee-collected"
                                        data-width="{{ $class['collected'] }}" style="width:0%"></div>
                                    <div class="progress-bar bg-warning fee-pending" data-width="{{ $class['pending'] }}"
                                        style="width:0%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-success">Collected: {{ $class['collected'] }}%</small>
                                    <small class="text-warning">Pending: {{ $class['pending'] }}%</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No fee collection data available.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2 text-warning"></i>
                            Expense Breakdown
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="expenseBreakdownChart" width="400" height="250"
                            aria-label="Expense breakdown by category" role="img"
                            data-labels='@json($expenseLabels ?? [])'
                            data-values='@json($expenseValues ?? [])'></canvas>
                        @if (empty($expenseLabels))
                            <div class="text-muted small mt-2">No expenses for the selected filters.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>
                            Recent Transactions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($recentTransactions ?? []) as $transaction)
                                        <tr>
                                            <td>{{ $transaction['date'] }}</td>
                                            <td>{{ $transaction['description'] }}</td>
                                            <td><span class="badge bg-secondary">{{ $transaction['category'] }}</span>
                                            </td>
                                            <td>{{ $transaction['method'] }}</td>
                                            <td>
                                                <span
                                                    class="fw-bold text-{{ $transaction['type'] == 'income' ? 'success' : 'danger' }}">
                                                    {{ formatMoney(abs($transaction['amount'])) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $transaction['status'] == 'Completed' ? 'success' : 'warning' }}">
                                                    {{ $transaction['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No recent transactions
                                                found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outstanding Payments -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2 text-danger"></i>
                            Outstanding Payments
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse(($outstandingList ?? []) as $payment)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                                <div>
                                    <h6 class="mb-1">{{ $payment['student'] }}</h6>
                                    <small class="text-muted">{{ $payment['type'] }} Fee</small>
                                    <div class="mt-1">
                                        <span
                                            class="badge bg-{{ $payment['days'] > 15 ? 'danger' : ($payment['days'] > 7 ? 'warning' : 'info') }}">
                                            {{ $payment['days'] }} days overdue
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-danger">{{ formatMoney($payment['amount']) }}</div>
                                    <button class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No outstanding invoices.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="exportFinancialForm" action="{{ route('admin.reports.export-pdf') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="financial">
        <input type="hidden" name="period" value="{{ request('period', 'this_month') }}">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
    </form>

    <form id="exportFinancialCsvForm" action="{{ route('admin.reports.export-excel') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="financial">
        <input type="hidden" name="period" value="{{ request('period', 'this_month') }}">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
    </form>

    <form id="exportFinancialExcelForm" action="{{ route('admin.reports.export-excel') }}" method="POST"
        class="d-none">
        @csrf
        <input type="hidden" name="type" value="financial">
        <input type="hidden" name="period" value="{{ request('period', 'this_month') }}">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        <input type="hidden" name="output_format" value="excel">
        <input type="hidden" name="format" value="xlsx">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue vs Expenses Chart (live)
        const revEl = document.getElementById('revenueExpensesChart');
        if (revEl) {
            const revenueCtx = revEl.getContext('2d');
            const revLabels = JSON.parse(revEl.dataset.labels || '[]');
            const revData = JSON.parse(revEl.dataset.rev || '[]');
            const expData = JSON.parse(revEl.dataset.exp || '[]');
            if (revLabels.length) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: revLabels,
                        datasets: [{
                                label: 'Revenue',
                                data: revData,
                                borderColor: 'rgba(40,167,69,1)',
                                backgroundColor: 'rgba(40,167,69,0.12)',
                                tension: .35,
                                fill: true,
                                pointRadius: 3
                            },
                            {
                                label: 'Expenses',
                                data: expData,
                                borderColor: 'rgba(220,53,69,1)',
                                backgroundColor: 'rgba(220,53,69,0.12)',
                                tension: .35,
                                fill: true,
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        // Payment Methods Chart
        const paymentCanvas = document.getElementById('paymentMethodsChart');
        if (paymentCanvas) {
            const paymentCtx = paymentCanvas.getContext('2d');
            const labelsAttr = paymentCanvas.dataset.labels || '';
            const valuesAttr = paymentCanvas.dataset.values || '';
            const pmLabelsRaw = labelsAttr ? labelsAttr.split('|').filter(Boolean) : [];
            const pmDataRaw = valuesAttr ? valuesAttr.split('|').map(v => parseFloat(v) || 0) : [];
            if (pmLabelsRaw.length) {
                new Chart(paymentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: pmLabelsRaw,
                        datasets: [{
                            data: pmDataRaw,
                            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6c757d', '#dc3545']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }

        // Expense Breakdown Chart (live)
        const expenseEl = document.getElementById('expenseBreakdownChart');
        if (expenseEl) {
            const expenseCtx = expenseEl.getContext('2d');
            const expenseLabels = JSON.parse(expenseEl.dataset.labels || '[]');
            const expenseValues = JSON.parse(expenseEl.dataset.values || '[]');
            if (expenseLabels.length) {
                new Chart(expenseCtx, {
                    type: 'bar',
                    data: {
                        labels: expenseLabels,
                        datasets: [{
                            label: 'Amount',
                            data: expenseValues,
                            backgroundColor: '#0d6efd'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        function exportFinancial() {
            document.getElementById('exportFinancialForm').submit();
        }

        function exportFinancialCsv() {
            document.getElementById('exportFinancialCsvForm').submit();
        }

        function exportFinancialExcel() {
            document.getElementById('exportFinancialExcelForm').submit();
        }

        // Animate fee collection bars
        document.querySelectorAll('.fee-collected,.fee-pending').forEach(el => {
            const w = parseFloat(el.dataset.width || '0');
            requestAnimationFrame(() => el.style.width = (isNaN(w) ? 0 : w) + '%');
        });

        // Show/hide custom date range based on selected period
        (function() {
            const periodSel = document.getElementById('period');
            const gFrom = document.getElementById('date_from_group');
            const gTo = document.getElementById('date_to_group');

            function syncDates() {
                const isCustom = periodSel && periodSel.value === 'custom';
                if (gFrom) gFrom.style.display = isCustom ? '' : 'none';
                if (gTo) gTo.style.display = isCustom ? '' : 'none';
            }
            if (periodSel) {
                periodSel.addEventListener('change', syncDates);
                syncDates();
            }
        })();
    </script>
@endsection
