@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h4 fw-semibold mb-0">{{ __('Employee Details') }}</h1>
      <div class="small text-secondary">{{ $employee->first_name }} {{ $employee->last_name }}</div>
    </div>
    <div>
      <a href="{{ route('tenant.modules.human_resources.employees.edit', $employee) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i> {{ __('Edit') }}
      </a>
      <a href="{{ route('tenant.modules.human_resources.employees.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Employee Information') }}</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('Employee Number') }}:</strong><br>
              {{ $employee->employee_number ?? 'Not Assigned' }}
            </div>
            <div class="col-md-6">
              <strong>{{ __('Employee Type') }}:</strong><br>
              {{ ucfirst(str_replace('_', ' ', $employee->employee_type)) }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('National ID') }}:</strong><br>
              {{ $employee->national_id ?? 'Not provided' }}
            </div>
            <div class="col-md-6">
              <strong>{{ __('Gender') }}:</strong><br>
              {{ $employee->gender ? ucfirst($employee->gender) : 'Not specified' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('First Name') }}:</strong><br>
              {{ $employee->first_name }}
            </div>
            <div class="col-md-6">
              <strong>{{ __('Last Name') }}:</strong><br>
              {{ $employee->last_name }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('Email') }}:</strong><br>
              {{ $employee->email ?? 'Not provided' }}
            </div>
            <div class="col-md-6">
              <strong>{{ __('Phone') }}:</strong><br>
              {{ $employee->phone ?? 'Not provided' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('Department') }}:</strong><br>
              {{ $employee->department?->name ?? 'Not Assigned' }}
            </div>
            <div class="col-md-6">
              <strong>{{ __('Position') }}:</strong><br>
              {{ $employee->position?->title ?? 'Not Assigned' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('Employment Status') }}:</strong><br>
              <span class="badge bg-{{ $employee->employment_status === 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst($employee->employment_status) }}
              </span>
            </div>
            <div class="col-md-6">
              <strong>{{ __('Hire Date') }}:</strong><br>
              {{ $employee->hire_date?->format('M d, Y') ?? 'Not set' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ __('Birth Date') }}:</strong><br>
              {{ $employee->birth_date?->format('M d, Y') ?? 'Not set' }}
            </div>
            <div class="col-md-6">
              <strong>{{ __('Salary Scale') }}:</strong><br>
              {{ $employee->salaryScale?->name ?? 'Not Assigned' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Year-to-Date Summary Card -->
      <div class="card mt-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="bi bi-graph-up"></i> {{ __('Year-to-Date Summary') }} ({{ now()->year }})</h5>
        </div>
        <div class="card-body">
          <div class="row text-center g-3">
            <div class="col-md-4">
              <div class="border rounded p-3">
                <small class="text-muted d-block">{{ __('Total Gross') }}</small>
                <h5 class="mb-0 text-success">{{ format_money($ytdData['total_gross']) }}</h5>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3">
                <small class="text-muted d-block">{{ __('Total Net') }}</small>
                <h5 class="mb-0 text-primary">{{ format_money($ytdData['total_net']) }}</h5>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3">
                <small class="text-muted d-block">{{ __('Deductions') }}</small>
                <h5 class="mb-0 text-danger">{{ format_money($ytdData['total_deductions']) }}</h5>
              </div>
            </div>
          </div>
          <hr>
          <div class="row g-2">
            <div class="col-6">
              <small class="text-muted">{{ __('Total Tax (PAYE)') }}:</small>
              <span class="float-end fw-bold">{{ format_money($ytdData['total_tax']) }}</span>
            </div>
            <div class="col-6">
              <small class="text-muted">{{ __('Total NSSF') }}:</small>
              <span class="float-end fw-bold">{{ format_money($ytdData['total_nssf']) }}</span>
            </div>
            <div class="col-6">
              <small class="text-muted">{{ __('Total Bonuses') }}:</small>
              <span class="float-end fw-bold text-success">{{ format_money($ytdData['total_bonuses']) }}</span>
            </div>
            <div class="col-6">
              <small class="text-muted">{{ __('Months Paid') }}:</small>
              <span class="float-end fw-bold">{{ $ytdData['months_paid'] }}/{{ now()->month }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment History Card -->
      <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bi bi-clock-history"></i> {{ __('Payment History') }}</h5>
          <span class="badge bg-secondary">{{ $recentPayrolls->count() }} {{ __('records') }}</span>
        </div>
        <div class="card-body p-0">
          @if($recentPayrolls->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>{{ __('Period') }}</th>
                  <th class="text-end">{{ __('Gross') }}</th>
                  <th class="text-end">{{ __('Deductions') }}</th>
                  <th class="text-end">{{ __('Net') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th class="text-center">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentPayrolls as $payroll)
                <tr>
                  <td>
                    <strong>{{ $payroll->period_label }}</strong><br>
                    <small class="text-muted">{{ $payroll->payment_date->format('M d, Y') }}</small>
                  </td>
                  <td class="text-end">{{ format_money($payroll->gross_salary) }}</td>
                  <td class="text-end text-danger">{{ format_money($payroll->total_deductions) }}</td>
                  <td class="text-end"><strong>{{ format_money($payroll->net_salary) }}</strong></td>
                  <td><span class="badge {{ $payroll->status_badge_class }}">{{ ucfirst($payroll->status) }}</span></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-info" 
                            data-bs-toggle="modal" 
                            data-bs-target="#payrollDetailModal{{ $payroll->id }}"
                            title="{{ __('View Details') }}">
                      <i class="bi bi-eye"></i>
                    </button>
                    @if($payroll->status == 'paid')
                      <a href="{{ route('tenant.modules.human_resources.payroll.payslip', $payroll) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Download Payslip') }}">
                        <i class="bi bi-download"></i>
                      </a>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @else
          <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">{{ __('No payment records found') }}</p>
          </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <!-- Salary Summary Card -->
      <div class="card mt-3">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="bi bi-cash-stack"></i> {{ __('Salary Summary') }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <small class="text-muted d-block">{{ __('Basic Salary') }}</small>
            <h4 class="mb-0">{{ format_money($salarySummary['basic_salary']) }}</h4>
          </div>
          <hr>
          <div class="mb-2">
            <small class="text-muted">{{ __('Average Gross (YTD)') }}:</small>
            <strong class="float-end">{{ format_money($salarySummary['average_gross']) }}</strong>
          </div>
          <div class="mb-2">
            <small class="text-muted">{{ __('Average Net (YTD)') }}:</small>
            <strong class="float-end">{{ format_money($salarySummary['average_net']) }}</strong>
          </div>
          @if($salarySummary['last_payment_date'])
          <div class="mb-2">
            <small class="text-muted">{{ __('Last Payment') }}:</small>
            <strong class="float-end">{{ $salarySummary['last_payment_date']->format('M d, Y') }}</strong>
          </div>
          @endif
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('tenant.modules.human_resources.employee-ids.index') }}?employee_id={{ $employee->id }}" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-id-card"></i> {{ __('Generate ID Card') }}
            </a>
            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#payrollModal">
              <i class="fas fa-money-bill-wave"></i> {{ __('Record Salary Payment') }}
            </button>
            <a href="{{ route('tenant.modules.human_resources.leave_requests.index', ['employee' => $employee->first_name]) }}" class="btn btn-outline-info btn-sm">
              <i class="fas fa-calendar-times"></i> {{ __('Leave Requests') }}
            </a>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Passport Photo') }}</h5>
        </div>
        <div class="card-body text-center">
          @if($employee->photo_path)
            <img src="{{ asset('storage/'.$employee->photo_path) }}" alt="Photo" class="rounded-circle border" style="width:120px;height:120px;object-fit:cover;">
          @else
            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width:120px;height:120px;">
              <span class="text-white fw-semibold" style="font-size:2rem;">
                {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
              </span>
            </div>
            <div class="small text-muted mt-2">{{ __('No photo uploaded') }}</div>
          @endif
        </div>
      </div>

      @if($employee->metadata)
      <div class="card mt-3">
        <div class="card-header">
          <h5 class="mb-0">{{ __('Additional Information') }}</h5>
        </div>
        <div class="card-body">
          <pre class="small">{{ json_encode($employee->metadata, JSON_PRETTY_PRINT) }}</pre>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Payroll Detail Modals -->
@foreach($recentPayrolls as $payroll)
<div class="modal fade" id="payrollDetailModal{{ $payroll->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title">
          <i class="bi bi-receipt"></i> {{ __('Payroll Details') }} - {{ $payroll->period_label }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>{{ __('Payroll Number') }}:</strong> {{ $payroll->payroll_number }}</p>
            <p><strong>{{ __('Payment Date') }}:</strong> {{ $payroll->payment_date->format('F d, Y') }}</p>
            <p><strong>{{ __('Status') }}:</strong> <span class="badge {{ $payroll->status_badge_class }}">{{ ucfirst($payroll->status) }}</span></p>
          </div>
          <div class="col-md-6">
            <p><strong>{{ __('Payment Method') }}:</strong> {{ $payroll->payment_method_label }}</p>
            @if($payroll->payment_reference)
            <p><strong>{{ __('Reference') }}:</strong> {{ $payroll->payment_reference }}</p>
            @endif
            @if($payroll->working_days)
            <p><strong>{{ __('Days Worked') }}:</strong> {{ $payroll->days_worked ?? 0 }}/{{ $payroll->working_days }}</p>
            @endif
          </div>
        </div>

        <h6 class="border-bottom pb-2">{{ __('Earnings') }}</h6>
        <table class="table table-sm">
          <tr>
            <td>{{ __('Basic Salary') }}</td>
            <td class="text-end">{{ format_money($payroll->basic_salary) }}</td>
          </tr>
          <tr>
            <td>{{ __('Allowances') }}</td>
            <td class="text-end">{{ format_money($payroll->allowances) }}</td>
          </tr>
          @if($payroll->bonuses > 0)
          <tr>
            <td>{{ __('Bonuses') }}</td>
            <td class="text-end text-success">{{ format_money($payroll->bonuses) }}</td>
          </tr>
          @endif
          @if($payroll->overtime_pay > 0)
          <tr>
            <td>{{ __('Overtime Pay') }} ({{ $payroll->overtime_hours }}h)</td>
            <td class="text-end text-success">{{ format_money($payroll->overtime_pay) }}</td>
          </tr>
          @endif
          <tr class="table-light fw-bold">
            <td>{{ __('Gross Salary') }}</td>
            <td class="text-end">{{ format_money($payroll->gross_salary) }}</td>
          </tr>
        </table>

        <h6 class="border-bottom pb-2 mt-3">{{ __('Deductions') }}</h6>
        <table class="table table-sm">
          <tr>
            <td>{{ __('Tax (PAYE)') }}</td>
            <td class="text-end text-danger">{{ format_money($payroll->tax_deduction) }}</td>
          </tr>
          <tr>
            <td>{{ __('NSSF') }}</td>
            <td class="text-end text-danger">{{ format_money($payroll->nssf_deduction) }}</td>
          </tr>
          <tr>
            <td>{{ __('Health Insurance') }}</td>
            <td class="text-end text-danger">{{ format_money($payroll->health_insurance) }}</td>
          </tr>
          @if($payroll->loan_deduction > 0)
          <tr>
            <td>{{ __('Loan Deduction') }}</td>
            <td class="text-end text-danger">{{ format_money($payroll->loan_deduction) }}</td>
          </tr>
          @endif
          @if($payroll->other_deductions > 0)
          <tr>
            <td>{{ __('Other Deductions') }}</td>
            <td class="text-end text-danger">{{ format_money($payroll->other_deductions) }}</td>
          </tr>
          @endif
          <tr class="table-light fw-bold">
            <td>{{ __('Total Deductions') }}</td>
            <td class="text-end text-danger">{{ format_money($payroll->total_deductions) }}</td>
          </tr>
        </table>

        <div class="alert alert-success mt-3">
          <h5 class="mb-0">
            {{ __('Net Salary') }}: <strong class="float-end">{{ format_money($payroll->net_salary) }}</strong>
          </h5>
        </div>

        @if($payroll->notes)
        <div class="alert alert-info mt-2">
          <strong>{{ __('Notes') }}:</strong> {{ $payroll->notes }}
        </div>
        @endif

        @if($payroll->paid_at)
        <p class="text-muted small mt-2">
          <i class="bi bi-check-circle text-success"></i> {{ __('Paid on') }} {{ $payroll->paid_at->format('F d, Y \a\t h:i A') }}
          @if($payroll->paidBy)
          {{ __('by') }} {{ $payroll->paidBy->name }}
          @endif
        </p>
        @endif
      </div>
      <div class="modal-footer">
        @if($payroll->status == 'paid')
        <a href="{{ route('tenant.modules.human_resources.payroll.payslip', $payroll) }}" class="btn btn-primary">
          <i class="bi bi-download"></i> {{ __('Download Payslip') }}
        </a>
        @endif
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>
@endforeach

<!-- Record Payment Modal -->
<div class="modal fade" id="payrollModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="bi bi-cash-coin"></i> {{ __('Record Salary Payment') }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="#" method="POST">
        @csrf
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> {{ __('Recording salary payment for') }}: <strong>{{ $employee->full_name }}</strong>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Period Month') }} <span class="text-danger">*</span></label>
              <select class="form-select" name="period_month" required>
                <option value="">{{ __('Select Month') }}</option>
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == now()->month ? 'selected' : '' }}>
                  {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                </option>
                @endfor
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Period Year') }} <span class="text-danger">*</span></label>
              <select class="form-select" name="period_year" required>
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">{{ __('Basic Salary') }} <span class="text-danger">*</span></label>
              <input type="number" class="form-control" name="basic_salary" 
                     value="{{ $salarySummary['basic_salary'] }}" 
                     step="0.01" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Allowances') }}</label>
              <input type="number" class="form-control" name="allowances" value="0" step="0.01">
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">{{ __('Bonuses') }}</label>
              <input type="number" class="form-control" name="bonuses" value="0" step="0.01">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Overtime Pay') }}</label>
              <input type="number" class="form-control" name="overtime_pay" value="0" step="0.01">
            </div>
          </div>

          <hr class="my-3">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Tax Deduction (PAYE)') }}</label>
              <input type="number" class="form-control" name="tax_deduction" value="0" step="0.01">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('NSSF Deduction') }}</label>
              <input type="number" class="form-control" name="nssf_deduction" value="0" step="0.01">
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">{{ __('Health Insurance') }}</label>
              <input type="number" class="form-control" name="health_insurance" value="0" step="0.01">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Loan Deduction') }}</label>
              <input type="number" class="form-control" name="loan_deduction" value="0" step="0.01">
            </div>
          </div>

          <hr class="my-3">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
              <input type="date" class="form-control" name="payment_date" 
                     value="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
              <select class="form-select" name="payment_method" required>
                <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                <option value="mobile_money">{{ __('Mobile Money') }}</option>
                <option value="cash">{{ __('Cash') }}</option>
                <option value="cheque">{{ __('Cheque') }}</option>
              </select>
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-12">
              <label class="form-label">{{ __('Payment Reference') }}</label>
              <input type="text" class="form-control" name="payment_reference" 
                     placeholder="{{ __('Transaction ID, Cheque Number, etc.') }}">
            </div>
          </div>

          <div class="mt-3">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea class="form-control" name="notes" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> {{ __('Record Payment') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection