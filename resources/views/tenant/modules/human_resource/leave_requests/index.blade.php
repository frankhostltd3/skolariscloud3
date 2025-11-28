@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Leave Management') }}</h1>
            <div class="small text-secondary">
                {{ __('Review, approve, or decline staff leave requests with financial tracking.') }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tenant.modules.human-resource.leave-requests.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> {{ __('New Leave Request') }}
            </a>
            <a href="{{ route('tenant.modules.human-resource.leave-requests.financial-report') }}"
                class="btn btn-outline-info btn-sm">
                <i class="bi bi-graph-up"></i> {{ __('Financial Report') }}
            </a>
            <a href="{{ route('tenant.modules.human-resource.leave-requests.export-financial', request()->all()) }}"
                class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> {{ __('Export CSV') }}
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    @if (isset($summary))
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">{{ __('Total Requests') }}</div>
                                <h4 class="mb-0">{{ $summary['total_requests'] }}</h4>
                            </div>
                            <div class="text-primary"><i class="bi bi-file-text fs-2"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">{{ __('Pending Approval') }}</div>
                                <h4 class="mb-0">{{ $summary['pending'] }}</h4>
                            </div>
                            <div class="text-warning"><i class="bi bi-clock-history fs-2"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">{{ __('Total Days Off') }}</div>
                                <h4 class="mb-0">{{ number_format($summary['total_days']) }}</h4>
                            </div>
                            <div class="text-success"><i class="bi bi-calendar-check fs-2"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">{{ __('Unpaid Deductions') }}</div>
                                <h4 class="mb-0 small">{{ format_money($summary['unpaid_deductions']) }}</h4>
                            </div>
                            <div class="text-danger"><i class="bi bi-cash-coin fs-2"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="status_filter" class="form-label">{{ __('Status') }}</label>
                    <select class="form-select form-select-sm" id="status_filter" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                            {{ __('Approved') }}</option>
                        <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>
                            {{ __('Declined') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="employee_filter" class="form-label">{{ __('Employee') }}</label>
                    <input type="text" class="form-control form-control-sm" id="employee_filter" name="employee"
                        value="{{ request('employee') }}" placeholder="{{ __('Search by name') }}">
                </div>
                <div class="col-md-2">
                    <label for="leave_type_filter" class="form-label">{{ __('Leave Type') }}</label>
                    <input type="text" class="form-control form-control-sm" id="leave_type_filter" name="leave_type"
                        value="{{ request('leave_type') }}" placeholder="{{ __('Type or code') }}">
                </div>
                <div class="col-md-2">
                    <label for="year_filter" class="form-label">{{ __('Year') }}</label>
                    <select class="form-select form-select-sm" id="year_filter" name="year">
                        <option value="">{{ __('All Years') }}</option>
                        @if (isset($years))
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="month_filter" class="form-label">{{ __('Month') }}</label>
                    <select class="form-select form-select-sm" id="month_filter" name="month">
                        <option value="">{{ __('All Months') }}</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary btn-sm me-1">{{ __('Filter') }}</button>
                    <a href="{{ route('tenant.modules.human-resource.leave-requests.index') }}"
                        class="btn btn-outline-secondary btn-sm">{{ __('Clear') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Leave Type') }}</th>
                        <th>{{ __('Dates') }}</th>
                        <th>{{ __('Days') }}</th>
                        <th>{{ __('Financial Impact') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $leaveRequest)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-primary text-white">
                                            {{ substr($leaveRequest->employee->first_name, 0, 1) }}{{ substr($leaveRequest->employee->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $leaveRequest->employee->first_name }}
                                            {{ $leaveRequest->employee->last_name }}</div>
                                        <small class="text-muted">{{ $leaveRequest->employee->employee_type }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $leaveRequest->leaveType->name }}</span>
                                <br><small class="text-muted">{{ $leaveRequest->leaveType->code }}</small>
                            </td>
                            <td>
                                <div>{{ $leaveRequest->start_date->format('M d, Y') }}</div>
                                <small class="text-muted">to {{ $leaveRequest->end_date->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $leaveRequest->days_requested ?? 0 }} days</span>
                            </td>
                            <td>
                                @if ($leaveRequest->is_paid)
                                    <span class="text-success">
                                        <i class="bi bi-check-circle"></i> {{ __('Paid') }}
                                    </span>
                                    <br><small
                                        class="text-muted">{{ format_money($leaveRequest->financial_impact ?? 0) }}</small>
                                @else
                                    <span class="text-danger">
                                        <i class="bi bi-dash-circle"></i> {{ __('Unpaid') }}
                                    </span>
                                    <br><small
                                        class="text-danger">-{{ format_money($leaveRequest->financial_impact ?? 0) }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($leaveRequest->status)
                                    @case('pending')
                                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                                    @break

                                    @case('approved')
                                        <span class="badge bg-success">{{ __('Approved') }}</span>
                                    @break

                                    @case('declined')
                                        <span class="badge bg-danger">{{ __('Declined') }}</span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($leaveRequest->status) }}</span>
                                @endswitch
                                @if ($leaveRequest->approved_at)
                                    <br><small class="text-muted">{{ $leaveRequest->approved_at->format('M d') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('tenant.modules.human-resource.leave-requests.show', $leaveRequest) }}"
                                        class="btn btn-outline-info" title="{{ __('View Details') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if ($leaveRequest->status === 'pending')
                                        <form method="POST"
                                            action="{{ route('tenant.modules.human-resource.leave-requests.approve', $leaveRequest) }}"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success"
                                                title="{{ __('Approve') }}"
                                                onclick="return confirm('{{ __('Approve this leave request?') }}')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('tenant.modules.human-resource.leave-requests.reject', $leaveRequest) }}"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger"
                                                title="{{ __('Decline') }}"
                                                onclick="return confirm('{{ __('Decline this leave request?') }}')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">
                                    <i class="bi bi-calendar-x display-4 text-muted mb-3 d-block"></i>
                                    <div>{{ __('No leave requests found matching your filters') }}</div>
                                    <a href="{{ route('tenant.modules.human-resource.leave-requests.index') }}"
                                        class="btn btn-outline-secondary btn-sm mt-2">
                                        {{ __('Clear Filters') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($leaveRequests->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $leaveRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endsection
