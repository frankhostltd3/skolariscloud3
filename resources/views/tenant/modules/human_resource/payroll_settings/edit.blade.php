@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h4 fw-semibold mb-0">{{ __('Payroll Settings') }}</h1>
      <div class="small text-secondary">{{ __('Configure payroll policies, rates, and automation settings') }}</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('tenant.modules.human_resources.payroll-settings.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> {{ __('Back to Overview') }}
      </a>
      <form method="POST" action="{{ route('tenant.modules.human_resources.payroll-settings.reset') }}" style="display: inline;">
        @csrf
        @method('POST')
        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('{{ __('Are you sure you want to reset all settings to defaults?') }}')">
          <i class="bi bi-arrow-counterclockwise"></i> {{ __('Reset to Defaults') }}
        </button>
      </form>
    </div>
  </div>

  <form method="POST" action="{{ route('tenant.modules.human_resources.payroll-settings.update') }}">
    @csrf
    @method('PUT')

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="payrollTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pay-period-tab" data-bs-toggle="tab" data-bs-target="#pay-period" type="button" role="tab">
          <i class="bi bi-calendar-event"></i> {{ __('Pay Period') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="currency-tab" data-bs-toggle="tab" data-bs-target="#currency" type="button" role="tab">
          <i class="bi bi-cash"></i> {{ __('Currency') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab">
          <i class="bi bi-piggy-bank"></i> {{ __('Salary Components') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="deductions-tab" data-bs-toggle="tab" data-bs-target="#deductions" type="button" role="tab">
          <i class="bi bi-dash-circle"></i> {{ __('Deductions') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="overtime-tab" data-bs-toggle="tab" data-bs-target="#overtime" type="button" role="tab">
          <i class="bi bi-clock"></i> {{ __('Overtime') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking" type="button" role="tab">
          <i class="bi bi-bank"></i> {{ __('Banking') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="compliance-tab" data-bs-toggle="tab" data-bs-target="#compliance" type="button" role="tab">
          <i class="bi bi-shield-check"></i> {{ __('Compliance') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="integration-tab" data-bs-toggle="tab" data-bs-target="#integration" type="button" role="tab">
          <i class="bi bi-gear"></i> {{ __('Integration') }}
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="payrollTabContent">

      <!-- Pay Period Tab -->
      <div class="tab-pane fade show active" id="pay-period" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Pay Period & Frequency Settings') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pay_frequency" class="form-label">{{ __('Pay Frequency') }}</label>
                  @php
                    $payFreqOptions = $groupedSettings['pay_period']['pay_frequency']['options'] ?? [];
                    if(empty($payFreqOptions) && function_exists('config')) {
                        $payFreqOptions = collect(config('payroll.frequencies', []))
                          ->mapWithKeys(fn($v,$k)=>[$k => $v['label'] ?? ucfirst($k)])->toArray();
                    }
                  @endphp
                  <select class="form-select" id="pay_frequency" name="pay_frequency">
                    @foreach($payFreqOptions as $key => $label)
                      <option value="{{ $key }}" {{ ($currentSettings['pay_frequency'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                  </select>
                  <div class="form-text">{{ $groupedSettings['pay_period']['pay_frequency']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pay_day" class="form-label">{{ __('Pay Day') }}</label>
                  <input type="number" class="form-control" id="pay_day" name="pay_day"
                         value="{{ $currentSettings['pay_day'] ?? '' }}" min="1" max="31">
                  <div class="form-text">{{ $groupedSettings['pay_period']['pay_day']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="fiscal_year_start" class="form-label">{{ __('Fiscal Year Start') }}</label>
                  <input type="text" class="form-control" id="fiscal_year_start" name="fiscal_year_start"
                         value="{{ $currentSettings['fiscal_year_start'] ?? '' }}" placeholder="MM-DD">
                  <div class="form-text">{{ $groupedSettings['pay_period']['fiscal_year_start']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Currency Tab -->
      <div class="tab-pane fade" id="currency" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Currency & Localization Settings') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="default_currency" class="form-label">{{ __('Default Currency') }}</label>
                  @php
                    $currencyOptions = $groupedSettings['currency']['default_currency']['options'] ?? [];
                    if(empty($currencyOptions)) {
                        $currencyOptions = [
                          'USD' => 'USD - US Dollar',
                          'EUR' => 'EUR - Euro',
                          'GBP' => 'GBP - British Pound',
                          'UGX' => 'UGX - Ugandan Shilling',
                          'KES' => 'KES - Kenyan Shilling',
                          'TZS' => 'TZS - Tanzanian Shilling',
                        ];
                    }
                  @endphp
                  <select class="form-select" id="default_currency" name="default_currency">
                    @foreach($currencyOptions as $code => $label)
                      <option value="{{ $code }}" {{ ($currentSettings['default_currency'] ?? '') == $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                  </select>
                  <div class="form-text">{{ $groupedSettings['currency']['default_currency']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="currency_symbol" class="form-label">{{ __('Currency Symbol') }}</label>
                  <input type="text" class="form-control" id="currency_symbol" name="currency_symbol"
                         value="{{ $currentSettings['currency_symbol'] ?? '' }}" maxlength="5" 
                         placeholder="Auto-derived from currency">
                  <div class="form-text">{{ $groupedSettings['currency']['currency_symbol']['description'] ?? 'Symbol automatically derived from default currency (e.g., $ for USD, £ for GBP, USh for UGX). Override if needed.' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="decimal_places" class="form-label">{{ __('Decimal Places') }}</label>
                  <input type="number" class="form-control" id="decimal_places" name="decimal_places"
                         value="{{ $currentSettings['decimal_places'] ?? '' }}" min="0" max="4">
                  <div class="form-text">{{ $groupedSettings['currency']['decimal_places']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Salary Components Tab -->
      <div class="tab-pane fade" id="salary" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Salary Components & Allowances') }}</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-info">
              <i class="bi bi-info-circle"></i>
              {{ __('These percentages should add up to 100% of the total salary package.') }}
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="basic_salary_percentage" class="form-label">{{ __('Basic Salary (%)') }}</label>
                  <input type="number" class="form-control" id="basic_salary_percentage" name="basic_salary_percentage"
                         value="{{ $currentSettings['basic_salary_percentage'] ?? '' }}" min="0" max="100" step="0.01">
                  <div class="form-text">{{ $groupedSettings['salary_components']['basic_salary_percentage']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="house_allowance_percentage" class="form-label">{{ __('House Allowance (%)') }}</label>
                  <input type="number" class="form-control" id="house_allowance_percentage" name="house_allowance_percentage"
                         value="{{ $currentSettings['house_allowance_percentage'] ?? '' }}" min="0" max="100" step="0.01">
                  <div class="form-text">{{ $groupedSettings['salary_components']['house_allowance_percentage']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="transport_allowance_percentage" class="form-label">{{ __('Transport Allowance (%)') }}</label>
                  <input type="number" class="form-control" id="transport_allowance_percentage" name="transport_allowance_percentage"
                         value="{{ $currentSettings['transport_allowance_percentage'] ?? '' }}" min="0" max="100" step="0.01">
                  <div class="form-text">{{ $groupedSettings['salary_components']['transport_allowance_percentage']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="medical_allowance_percentage" class="form-label">{{ __('Medical Allowance (%)') }}</label>
                  <input type="number" class="form-control" id="medical_allowance_percentage" name="medical_allowance_percentage"
                         value="{{ $currentSettings['medical_allowance_percentage'] ?? '' }}" min="0" max="100" step="0.01">
                  <div class="form-text">{{ $groupedSettings['salary_components']['medical_allowance_percentage']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
            <div class="mt-3">
              <strong>{{ __('Total:') }}</strong> <span id="total-percentage" class="badge bg-info">0%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Deductions Tab -->
      <div class="tab-pane fade" id="deductions" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Tax & Deduction Settings') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="income_tax_rate" class="form-label">{{ __('Income Tax Rate (%)') }}</label>
                  <input type="number" class="form-control" id="income_tax_rate" name="income_tax_rate"
                         value="{{ $currentSettings['income_tax_rate'] ?? '' }}" min="0" max="100" step="0.01">
                  <div class="form-text">{{ $groupedSettings['deductions']['income_tax_rate']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="social_security_rate" class="form-label">{{ __('Social Security Rate (%)') }}</label>
                  <input type="number" class="form-control" id="social_security_rate" name="social_security_rate"
                         value="{{ $currentSettings['social_security_rate'] ?? '' }}" min="0" max="50" step="0.01">
                  <div class="form-text">{{ $groupedSettings['deductions']['social_security_rate']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="provident_fund_rate" class="form-label">{{ __('Provident Fund Rate (%)') }}</label>
                  <input type="number" class="form-control" id="provident_fund_rate" name="provident_fund_rate"
                         value="{{ $currentSettings['provident_fund_rate'] ?? '' }}" min="0" max="50" step="0.01">
                  <div class="form-text">{{ $groupedSettings['deductions']['provident_fund_rate']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Overtime Tab -->
      <div class="tab-pane fade" id="overtime" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Overtime & Leave Policies') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="overtime_rate_regular" class="form-label">{{ __('Regular Overtime Rate') }}</label>
                  <input type="number" class="form-control" id="overtime_rate_regular" name="overtime_rate_regular"
                         value="{{ $currentSettings['overtime_rate_regular'] ?? '' }}" min="1" max="5" step="0.1">
                  <div class="form-text">{{ $groupedSettings['overtime']['overtime_rate_regular']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="overtime_rate_holiday" class="form-label">{{ __('Holiday Overtime Rate') }}</label>
                  <input type="number" class="form-control" id="overtime_rate_holiday" name="overtime_rate_holiday"
                         value="{{ $currentSettings['overtime_rate_holiday'] ?? '' }}" min="1" max="5" step="0.1">
                  <div class="form-text">{{ $groupedSettings['overtime']['overtime_rate_holiday']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="max_overtime_hours_monthly" class="form-label">{{ __('Max Overtime Hours (Monthly)') }}</label>
                  <input type="number" class="form-control" id="max_overtime_hours_monthly" name="max_overtime_hours_monthly"
                         value="{{ $currentSettings['max_overtime_hours_monthly'] ?? '' }}" min="0" max="200">
                  <div class="form-text">{{ $groupedSettings['overtime']['max_overtime_hours_monthly']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Banking Tab -->
      <div class="tab-pane fade" id="banking" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Banking & Payment Settings') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="bank_name" class="form-label">{{ __('Bank Name') }}</label>
                  <input type="text" class="form-control" id="bank_name" name="bank_name"
                         value="{{ $currentSettings['bank_name'] ?? '' }}" maxlength="255">
                  <div class="form-text">{{ $groupedSettings['banking']['bank_name']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="bank_account_number" class="form-label">{{ __('Bank Account Number') }}</label>
                  <input type="text" class="form-control" id="bank_account_number" name="bank_account_number"
                         value="{{ $currentSettings['bank_account_number'] ?? '' }}" maxlength="50">
                  <div class="form-text">{{ $groupedSettings['banking']['bank_account_number']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="payment_method" class="form-label">{{ __('Default Payment Method') }}</label>
                  @php
                    $paymentMethodOptions = $groupedSettings['banking']['payment_method']['options'] ?? [];
                    if(empty($paymentMethodOptions)) {
                        $paymentMethodOptions = [
                          'bank_transfer' => 'Bank Transfer',
                          'check' => 'Check / Cheque',
                          'cash' => 'Cash',
                          'mobile_money' => 'Mobile Money',
                          'direct_deposit' => 'Direct Deposit',
                          'payroll_card' => 'Payroll Card',
                        ];
                    }
                  @endphp
                  <select class="form-select" id="payment_method" name="payment_method">
                    @foreach($paymentMethodOptions as $key => $label)
                      <option value="{{ $key }}" {{ ($currentSettings['payment_method'] ?? 'bank_transfer') == $key ? 'selected' : '' }}>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">{{ $groupedSettings['banking']['payment_method']['description'] ?? 'Primary method for salary payments to employees.' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Compliance Tab -->
      <div class="tab-pane fade" id="compliance" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Compliance & Legal Requirements') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="minimum_wage" class="form-label">{{ __('Minimum Monthly Wage') }}</label>
                  <div class="input-group">
                    <span class="input-group-text">{{ $currentSettings['currency_symbol'] ?? '$' }}</span>
                    <input type="number" class="form-control" id="minimum_wage" name="minimum_wage"
                           value="{{ $currentSettings['minimum_wage'] ?? '' }}" min="0" step="0.01">
                  </div>
                  <div class="form-text">{{ $groupedSettings['compliance']['minimum_wage']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="working_hours_per_day" class="form-label">{{ __('Working Hours Per Day') }}</label>
                  <input type="number" class="form-control" id="working_hours_per_day" name="working_hours_per_day"
                         value="{{ $currentSettings['working_hours_per_day'] ?? '' }}" min="1" max="24">
                  <div class="form-text">{{ $groupedSettings['compliance']['working_hours_per_day']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="working_days_per_week" class="form-label">{{ __('Working Days Per Week') }}</label>
                  <input type="number" class="form-control" id="working_days_per_week" name="working_days_per_week"
                         value="{{ $currentSettings['working_days_per_week'] ?? '' }}" min="1" max="7">
                  <div class="form-text">{{ $groupedSettings['compliance']['working_days_per_week']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Integration Tab -->
      <div class="tab-pane fade" id="integration" role="tabpanel">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">{{ __('Integration & Automation Settings') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3 form-check">
                  <input type="hidden" name="auto_process_payroll" value="0">
                  <input class="form-check-input" type="checkbox" id="auto_process_payroll" name="auto_process_payroll" value="1"
                         {{ ($currentSettings['auto_process_payroll'] ?? false) ? 'checked' : '' }}>
                  <label class="form-check-label" for="auto_process_payroll">
                    {{ $groupedSettings['integration']['auto_process_payroll']['label'] ?? 'Auto Process Payroll' }}
                  </label>
                  <div class="form-text">{{ $groupedSettings['integration']['auto_process_payroll']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3 form-check">
                  <input type="hidden" name="email_pay_slips" value="0">
                  <input class="form-check-input" type="checkbox" id="email_pay_slips" name="email_pay_slips" value="1"
                         {{ ($currentSettings['email_pay_slips'] ?? false) ? 'checked' : '' }}>
                  <label class="form-check-label" for="email_pay_slips">
                    {{ $groupedSettings['integration']['email_pay_slips']['label'] ?? 'Email Pay Slips' }}
                  </label>
                  <div class="form-text">{{ $groupedSettings['integration']['email_pay_slips']['description'] ?? '' }}</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3 form-check">
                  <input type="hidden" name="export_to_accounting" value="0">
                  <input class="form-check-input" type="checkbox" id="export_to_accounting" name="export_to_accounting" value="1"
                         {{ ($currentSettings['export_to_accounting'] ?? false) ? 'checked' : '' }}>
                  <label class="form-check-label" for="export_to_accounting">
                    {{ $groupedSettings['integration']['export_to_accounting']['label'] ?? 'Export to Accounting' }}
                  </label>
                  <div class="form-text">{{ $groupedSettings['integration']['export_to_accounting']['description'] ?? '' }}</div>
                </div>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="export_format" class="form-label">{{ __('Export Format') }}</label>
                  <select class="form-select" id="export_format" name="export_format">
                    @foreach($groupedSettings['integration']['export_format']['options'] ?? ['csv' => 'CSV', 'json' => 'JSON'] as $key => $label)
                      <option value="{{ $key }}" {{ ($currentSettings['export_format'] ?? 'csv') == $key ? 'selected' : '' }}>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">{{ $groupedSettings['integration']['export_format']['description'] ?? 'File format for accounting exports' }}</div>
                </div>
              </div>
            </div>

            <div class="alert alert-info mt-3">
              <i class="bi bi-info-circle"></i>
              <strong>Important:</strong> These automation features require:
              <ul class="mb-0 mt-2">
                <li>Queue workers to be running (<code>php artisan queue:work</code>)</li>
                <li>Valid email configuration for pay slip delivery</li>
                <li>Storage permissions for accounting exports (stored in <code>storage/app/exports/payroll/</code>)</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Form Actions -->
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="{{ route('tenant.modules.human_resources.payroll-settings.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> {{ __('Save Settings') }}
      </button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate total percentage for salary components
    function calculateTotalPercentage() {
        const percentages = [
            parseFloat(document.getElementById('basic_salary_percentage').value) || 0,
            parseFloat(document.getElementById('house_allowance_percentage').value) || 0,
            parseFloat(document.getElementById('transport_allowance_percentage').value) || 0,
            parseFloat(document.getElementById('medical_allowance_percentage').value) || 0
        ];

        const total = percentages.reduce((sum, percentage) => sum + percentage, 0);
        const totalElement = document.getElementById('total-percentage');

        totalElement.textContent = total.toFixed(2) + '%';

        if (total !== 100) {
            totalElement.className = 'badge bg-warning';
        } else {
            totalElement.className = 'badge bg-success';
        }
    }

    // Add event listeners to percentage inputs
    const percentageInputs = [
        'basic_salary_percentage',
        'house_allowance_percentage',
        'transport_allowance_percentage',
        'medical_allowance_percentage'
    ];

    percentageInputs.forEach(id => {
        document.getElementById(id).addEventListener('input', calculateTotalPercentage);
    });

    // Initial calculation
    calculateTotalPercentage();

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const fiscalYearStart = document.getElementById('fiscal_year_start').value;
        if (fiscalYearStart && !/^\d{2}-\d{2}$/.test(fiscalYearStart)) {
            e.preventDefault();
            alert('{{ __("Fiscal year start must be in MM-DD format") }}');
            return false;
        }
    });
});
</script>
@endsection