@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h4 fw-semibold mb-0">{{ __('Payroll Settings') }}</h1>
                <div class="small text-secondary">{{ __('Manage payroll policies, rates, and automation settings') }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.modules.human-resource.payroll-settings.edit') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> {{ __('Edit Settings') }}
                </a>
                <form method="POST" action="{{ route('tenant.modules.human-resource.payroll-settings.export') }}"
                    style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> {{ __('Export') }}
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Settings Overview Cards -->
        <div class="row g-4">

            <!-- Pay Period Settings -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            <h6 class="mb-0">{{ __('Pay Period') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Frequency') }}</small>
                            <div class="fw-semibold">
                                {{ $groupedSettings['pay_period']['pay_frequency']['options'][$currentSettings['pay_frequency'] ?? ''] ?? __('Not Set') }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Pay Day') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['pay_day'] ?? __('Not Set') }}</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Fiscal Year Start') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['fiscal_year_start'] ?? __('Not Set') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency Settings -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash text-success me-2"></i>
                            <h6 class="mb-0">{{ __('Currency') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Default Currency') }}</small>
                            <div class="fw-semibold">
                                {{ $groupedSettings['currency']['default_currency']['options'][$currentSettings['default_currency'] ?? ''] ?? __('Not Set') }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Symbol') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['currency_symbol'] ?? __('Not Set') }}</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Decimal Places') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['decimal_places'] ?? __('Not Set') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Components -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-piggy-bank text-warning me-2"></i>
                            <h6 class="mb-0">{{ __('Salary Components') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Basic Salary') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['basic_salary_percentage'] ?? 0 }}%</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('House Allowance') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['house_allowance_percentage'] ?? 0 }}%</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Transport Allowance') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['transport_allowance_percentage'] ?? 0 }}%</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Medical Allowance') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['medical_allowance_percentage'] ?? 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-dash-circle text-danger me-2"></i>
                            <h6 class="mb-0">{{ __('Deductions') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Income Tax') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['income_tax_rate'] ?? 0 }}%</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Social Security') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['social_security_rate'] ?? 0 }}%</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Provident Fund') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['provident_fund_rate'] ?? 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overtime -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-info me-2"></i>
                            <h6 class="mb-0">{{ __('Overtime') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Regular Rate') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['overtime_rate_regular'] ?? 0 }}x</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Holiday Rate') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['overtime_rate_holiday'] ?? 0 }}x</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Max Hours/Month') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['max_overtime_hours_monthly'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banking -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bank text-secondary me-2"></i>
                            <h6 class="mb-0">{{ __('Banking') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Bank Name') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['bank_name'] ?? __('Not Set') }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Account Number') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['bank_account_number'] ?? __('Not Set') }}</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Payment Method') }}</small>
                            <div class="fw-semibold">
                                {{ $groupedSettings['banking']['payment_method']['options'][$currentSettings['payment_method'] ?? ''] ?? __('Not Set') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check text-primary me-2"></i>
                            <h6 class="mb-0">{{ __('Compliance') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Minimum Wage') }}</small>
                            <div class="fw-semibold">{{ format_money($currentSettings['minimum_wage'] ?? 0) }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Hours/Day') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['working_hours_per_day'] ?? 0 }}</div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Days/Week') }}</small>
                            <div class="fw-semibold">{{ $currentSettings['working_days_per_week'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration -->
            <div class="col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-gear text-dark me-2"></i>
                            <h6 class="mb-0">{{ __('Integration') }}</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Auto Process Payroll') }}</small>
                            <div class="fw-semibold">
                                <span
                                    class="badge {{ $currentSettings['auto_process_payroll'] ?? false ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $currentSettings['auto_process_payroll'] ?? false ? __('Enabled') : __('Disabled') }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">{{ __('Email Pay Slips') }}</small>
                            <div class="fw-semibold">
                                <span
                                    class="badge {{ $currentSettings['email_pay_slips'] ?? false ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $currentSettings['email_pay_slips'] ?? false ? __('Enabled') : __('Disabled') }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-0">
                            <small class="text-muted">{{ __('Export to Accounting') }}</small>
                            <div class="fw-semibold">
                                <span
                                    class="badge {{ $currentSettings['export_to_accounting'] ?? false ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $currentSettings['export_to_accounting'] ?? false ? __('Enabled') : __('Disabled') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">{{ __('Quick Actions') }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('tenant.modules.human-resource.payroll-settings.edit') }}"
                            class="btn btn-outline-primary w-100">
                            <i class="bi bi-pencil-square"></i>
                            <div class="small">{{ __('Edit All Settings') }}</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <form method="POST" action="{{ route('tenant.modules.human-resource.payroll-settings.reset') }}"
                            style="display: inline;">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-outline-warning w-100"
                                onclick="return confirm('{{ __('Are you sure you want to reset all settings to defaults?') }}')">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <div class="small">{{ __('Reset to Defaults') }}</div>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form method="POST"
                            action="{{ route('tenant.modules.human-resource.payroll-settings.export') }}"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-download"></i>
                                <div class="small">{{ __('Export Settings') }}</div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Summary -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">{{ __('Settings Summary') }}</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="h4 text-primary mb-1">{{ $settings->count() }}</div>
                        <small class="text-muted">{{ __('Total Settings') }}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h4 text-success mb-1">{{ $settings->where('category', 'pay_period')->count() }}</div>
                        <small class="text-muted">{{ __('Pay Period') }}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h4 text-warning mb-1">{{ $settings->where('category', 'salary_components')->count() }}
                        </div>
                        <small class="text-muted">{{ __('Salary Components') }}</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h4 text-info mb-1">{{ $settings->where('category', 'deductions')->count() }}</div>
                        <small class="text-muted">{{ __('Deductions') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
