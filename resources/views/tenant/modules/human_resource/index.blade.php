@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@push('styles')
    <style>
        .hr-hero {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
        }

        .hr-hero .badge {
            background: rgba(255, 255, 255, 0.15);
        }

        .hr-stat-card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .pulse-indicator {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            position: relative;
            background: #198754;
        }

        .pulse-indicator::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 999px;
            animation: pulse 2s infinite;
            border: 2px solid rgba(25, 135, 84, 0.5);
        }

        @keyframes pulse {
            from {
                transform: scale(1);
                opacity: 1;
            }

            to {
                transform: scale(2.25);
                opacity: 0;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $headcountTotal = max($headcount['total'] ?? 0, 1);
        $activePercent = ($headcount['total'] ?? 0) > 0 ? round(($headcount['active'] / $headcount['total']) * 100) : 0;
        $onLeavePercent =
            ($headcount['total'] ?? 0) > 0 ? round(($headcount['on_leave'] / $headcount['total']) * 100) : 0;
        $pendingLeaves = $leaveSummary['pending'] ?? 0;
        $pendingRuns = $payrollSummary['pending_runs'] ?? 0;
        $lastPayroll = $payrollSummary['last_run'] ?? null;
        $employeeIdStudioUrl = Route::has('tenant.modules.human-resource.employee-ids.index')
            ? route('tenant.modules.human-resource.employee-ids.index')
            : null;
    @endphp

    <div class="container-fluid py-2">
        <div class="card hr-hero text-white border-0 shadow-lg mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <span class="badge rounded-pill text-white fw-semibold mb-3">
                            <i class="bi bi-diagram-3 me-1"></i> Human Resource Command Center
                        </span>
                        <h1 class="display-6 fw-semibold mb-3">People operations, orchestrated
                            beautifully.</h1>
                        <p class="fs-5 mb-4 opacity-75">Oversee hiring pipelines, payroll cadences, and leave
                            workflows without leaving this cockpit.
                            Every insight is tenant-aware and production ready.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('tenant.modules.human-resource.employees.create') }}"
                                class="btn btn-light text-primary fw-semibold">
                                <i class="bi bi-person-plus me-2"></i>Register Employee
                            </a>
                            <a href="{{ route('tenant.modules.human-resource.leave-requests.create') }}"
                                class="btn btn-outline-light">
                                <i class="bi bi-calendar-check me-2"></i>Log Leave Request
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="bg-white bg-opacity-10 rounded-4 p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <p class="text-uppercase small opacity-75 mb-1">Workforce</p>
                                    <h3 class="fw-semibold mb-0">{{ number_format($headcount['total']) }}</h3>
                                </div>
                                <span class="badge bg-success text-white">{{ $structureTotals['departments'] }}
                                    departments</span>
                            </div>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between">
                                    <span>Teachers</span>
                                    <strong>{{ number_format($headcount['teachers']) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>On leave</span>
                                    <strong>{{ number_format($headcount['on_leave']) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Recent hires (30d)</span>
                                    <strong>{{ number_format($headcount['recent_hires']) }}</strong>
                                </div>
                            </div>
                            <hr class="border-white border-opacity-25 my-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-indicator"></span>
                                <div>
                                    <small class="text-uppercase opacity-75">Live status</small>
                                    <div class="fw-semibold">{{ $pendingRuns }} payroll runs queued ·
                                        {{ $pendingLeaves }} leave awaiting sign-off
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
            <div class="col">
                <div class="card hr-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-uppercase text-muted small mb-1">Total Workforce</p>
                                <h3 class="mb-0">{{ number_format($headcount['total']) }}</h3>
                            </div>
                            <span class="bi bi-people text-primary fs-3"></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>{{ number_format($headcount['teachers']) }} teachers</span>
                            <span>{{ $structureTotals['departments'] }} departments</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card hr-stat-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">Active Coverage</p>
                        <div class="d-flex align-items-baseline justify-content-between mb-2">
                            <h3 class="mb-0">{{ number_format($headcount['active']) }}</h3>
                            <span class="badge bg-success-subtle text-success">{{ $activePercent }}%</span>
                        </div>
                        <div class="progress bg-light" style="height: 5px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activePercent }}%"
                                aria-valuenow="{{ $activePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted d-block mt-2">{{ number_format($headcount['probation']) }} on
                            probation</small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card hr-stat-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">People Ops Pulse</p>
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <h4 class="mb-0">{{ number_format($headcount['recent_hires']) }}</h4>
                                <small class="text-muted">Hires in 30 days</small>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0">{{ number_format($headcount['average_tenure_months']) }}<span
                                        class="fs-6"> mo</span></h4>
                                <small class="text-muted">Avg tenure</small>
                            </div>
                        </div>
                        <span class="badge bg-info-subtle text-info">{{ number_format($headcount['on_leave']) }} on
                            leave</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card hr-stat-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase text-muted small mb-1">Leave & Payroll</p>
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <h4 class="mb-0">{{ number_format($pendingLeaves) }}</h4>
                                <small class="text-muted">Pending leave</small>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0">{{ $pendingRuns }}</h4>
                                <small class="text-muted">Queued payroll</small>
                            </div>
                        </div>
                        <small class="text-muted">{{ formatMoney($payrollSummary['current_month_net'] ?? 0) }} net this
                            month</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i>Workforce Intelligence</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small">Status Mix</h6>
                                <ul class="list-group list-group-flush">
                                    @forelse ($statusDistribution as $statusRow)
                                        @php
                                            $statusPercent =
                                                $headcount['total'] > 0
                                                    ? round(($statusRow->total / $headcount['total']) * 100)
                                                    : 0;
                                        @endphp
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span
                                                class="text-capitalize">{{ str_replace('_', ' ', $statusRow->status ?? 'unknown') }}</span>
                                            <span class="fw-semibold">{{ number_format($statusRow->total) }} <small
                                                    class="text-muted">({{ $statusPercent }}%)</small></span>
                                        </li>
                                    @empty
                                        <li class="list-group-item px-0 text-muted">No staff data yet.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small">Gender Split</h6>
                                <ul class="list-group list-group-flush">
                                    @forelse ($genderDistribution as $gender)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span class="text-capitalize">{{ $gender->gender ?? 'Not set' }}</span>
                                            <span class="fw-semibold">{{ number_format($gender->total) }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item px-0 text-muted">Gender data not captured.</li>
                                    @endforelse
                                </ul>

                                <div class="mt-3">
                                    <span class="badge bg-light text-dark me-2">{{ $structureTotals['positions'] }}
                                        positions</span>
                                    <span class="badge bg-light text-dark">{{ $structureTotals['salary_scales'] }} salary
                                        scales</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0"><i class="bi bi-diagram-3-fill me-2"></i>Departments Leading Headcount
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Department</th>
                                        <th class="text-end">Team Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departmentLeaders as $department)
                                        <tr>
                                            <td>
                                                <strong>{{ $department->name }}</strong>
                                                <div class="text-muted small">{{ $department->code ?? 'No code' }}</div>
                                            </td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-primary-subtle text-primary">{{ number_format($department->employees_count) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-4">Create departments to
                                                see distribution.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0"><i class="bi bi-activity me-2"></i>Leave & Payroll Pulse</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small">Leave Overview</h6>
                                <p class="h3 mb-1">{{ number_format($pendingLeaves) }} <small
                                        class="fs-6 text-muted">pending</small></p>
                                <p class="text-muted small mb-3">
                                    {{ number_format($leaveSummary['approved_this_month'] ?? 0) }} approved this month ·
                                    {{ formatMoney($leaveSummary['financial_impact_month'] ?? 0) }} impact</p>
                                <ul class="list-group list-group-flush">
                                    @forelse ($leaveSummary['top_types'] as $type)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span>{{ $type->leaveType->name ?? 'Leave Type' }}</span>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary">{{ number_format($type->total) }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item px-0 text-muted">No leave activity yet.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small">Payroll</h6>
                                <p class="h3 mb-1">{{ formatMoney($payrollSummary['current_month_net'] ?? 0) }}</p>
                                <p class="text-muted small mb-3">Net released {{ now()->format('M') }}</p>
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Queued runs</span>
                                    <strong>{{ $pendingRuns }}</strong>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Last payment</span>
                                    <strong>{{ $lastPayroll?->payment_date?->format('M d, Y') ?? 'Not yet processed' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0"><i class="bi bi-cake me-2"></i>Upcoming Birthdays</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($upcomingBirthdays as $birthday)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $birthday->full_name ?? $birthday->first_name . ' ' . $birthday->last_name }}</strong>
                                        <div class="text-muted small">{{ $birthday->department->name ?? 'Unassigned' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-warning-subtle text-warning">{{ $birthday->days_until_birthday }}d</span>
                                        <div class="small text-muted">{{ $birthday->next_birthday->format('M d') }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No birthdays within 30 days.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0"><i class="bi bi-stars me-2"></i>Recent Hires</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentHires as $hire)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $hire->full_name ?? $hire->first_name . ' ' . $hire->last_name }}</strong>
                                            <div class="text-muted small">{{ $hire->department->name ?? 'Unassigned' }}
                                            </div>
                                        </div>
                                        <span
                                            class="text-muted small">{{ optional($hire->hire_date)->format('M d, Y') ?? 'N/A' }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No hires recorded yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>Core Workforce Records
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Employees</h6>
                                        <span class="badge bg-primary">Profiles</span>
                                    </div>
                                    <p class="text-muted small mb-3">Maintain biodata, contacts, contracts, and employment
                                        status for every staff member.</p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('tenant.modules.human-resource.employees.index') }}"
                                            class="btn btn-sm btn-outline-primary flex-grow-1">Open</a>
                                        <a href="{{ route('tenant.modules.human-resource.employees.create') }}"
                                            class="btn btn-sm btn-primary"><i class="bi bi-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Departments</h6>
                                        <span class="badge bg-success">Structure</span>
                                    </div>
                                    <p class="text-muted small mb-3">Define functional units, assign leadership, and link
                                        staff to their home department.</p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('tenant.modules.human-resource.departments.index') }}"
                                            class="btn btn-sm btn-outline-success flex-grow-1">Open</a>
                                        <a href="{{ route('tenant.modules.human-resource.departments.create') }}"
                                            class="btn btn-sm btn-success"><i class="bi bi-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Positions & Salary Scales</h6>
                                        <span class="badge bg-warning text-dark">Compensation</span>
                                    </div>
                                    <p class="text-muted small mb-3">Map job titles to grades, remuneration ranges, and
                                        benefits to enforce pay equity.</p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('tenant.modules.human-resource.positions.index') }}"
                                            class="btn btn-sm btn-outline-warning flex-grow-1">Positions</a>
                                        <a href="{{ route('tenant.modules.human-resource.salary-scales.index') }}"
                                            class="btn btn-sm btn-outline-warning flex-grow-1">Salary Scales</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Employee ID Cards</h6>
                                        <span class="badge bg-dark">Identity</span>
                                    </div>
                                    <p class="text-muted small mb-3">Generate secure staff ID cards with QR codes and
                                        branding consistent with your school.</p>
                                    @if ($employeeIdStudioUrl)
                                        <a href="{{ $employeeIdStudioUrl }}" class="btn btn-sm btn-dark w-100">Launch ID
                                            Studio</a>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                                            ID Studio coming soon
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>Payroll & Compliance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <h6 class="mb-2">Payroll Settings</h6>
                                    <p class="text-muted small mb-3">Configure pay periods, allowances, deductions, and
                                        statutory contributions.</p>
                                    <a href="{{ route('tenant.modules.human-resource.payroll-settings.index') }}"
                                        class="btn btn-sm btn-outline-secondary w-100">Open Payroll Settings</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100">
                                    <h6 class="mb-2">Payslip Processing</h6>
                                    <p class="text-muted small mb-3">Generate, preview, and release payslips with
                                        tenant-specific branding.</p>
                                    <a href="{{ route('tenant.modules.human-resource.payroll-payslip.index') }}"
                                        class="btn btn-sm btn-outline-secondary w-100">View Payslips</a>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                            <i class="bi bi-exclamation-octagon text-danger"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Audit Ready</h6>
                                            <small class="text-muted">Every payroll change is logged. Export summaries
                                                directly from each payroll screen.</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('tenant.modules.human-resource.payroll-settings.index') }}"
                                        class="btn btn-sm btn-danger">Review Compliance Checklist</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i>Leave & Attendance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('tenant.modules.human-resource.leave-types.index') }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>Leave Types</strong>
                                    <span class="d-block text-muted small">Entitlements, balances, blackout dates.</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('tenant.modules.human-resource.leave-requests.index') }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>Leave Requests</strong>
                                    <span class="d-block text-muted small">Approve, reject, or escalate submissions.</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('tenant.modules.human-resource.employees.index') }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>Staff Attendance</strong>
                                    <span class="d-block text-muted small">Sync with attendance analytics for
                                        compliance.</span>
                                </span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning-charge me-2"></i>Shortcuts
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('tenant.modules.human-resource.employees.index') }}"
                                class="btn btn-outline-primary">
                                <i class="bi bi-people"></i> View All Staff
                            </a>
                            <a href="{{ route('tenant.modules.human-resource.departments.index') }}"
                                class="btn btn-outline-secondary">
                                <i class="bi bi-diagram-3"></i> Manage Departments
                            </a>
                            <a href="{{ route('tenant.modules.human-resource.payroll-payslip.index') }}"
                                class="btn btn-outline-success">
                                <i class="bi bi-wallet2"></i> Run Payroll
                            </a>
                            <a href="{{ route('tenant.modules.human-resource.leave-requests.index') }}"
                                class="btn btn-outline-warning">
                                <i class="bi bi-envelope-open"></i> Review Leave Inbox
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
