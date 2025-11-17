@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Financial overview') }}</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" onclick="exportFinancial()">
                <i class="bi bi-download me-1"></i>{{ __('Export PDF') }}
            </button>
            <a class="btn btn-primary" href="{{ route('tenant.modules.financials.fees') }}">
                <i class="bi bi-cash me-1"></i>{{ __('Manage Fees') }}
            </a>
            <button class="btn btn-outline-secondary" onclick="exportFinancialCsv()">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ __('Export CSV') }}
            </button>
            <button class="btn btn-outline-primary" onclick="exportFinancialExcel()">
                <i class="bi bi-file-earmark-excel me-1"></i>{{ __('Export Excel') }}
            </button>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row">
        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Total Revenue') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_money($financialData['total_revenue']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('Monthly Revenue') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_money($financialData['monthly_revenue']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Fees Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('Pending Fees') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_money($financialData['pending_fees']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expenses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                {{ __('Total Expenses') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ format_money($financialData['expenses']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Recent Transactions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Details') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financialData['recent_transactions'] as $transaction)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $transaction['amount'] > 0 ? 'success' : 'danger' }}">
                                            {{ $transaction['type'] }}
                                        </span>
                                    </td>
                                    <td class="{{ $transaction['amount'] > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ format_money(abs($transaction['amount'])) }}
                                    </td>
                                    <td>{{ $transaction['date'] }}</td>
                                    <td>{{ $transaction['student'] ?? __('N/A') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ __('No recent transactions') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Quick Actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.modules.financials.fees') }}" class="btn btn-outline-primary">
                            <i class="bi bi-cash me-2"></i>{{ __('View Fee Collection') }}
                        </a>
                        <a href="{{ route('tenant.modules.financials.expenses') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-receipt me-2"></i>{{ __('Manage Expenses') }}
                        </a>
                        <a href="{{ route('tenant.modules.financials.tuition_plans') }}" class="btn btn-outline-info">
                            <i class="bi bi-calculator me-2"></i>{{ __('Tuition Plans') }}
                        </a>
                        <a href="{{ route('tenant.modules.financials.invoices') }}" class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-text me-2"></i>{{ __('View Invoices') }}
                        </a>
                        <button class="btn btn-outline-warning" onclick="generateReport()">
                            <i class="bi bi-graph-up me-2"></i>{{ __('Generate Report') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Financial Health Indicator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Financial Health') }}</h6>
                </div>
                <div class="card-body">
                    @php
                        $netIncome = $financialData['total_revenue'] - $financialData['expenses'];
                        $healthScore = $financialData['total_revenue'] > 0 ? min(100, round(($netIncome / $financialData['total_revenue']) * 100)) : 0;
                    @endphp

                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2">{{ __('Net Income') }}:</span>
                        <span class="fw-bold {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ format_money($netIncome) }}
                        </span>
                    </div>

                    <div class="progress mb-2">
                        <div class="progress-bar {{ $healthScore >= 70 ? 'bg-success' : ($healthScore >= 40 ? 'bg-warning' : 'bg-danger') }}"
                             role="progressbar"
                             style="width: {{ $healthScore }}%"
                             aria-valuenow="{{ $healthScore }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ $healthScore }}%
                        </div>
                    </div>

                    <small class="text-muted">
                        @if($healthScore >= 70)
                            {{ __('Excellent financial health') }}
                        @elseif($healthScore >= 40)
                            {{ __('Good financial health') }}
                        @else
                            {{ __('Needs attention') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportFinancial() {
    // Placeholder for PDF export functionality
    alert('{{ __("PDF export functionality will be implemented soon") }}');
}

function exportFinancialCsv() {
    // Placeholder for CSV export functionality
    alert('{{ __("CSV export functionality will be implemented soon") }}');
}

function exportFinancialExcel() {
    // Placeholder for Excel export functionality
    alert('{{ __("Excel export functionality will be implemented soon") }}');
}

function generateReport() {
    // Placeholder for report generation
    alert('{{ __("Report generation will be implemented soon") }}');
}
</script>
@endsection
