@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h4 fw-semibold mb-0">{{ __('Leave Request Details') }}</h1>
      <div class="small text-secondary">{{ __('Review leave request information') }}</div>
    </div>
    <div class="d-flex gap-2">
      @if($leaveRequest->status === 'pending')
        <a href="{{ route('tenant.modules.human_resources.leave_requests.edit', $leaveRequest) }}" class="btn btn-warning btn-sm">
          <i class="bi bi-pencil"></i> {{ __('Review Request') }}
        </a>
      @endif
      <a href="{{ route('tenant.modules.human_resources.leave_requests.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> {{ __('Back to List') }}
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">{{ __('Request Information') }}</h5>
            @switch($leaveRequest->status)
              @case('pending')
                <span class="badge bg-warning fs-6">{{ __('Pending Review') }}</span>
                @break
              @case('approved')
                <span class="badge bg-success fs-6">{{ __('Approved') }}</span>
                @break
              @case('rejected')
                <span class="badge bg-danger fs-6">{{ __('Rejected') }}</span>
                @break
              @default
                <span class="badge bg-secondary fs-6">{{ ucfirst($leaveRequest->status) }}</span>
            @endswitch
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="text-muted mb-2">{{ __('Employee Details') }}</h6>
              <div class="d-flex align-items-center mb-2">
                <div class="avatar avatar-md me-3">
                  <span class="avatar-initial rounded-circle bg-primary text-white">
                    {{ substr($leaveRequest->employee->first_name, 0, 1) }}{{ substr($leaveRequest->employee->last_name, 0, 1) }}
                  </span>
                </div>
                <div>
                  <h6 class="mb-0">{{ $leaveRequest->employee->first_name }} {{ $leaveRequest->employee->last_name }}</h6>
                  <small class="text-muted">{{ $leaveRequest->employee->employee_type }}</small>
                </div>
              </div>
              <p class="mb-1"><strong>{{ __('Email:') }}</strong> {{ $leaveRequest->employee->email }}</p>
              <p class="mb-0"><strong>{{ __('Phone:') }}</strong> {{ $leaveRequest->employee->phone ?? 'Not provided' }}</p>
            </div>

            <div class="col-md-6">
              <h6 class="text-muted mb-2">{{ __('Leave Details') }}</h6>
              <p class="mb-1"><strong>{{ __('Leave Type:') }}</strong> {{ $leaveRequest->leaveType->name }} ({{ $leaveRequest->leaveType->code }})</p>
              <p class="mb-1"><strong>{{ __('Start Date:') }}</strong> {{ $leaveRequest->start_date->format('l, F d, Y') }}</p>
              <p class="mb-1"><strong>{{ __('End Date:') }}</strong> {{ $leaveRequest->end_date->format('l, F d, Y') }}</p>
              <p class="mb-1"><strong>{{ __('Days Requested:') }}</strong> {{ $leaveRequest->days_requested }} working days</p>
              <p class="mb-0"><strong>{{ __('Submitted:') }}</strong> {{ $leaveRequest->created_at->format('M d, Y H:i') }}</p>
            </div>
          </div>

          <div class="mb-4">
            <h6 class="text-muted mb-2">{{ __('Reason for Leave') }}</h6>
            <div class="bg-light p-3 rounded">
              {{ $leaveRequest->reason }}
            </div>
          </div>

          @if($leaveRequest->manager_comment)
            <div class="mb-4">
              <h6 class="text-muted mb-2">{{ __('Manager Comments') }}</h6>
              <div class="bg-light p-3 rounded">
                {{ $leaveRequest->manager_comment }}
              </div>
            </div>
          @endif

          @if($leaveRequest->status !== 'pending')
            <div class="alert alert-info">
              <i class="bi bi-info-circle"></i>
              {{ __('This leave request was') }}
              <strong>{{ $leaveRequest->status === 'approved' ? __('approved') : __('rejected') }}</strong>
              {{ __('on') }} {{ $leaveRequest->updated_at->format('M d, Y H:i') }}.
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header">
          <h6 class="mb-0">{{ __('Leave Balance') }}</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>{{ $leaveRequest->leaveType->name }}</span>
              <span class="badge bg-info">{{ $leaveRequest->leaveType->default_days }} days</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-success" style="width: 75%"></div>
            </div>
            <small class="text-muted">{{ __('15 days remaining') }}</small>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mt-3">
        <div class="card-header">
          <h6 class="mb-0">{{ __('Quick Actions') }}</h6>
        </div>
        <div class="card-body">
          @if($leaveRequest->status === 'pending')
            <div class="d-grid gap-2">
              <form method="POST" action="{{ route('tenant.modules.human_resources.leave_requests.update', $leaveRequest) }}" style="display: inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Are you sure you want to approve this leave request?') }}')">
                  <i class="bi bi-check-circle"></i> {{ __('Approve Request') }}
                </button>
              </form>

              <form method="POST" action="{{ route('tenant.modules.human_resources.leave_requests.update', $leaveRequest) }}" style="display: inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('{{ __('Are you sure you want to reject this leave request?') }}')">
                  <i class="bi bi-x-circle"></i> {{ __('Reject Request') }}
                </button>
              </form>
            </div>
          @else
            <div class="text-center text-muted">
              <i class="bi bi-check-circle display-4 mb-2"></i>
              <p>{{ __('This request has been') }} <strong>{{ $leaveRequest->status }}</strong>.</p>
            </div>
          @endif
        </div>
      </div>

      <div class="card shadow-sm mt-3">
        <div class="card-header">
          <h6 class="mb-0">{{ __('Leave History') }}</h6>
        </div>
        <div class="card-body">
          <div class="text-center text-muted">
            <small>{{ __('Employee leave history will be displayed here') }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
