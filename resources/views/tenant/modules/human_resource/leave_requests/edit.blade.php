@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h4 fw-semibold mb-0">{{ __('Review Leave Request') }}</h1>
                <div class="small text-secondary">{{ __('Approve or reject the leave request') }}</div>
            </div>
            <a href="{{ route('tenant.modules.human-resource.leave-requests.show', $leaveRequest) }}"
                class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> {{ __('Back to Details') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Request Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Leave Request Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>{{ __('Employee:') }}</strong>
                                    {{ $leaveRequest->employee->first_name }} {{ $leaveRequest->employee->last_name }}</p>
                                <p class="mb-1"><strong>{{ __('Leave Type:') }}</strong>
                                    {{ $leaveRequest->leaveType->name }}</p>
                                <p class="mb-0"><strong>{{ __('Days Requested:') }}</strong>
                                    {{ $leaveRequest->days_requested }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>{{ __('From:') }}</strong>
                                    {{ $leaveRequest->start_date->format('M d, Y') }}</p>
                                <p class="mb-1"><strong>{{ __('To:') }}</strong>
                                    {{ $leaveRequest->end_date->format('M d, Y') }}</p>
                                <p class="mb-0"><strong>{{ __('Status:') }}</strong>
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <strong>{{ __('Reason:') }}</strong>
                            <p class="mt-2">{{ $leaveRequest->reason }}</p>
                        </div>
                    </div>
                </div>

                <!-- Review Form -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Review Decision') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('tenant.modules.human-resource.leave-requests.update', $leaveRequest) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label">{{ __('Decision') }} <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-check-lg">
                                            <input class="form-check-input" type="radio" name="status"
                                                id="status_approved" value="approved" required>
                                            <label class="form-check-label" for="status_approved">
                                                <i class="bi bi-check-circle text-success me-2"></i>
                                                <strong class="text-success">{{ __('Approve Request') }}</strong>
                                                <br><small
                                                    class="text-muted">{{ __('Allow the employee to take the requested leave') }}</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-check-lg">
                                            <input class="form-check-input" type="radio" name="status"
                                                id="status_rejected" value="rejected" required>
                                            <label class="form-check-label" for="status_rejected">
                                                <i class="bi bi-x-circle text-danger me-2"></i>
                                                <strong class="text-danger">{{ __('Reject Request') }}</strong>
                                                <br><small class="text-muted">{{ __('Deny the leave request') }}</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="manager_comment" class="form-label">{{ __('Comments') }} <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="manager_comment" name="manager_comment" rows="4"
                                    placeholder="{{ __('Provide feedback or reason for your decision...') }}" required>{{ old('manager_comment') }}</textarea>
                                <div class="form-text">{{ __('Your comments will be visible to the employee') }}</div>
                                @error('manager_comment')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                {{ __('This action cannot be undone. The employee will be notified of your decision.') }}
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> {{ __('Submit Decision') }}
                                </button>
                                <a href="{{ route('tenant.modules.human-resource.leave-requests.show', $leaveRequest) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Leave Balance Check') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $leaveRequest->leaveType->name }}</span>
                                <span class="badge bg-info">{{ $leaveRequest->leaveType->default_days }} days</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                            <small class="text-muted">{{ __('15 days remaining') }}</small>
                        </div>

                        @if ($leaveRequest->days_requested > 15)
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle"></i>
                                {{ __('Warning: Employee is requesting more days than available balance.') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Review Guidelines') }}</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Check employee leave balance') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Verify leave type policies') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Consider business impact') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Provide clear feedback') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('Quick Stats') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="h5 text-warning mb-1">
                                    {{ \App\Models\LeaveRequest::where('status', 'pending')->count() }}</div>
                                <small class="text-muted">{{ __('Pending') }}</small>
                            </div>
                            <div class="col-6">
                                <div class="h5 text-success mb-1">
                                    {{ \App\Models\LeaveRequest::where('status', 'approved')->count() }}</div>
                                <small class="text-muted">{{ __('Approved') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const statusSelected = document.querySelector('input[name="status"]:checked');
                const comment = document.getElementById('manager_comment').value.trim();

                if (!statusSelected) {
                    e.preventDefault();
                    alert('{{ __('Please select a decision (Approve or Reject)') }}');
                    return false;
                }

                if (!comment) {
                    e.preventDefault();
                    alert('{{ __('Please provide comments for your decision') }}');
                    return false;
                }

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> {{ __('Processing...') }}';
            });
        });
    </script>
@endsection
