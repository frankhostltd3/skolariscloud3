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
                <a href="{{ route('tenant.modules.human-resource.employees.edit', $employee) }}"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                @if (
                    $employee->user &&
                        in_array($employee->user->user_type?->value ?? $employee->user->user_type, [
                            'teaching_staff',
                            'general_staff',
                            'admin',
                        ]))
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#changeTypeModal">
                        <i class="bi bi-arrow-left-right"></i> {{ __('Change Type') }}
                    </button>
                @endif
                <a href="{{ route('tenant.modules.human-resource.employees.index') }}" class="btn btn-secondary btn-sm">
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
                                <strong>{{ __('Employee Number') }}:</strong>
                                <div class="editable-field">
                                    {{ $employee->employee_number ?? 'Not Assigned' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Employee Type') }}:</strong>
                                <x-hr.inline-select :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="employee_type" :value="$employee->employee_type"
                                    :options="[
                                        'teacher' => __('Teacher'),
                                        'general_staff' => __('General Staff'),
                                        'admin' => __('Admin'),
                                        'full_time' => __('Full Time'),
                                        'part_time' => __('Part Time'),
                                        'contract' => __('Contract'),
                                        'intern' => __('Intern'),
                                    ]" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('National ID') }}:</strong>
                                <x-hr.inline-input type="text" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="national_id" :value="$employee->national_id"
                                    placeholder="{{ __('Not provided') }}" />
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Gender') }}:</strong>
                                <x-hr.inline-select :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="gender" :value="$employee->gender" :options="['male' => __('Male'), 'female' => __('Female'), 'other' => __('Other')]"
                                    placeholder="{{ __('Select Gender') }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('First Name') }}:</strong>
                                <x-hr.inline-input type="text" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="first_name" :value="$employee->first_name" />
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Last Name') }}:</strong>
                                <x-hr.inline-input type="text" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="last_name" :value="$employee->last_name" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Email') }}:</strong>
                                <x-hr.inline-input type="email" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="email" :value="$employee->email"
                                    placeholder="{{ __('Not provided') }}" />
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Phone') }}:</strong>
                                <x-hr.inline-input type="text" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="phone" :value="$employee->phone"
                                    placeholder="{{ __('Not provided') }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Department') }}:</strong>
                                <x-hr.inline-select :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="department_id" :value="$employee->department_id"
                                    :options="$departments->pluck('name', 'id')->toArray()" placeholder="{{ __('Not Assigned') }}" />
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Position') }}:</strong>
                                <x-hr.inline-select :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="position_id" :value="$employee->position_id"
                                    :options="$positions->pluck('title', 'id')->toArray()" placeholder="{{ __('Not Assigned') }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Employment Status') }}:</strong>
                                <x-hr.inline-select :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="employment_status" :value="$employee->employment_status"
                                    :options="[
                                        'active' => __('Active'),
                                        'probation' => __('Probation'),
                                        'on_leave' => __('On Leave'),
                                        'inactive' => __('Inactive'),
                                        'suspended' => __('Suspended'),
                                        'terminated' => __('Terminated'),
                                    ]" />
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Hire Date') }}:</strong>
                                <x-hr.inline-input type="date" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="hire_date" :value="$employee->hire_date?->format('Y-m-d')"
                                    placeholder="{{ __('Not set') }}" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Birth Date') }}:</strong>
                                <x-hr.inline-input type="date" :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="birth_date" :value="$employee->birth_date?->format('Y-m-d')"
                                    placeholder="{{ __('Not set') }}" />
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Salary Scale') }}:</strong>
                                <x-hr.inline-select :action="route('tenant.modules.human-resource.employees.update-detail', $employee)" field="salary_scale_id" :value="$employee->salary_scale_id"
                                    :options="$salaryScales->pluck('name', 'id')->toArray()" placeholder="{{ __('Not Assigned') }}" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Year-to-Date Summary Card -->
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> {{ __('Year-to-Date Summary') }}
                            ({{ now()->year }})</h5>
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
                                <span
                                    class="float-end fw-bold text-success">{{ format_money($ytdData['total_bonuses']) }}</span>
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
                        @if ($recentPayrolls->count() > 0)
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
                                        @foreach ($recentPayrolls as $payroll)
                                            <tr>
                                                <td>
                                                    <strong>{{ $payroll->period_label }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $payroll->payment_date->format('M d, Y') }}</small>
                                                </td>
                                                <td class="text-end">{{ format_money($payroll->gross_salary) }}</td>
                                                <td class="text-end text-danger">
                                                    {{ format_money($payroll->total_deductions) }}</td>
                                                <td class="text-end">
                                                    <strong>{{ format_money($payroll->net_salary) }}</strong>
                                                </td>
                                                <td><span
                                                        class="badge {{ $payroll->status_badge_class }}">{{ ucfirst($payroll->status) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#payrollDetailModal{{ $payroll->id }}"
                                                        title="{{ __('View Details') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    @if ($payroll->status == 'paid')
                                                        <a href="{{ route('tenant.modules.human-resource.payroll-payslip.show', $payroll) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="{{ __('Download Payslip') }}">
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

                @if ($teacherContext)
                    <div class="card mt-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="bi bi-mortarboard"></i> {{ __('Teacher Assignments') }}</h5>
                            <a href="{{ route('tenant.academics.teacher-allocations.workload', ['teacher_id' => $teacherContext['teacher']->user_id ?? $teacherContext['teacher']->id]) }}"
                                class="btn btn-outline-primary btn-sm">
                                {{ __('View Workload') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-circle bg-primary text-white">
                                        <i class="bi bi-person-badge" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $teacherContext['teacher']->name }}</div>
                                        <div class="text-muted small">{{ $teacherContext['teacher']->email }}</div>
                                        <div class="badge bg-success">{{ __('Active Teacher') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-secondary small">{{ __('Classes & Streams') }}</h6>
                                    @forelse ($teacherContext['classAssignments'] as $class)
                                        <div class="border rounded p-2 mb-2">
                                            <div class="fw-semibold">{{ $class['name'] }}</div>
                                            <div class="text-muted small">{{ $class['education_level'] }}</div>
                                            <div class="d-flex justify-content-between small">
                                                <span
                                                    class="badge {{ $class['is_class_teacher'] ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $class['is_class_teacher'] ? __('Class Teacher') : __('Subject Teacher') }}
                                                </span>
                                                @if ($class['academic_year'])
                                                    <span class="text-muted">{{ $class['academic_year'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted small">{{ __('No class assignments found.') }}</p>
                                    @endforelse

                                    @if ($teacherContext['streamAssignments']->isNotEmpty())
                                        <h6 class="text-uppercase text-secondary mt-4 small">{{ __('Streams') }}</h6>
                                        @foreach ($teacherContext['streamAssignments'] as $stream)
                                            <div class="border rounded p-2 mb-2">
                                                <div class="fw-semibold">{{ $stream['class_name'] }} -
                                                    {{ $stream['name'] }}</div>
                                                @if ($stream['academic_year'])
                                                    <div class="text-muted small">{{ $stream['academic_year'] }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-uppercase text-secondary small">{{ __('Subjects by Class') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Subject') }}</th>
                                                    <th>{{ __('Class') }}</th>
                                                    <th>{{ __('Year') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($teacherContext['subjectAssignments'] as $assignment)
                                                    <tr>
                                                        <td>
                                                            <span
                                                                class="fw-semibold">{{ $assignment->subject_name }}</span>
                                                            <div class="text-muted small">{{ $assignment->subject_code }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $assignment->class_name ?? __('All classes') }}</td>
                                                        <td>{{ $assignment->academic_year ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-muted small">
                                                            {{ __('No subject assignments found.') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
                        @if ($salarySummary['last_payment_date'])
                            <div class="mb-2">
                                <small class="text-muted">{{ __('Last Payment') }}:</small>
                                <strong
                                    class="float-end">{{ $salarySummary['last_payment_date']->format('M d, Y') }}</strong>
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
                            <a href="{{ route('tenant.modules.human-resource.employee-ids.index', ['employee_id' => $employee->id]) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-id-card"></i> {{ __('Generate ID Card') }}
                            </a>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#payrollModal">
                                <i class="fas fa-money-bill-wave"></i> {{ __('Record Salary Payment') }}
                            </button>
                            <a href="{{ route('tenant.modules.human-resource.leave-requests.index', ['employee' => $employee->first_name]) }}"
                                class="btn btn-outline-info btn-sm">
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
                        @if ($employee->photo_path)
                            <img src="{{ asset('storage/' . $employee->photo_path) }}" alt="Photo"
                                class="rounded-circle border" style="width:120px;height:120px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto"
                                style="width:120px;height:120px;">
                                <span class="text-white fw-semibold" style="font-size:2rem;">
                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="small text-muted mt-2">{{ __('No photo uploaded') }}</div>
                        @endif

                        <form class="mt-3" method="POST"
                            action="{{ route('tenant.modules.human-resource.employees.update-photo', $employee) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="input-group input-group-sm">
                                <input type="file" name="passport_photo" accept="image/*" class="form-control"
                                    required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> {{ __('Upload') }}
                                </button>
                            </div>
                            <small class="d-block text-muted mt-1">{{ __('JPEG/PNG/WebP up to 2MB') }}</small>
                            @error('passport_photo')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                </div>

                @if ($employee->metadata)
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
    @foreach ($recentPayrolls as $payroll)
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
                                <p><strong>{{ __('Payment Date') }}:</strong>
                                    {{ $payroll->payment_date->format('F d, Y') }}</p>
                                <p><strong>{{ __('Status') }}:</strong> <span
                                        class="badge {{ $payroll->status_badge_class }}">{{ ucfirst($payroll->status) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('Payment Method') }}:</strong> {{ $payroll->payment_method_label }}</p>
                                @if ($payroll->payment_reference)
                                    <p><strong>{{ __('Reference') }}:</strong> {{ $payroll->payment_reference }}</p>
                                @endif
                                @if ($payroll->working_days)
                                    <p><strong>{{ __('Days Worked') }}:</strong>
                                        {{ $payroll->days_worked ?? 0 }}/{{ $payroll->working_days }}</p>
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
                            @if ($payroll->bonuses > 0)
                                <tr>
                                    <td>{{ __('Bonuses') }}</td>
                                    <td class="text-end text-success">{{ format_money($payroll->bonuses) }}</td>
                                </tr>
                            @endif
                            @if ($payroll->overtime_pay > 0)
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
                            @if ($payroll->loan_deduction > 0)
                                <tr>
                                    <td>{{ __('Loan Deduction') }}</td>
                                    <td class="text-end text-danger">{{ format_money($payroll->loan_deduction) }}</td>
                                </tr>
                            @endif
                            @if ($payroll->other_deductions > 0)
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
                                {{ __('Net Salary') }}: <strong
                                    class="float-end">{{ format_money($payroll->net_salary) }}</strong>
                            </h5>
                        </div>

                        @if ($payroll->notes)
                            <div class="alert alert-info mt-2">
                                <strong>{{ __('Notes') }}:</strong> {{ $payroll->notes }}
                            </div>
                        @endif

                        @if ($payroll->paid_at)
                            <p class="text-muted small mt-2">
                                <i class="bi bi-check-circle text-success"></i> {{ __('Paid on') }}
                                {{ $payroll->paid_at->format('F d, Y \a\t h:i A') }}
                                @if ($payroll->paidBy)
                                    {{ __('by') }} {{ $payroll->paidBy->name }}
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($payroll->status == 'paid')
                            <a href="{{ route('tenant.modules.human-resource.payroll-payslip.show', $payroll) }}"
                                class="btn btn-primary">
                                <i class="bi bi-download"></i> {{ __('Download Payslip') }}
                            </a>
                        @endif
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
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
                            <i class="bi bi-info-circle"></i> {{ __('Recording salary payment for') }}:
                            <strong>{{ $employee->full_name }}</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Period Month') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="period_month" required>
                                    <option value="">{{ __('Select Month') }}</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                            {{ $m == now()->month ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Period Year') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="period_year" required>
                                    @for ($y = now()->year; $y >= now()->year - 2; $y--)
                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Basic Salary') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="basic_salary"
                                    value="{{ $salarySummary['basic_salary'] }}" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Allowances') }}</label>
                                <input type="number" class="form-control" name="allowances" value="0"
                                    step="0.01">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Bonuses') }}</label>
                                <input type="number" class="form-control" name="bonuses" value="0"
                                    step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Overtime Pay') }}</label>
                                <input type="number" class="form-control" name="overtime_pay" value="0"
                                    step="0.01">
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Tax Deduction (PAYE)') }}</label>
                                <input type="number" class="form-control" name="tax_deduction" value="0"
                                    step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('NSSF Deduction') }}</label>
                                <input type="number" class="form-control" name="nssf_deduction" value="0"
                                    step="0.01">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Health Insurance') }}</label>
                                <input type="number" class="form-control" name="health_insurance" value="0"
                                    step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Loan Deduction') }}</label>
                                <input type="number" class="form-control" name="loan_deduction" value="0"
                                    step="0.01">
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Payment Date') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payment_date"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Payment Method') }} <span
                                        class="text-danger">*</span></label>
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
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> {{ __('Record Payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Staff Type Modal -->
    @if (
        $employee->user &&
            in_array($employee->user->user_type?->value ?? $employee->user->user_type, [
                'teaching_staff',
                'general_staff',
                'admin',
            ]))
        <div class="modal fade" id="changeTypeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.staff.change-type', $employee->user) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Change Staff Type') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                {{ __('Current Type:') }}
                                <strong>{{ $employee->user->user_type instanceof \App\Enums\UserType ? $employee->user->user_type->label() : ucwords(str_replace('_', ' ', $employee->user->user_type)) }}</strong>
                            </div>
                            <div class="mb-3">
                                <label for="new_user_type" class="form-label">{{ __('New Staff Type') }}</label>
                                <select name="new_user_type" id="new_user_type" class="form-select" required>
                                    <option value="">{{ __('-- Select New Type --') }}</option>
                                    <option value="teaching_staff">
                                        {{ __('Teaching Staff (Teacher)') }}
                                    </option>
                                    <option value="general_staff">
                                        {{ __('General Staff (Non-Teaching)') }}
                                    </option>
                                    <option value="admin">
                                        {{ __('Administrator') }}
                                    </option>
                                </select>
                                <small class="form-text text-muted">
                                    {{ __('Switching to Teaching Staff will create/activate a Teacher record. Switching away will deactivate it.') }}
                                </small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sync_role" id="sync_role"
                                    value="1" checked>
                                <label class="form-check-label" for="sync_role">
                                    {{ __('Also update Spatie role (Teacher/Staff/Admin)') }}
                                </label>
                            </div>
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle"></i>
                                {{ __('This will update user type, role, and related Teacher/Employee records.') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-left-right"></i> {{ __('Change Type') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
