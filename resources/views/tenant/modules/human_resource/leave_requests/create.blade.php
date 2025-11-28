@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h4 fw-semibold mb-0">{{ __('Submit Leave Request') }}</h1>
                <div class="small text-secondary">{{ __('Request time off from work') }}</div>
            </div>
            <a href="{{ route('tenant.modules.human-resource.leave-requests.index') }}"
                class="btn btn-secondary btn-sm">{{ __('Back to Leave Requests') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('tenant.modules.human-resource.leave-requests.store') }}"
                            id="leaveRequestForm">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="employee_id" class="form-label">{{ __('Employee') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="employee_id" name="employee_id" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                                ({{ $employee->employee_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="leave_type_id" class="form-label">{{ __('Leave Type') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                                        <option value="">{{ __('Select Leave Type') }}</option>
                                        @foreach ($leaveTypes as $leaveType)
                                            <option value="{{ $leaveType->id }}"
                                                {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                                {{ $leaveType->name }} ({{ $leaveType->code }} -
                                                {{ $leaveType->default_days }} days)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leave_type_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">{{ __('Start Date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                                    @error('start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">{{ __('End Date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required>
                                    @error('end_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="days_requested" class="form-label">{{ __('Days Requested') }}</label>
                                    <input type="number" class="form-control" id="days_requested" name="days_requested"
                                        value="{{ old('days_requested', 0) }}" readonly>
                                    <div class="form-text">{{ __('Calculated automatically') }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">{{ __('Reason for Leave') }} <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="reason" name="reason" rows="4"
                                    placeholder="{{ __('Please provide details about why you are requesting leave...') }}" required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                {{ __('Your leave request will be submitted for approval. You will be notified once it is reviewed.') }}
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> {{ __('Submit Request') }}
                                </button>
                                <a href="{{ route('tenant.modules.human-resource.leave-requests.index') }}"
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
                        <h6 class="mb-0">{{ __('Leave Request Guidelines') }}</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Submit requests at least 2 weeks in advance') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Provide clear reason for your leave') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Check your leave balance before requesting') }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i>
                                {{ __('Emergency leave may require immediate approval') }}</li>
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
                                <div class="h4 text-primary mb-1">{{ $leaveTypes->count() }}</div>
                                <small class="text-muted">{{ __('Leave Types') }}</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 text-success mb-1">{{ $employees->count() }}</div>
                                <small class="text-muted">{{ __('Active Employees') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const daysRequestedInput = document.getElementById('days_requested');

            function calculateDays() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (startDate && endDate && startDate <= endDate) {
                    // Calculate working days (excluding weekends)
                    let days = 0;
                    let currentDate = new Date(startDate);

                    while (currentDate <= endDate) {
                        const dayOfWeek = currentDate.getDay();
                        // 0 = Sunday, 6 = Saturday
                        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                            days++;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }

                    daysRequestedInput.value = days;
                } else {
                    daysRequestedInput.value = 0;
                }
            }

            // Calculate days when dates change
            startDateInput.addEventListener('change', calculateDays);
            endDateInput.addEventListener('change', calculateDays);

            // Set minimum end date when start date changes
            startDateInput.addEventListener('change', function() {
                endDateInput.min = startDateInput.value;
                if (endDateInput.value && endDateInput.value < startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
                calculateDays();
            });

            // Form validation
            document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (startDate < today) {
                    e.preventDefault();
                    alert('{{ __('Start date cannot be in the past') }}');
                    return false;
                }

                if (endDate < startDate) {
                    e.preventDefault();
                    alert('{{ __('End date must be after or equal to start date') }}');
                    return false;
                }

                const days = parseInt(daysRequestedInput.value);
                if (days === 0) {
                    e.preventDefault();
                    alert('{{ __('Leave request must include at least one working day') }}');
                    return false;
                }
            });
        });
    </script>
@endsection
