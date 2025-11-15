@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('User Registration Approvals') }}</h1>
    </div>

    <!-- Status Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-warning mb-1">
                                <i class="bi bi-clock-history"></i> {{ __('Pending') }}
                            </h6>
                            <h2 class="mb-0">{{ $counts['pending'] }}</h2>
                        </div>
                        <i class="bi bi-clock-history text-warning" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-success mb-1">
                                <i class="bi bi-check-circle"></i> {{ __('Approved') }}
                            </h6>
                            <h2 class="mb-0">{{ $counts['approved'] }}</h2>
                        </div>
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-danger mb-1">
                                <i class="bi bi-x-circle"></i> {{ __('Rejected') }}
                            </h6>
                            <h2 class="mb-0">{{ $counts['rejected'] }}</h2>
                        </div>
                        <i class="bi bi-x-circle text-danger" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Status Filter -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}"
                href="{{ route('admin.user-approvals.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}">
                <i class="bi bi-clock-history"></i> {{ __('Pending') }}
                @if ($counts['pending'] > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $counts['pending'] }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}"
                href="{{ route('admin.user-approvals.index', array_merge(request()->except('status'), ['status' => 'approved'])) }}">
                <i class="bi bi-check-circle"></i> {{ __('Approved') }}
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}"
                href="{{ route('admin.user-approvals.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}">
                <i class="bi bi-x-circle"></i> {{ __('Rejected') }}
            </a>
        </li>
    </ul>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.user-approvals.index') }}" class="row g-3">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="{{ __('Search by name or email...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="Student" {{ request('role') === 'Student' ? 'selected' : '' }}>{{ __('Student') }}
                        </option>
                        <option value="Teacher" {{ request('role') === 'Teacher' ? 'selected' : '' }}>{{ __('Teacher') }}
                        </option>
                        <option value="Parent" {{ request('role') === 'Parent' ? 'selected' : '' }}>{{ __('Parent') }}
                        </option>
                        <option value="Staff" {{ request('role') === 'Staff' ? 'selected' : '' }}>{{ __('Staff') }}
                        </option>
                    </select>
                </div>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.user-approvals.index', ['status' => $status]) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                @if ($status === 'pending')
                    {{ __('Pending Registrations') }}
                @elseif($status === 'approved')
                    {{ __('Approved Registrations') }}
                @else
                    {{ __('Rejected Registrations') }}
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if ($users->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    @if ($status === 'pending')
                        {{ __('No pending user registrations at this time.') }}
                    @elseif($status === 'approved')
                        {{ __('No approved registrations found.') }}
                    @else
                        {{ __('No rejected registrations found.') }}
                    @endif
                </div>
            @else
                @if ($status === 'pending')
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-success" onclick="bulkApprove()">
                            <i class="bi bi-check-circle"></i> {{ __('Approve Selected') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#bulkRejectModal">
                            <i class="bi bi-x-circle"></i> {{ __('Reject Selected') }}
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                @if ($status === 'pending')
                                    <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                                @endif
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role(s)') }}</th>
                                <th>{{ __('Date') }}</th>
                                @if ($status === 'approved' || $status === 'rejected')
                                    <th>{{ __('Processed By') }}</th>
                                @endif
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    @if ($status === 'pending')
                                        <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                class="user-checkbox" form="bulkActionForm"></td>
                                    @endif
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if ($user->registration_data)
                                            <br><small class="text-muted">
                                                @if (isset($user->registration_data['student_id']))
                                                    ID: {{ $user->registration_data['student_id'] }}
                                                @endif
                                                @if (isset($user->registration_data['class']))
                                                    | Class: {{ $user->registration_data['class'] }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->getRoleNames() as $role)
                                            <span class="badge bg-info">{{ $role }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->diffForHumans() }}</small><br>
                                        <small class="text-muted">{{ $user->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    @if ($status === 'approved' || $status === 'rejected')
                                        <td>
                                            <small>{{ $user->approver->name ?? 'N/A' }}</small><br>
                                            <small
                                                class="text-muted">{{ $user->approved_at?->format('M d, Y H:i') }}</small>
                                        </td>
                                    @endif
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.user-approvals.show', $user) }}"
                                            class="btn btn-sm btn-info me-1" title="{{ __('View Details') }}">
                                            <i class="bi bi-eye"></i> {{ __('View') }}
                                        </a>
                                        @if ($status === 'pending')
                                            <form method="POST"
                                                action="{{ route('admin.user-approvals.approve', $user) }}"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success me-1"
                                                    title="{{ __('Approve') }}"
                                                    onclick="return confirm('{{ __('Approve this user?') }}')">
                                                    <i class="bi bi-check-circle"></i> {{ __('Approve') }}
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                title="{{ __('Reject') }}" data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $user->id }}">
                                                <i class="bi bi-x-circle"></i> {{ __('Reject') }}
                                            </button>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST"
                                                            action="{{ route('admin.user-approvals.reject', $user) }}">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('Reject Registration') }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ __('Please provide a reason for rejecting this registration:') }}
                                                                </p>
                                                                <textarea name="rejection_reason" class="form-control" rows="4" required
                                                                    placeholder="{{ __('Reason for rejection...') }}"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">{{ __('Reject') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($status === 'approved')
                                            @php
                                                $isStaffOrTeacher =
                                                    $user->hasRole('Staff') || $user->hasRole('Teacher');
                                                $isStudent = $user->hasRole('Student');
                                                $isParent = $user->hasRole('Parent');
                                                $showManage = $isStaffOrTeacher || $isStudent || $isParent;
                                            @endphp

                                            @if ($showManage)
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-gear"></i> {{ __('Manage') }}
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if ($isStaffOrTeacher)
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#employmentModal{{ $user->id }}">
                                                                    <i class="bi bi-briefcase me-1"></i>
                                                                    {{ __('Edit Employment') }}
                                                                </a>
                                                            </li>
                                                        @endif

                                                        @if ($isStudent)
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#studentEnrollmentModal{{ $user->id }}">
                                                                    <i class="bi bi-book me-1"></i>
                                                                    {{ __('Edit Enrollment') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if ($isParent)
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('tenant.users.parents.edit', $user) }}">
                                                                    <i class="bi bi-people-fill me-1"></i>
                                                                    {{ __('Link Children / Edit Guardian') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        @if ($user->is_active)
                                                            <li>
                                                                <form method="POST"
                                                                    action="{{ route('admin.user-approvals.suspend', $user) }}"
                                                                    class="px-3 py-1">
                                                                    @csrf
                                                                    <div class="d-flex gap-2">
                                                                        <input type="hidden" name="reason"
                                                                            value="{{ __('Suspended via approvals list') }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-warning w-100"
                                                                            onclick="return confirm('{{ __('Suspend this user?') }}')">
                                                                            <i class="bi bi-pause-circle"></i>
                                                                            {{ __('Suspend') }}
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <form method="POST"
                                                                    action="{{ route('admin.user-approvals.reinstate', $user) }}"
                                                                    class="px-3 py-1">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-success w-100"
                                                                        onclick="return confirm('{{ __('Reinstate this user?') }}')">
                                                                        <i class="bi bi-play-circle"></i>
                                                                        {{ __('Reinstate') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('admin.user-approvals.expel', $user) }}"
                                                                class="px-3 py-1">
                                                                @csrf
                                                                <div class="d-flex gap-2">
                                                                    <input type="hidden" name="reason"
                                                                        value="{{ __('Expelled/Terminated via approvals list') }}">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger w-100"
                                                                        onclick="return confirm('{{ __('Expel/Terminate this user? This will clear teaching allocations.') }}')">
                                                                        <i class="bi bi-slash-circle"></i>
                                                                        {{ __('Expel / Terminate') }}
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif

                                            <!-- Employment Edit Modal (for Staff/Teacher) -->
                                            @if ($isStaffOrTeacher)
                                                @php
                                                    $employee = \App\Models\Employee::where(
                                                        'user_id',
                                                        $user->id,
                                                    )->first();
                                                    $teacher = \App\Models\Teacher::where(
                                                        'email',
                                                        $user->email,
                                                    )->first();
                                                    $currentEmploymentType =
                                                        $teacher->employment_type ?? ($employee->employee_type ?? '');
                                                @endphp
                                                <div class="modal fade" id="employmentModal{{ $user->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <form method="POST"
                                                                action="{{ route('admin.user-approvals.employment', $user) }}"
                                                                id="employmentForm{{ $user->id }}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        {{ __('Edit Employment for') }}
                                                                        {{ $user->name }}</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Employment Role') }}</label>
                                                                            <select name="employment_role"
                                                                                class="form-select employment-role-select"
                                                                                required
                                                                                data-user-id="{{ $user->id }}">
                                                                                @foreach (['Teacher', 'Bursar', 'Nurse', 'Staff', 'Other'] as $role)
                                                                                    <option value="{{ $role }}"
                                                                                        {{ $user->hasRole($role) ? 'selected' : '' }}>
                                                                                        {{ __($role) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Employment Type') }}</label>
                                                                            <!-- Show dropdown for teachers with enum values -->
                                                                            <select name="employee_type"
                                                                                class="form-select employment-type-dropdown"
                                                                                id="employmentTypeDropdown{{ $user->id }}"
                                                                                style="{{ $user->hasRole('Teacher') ? '' : 'display:none;' }}">
                                                                                <option value="full_time"
                                                                                    {{ $currentEmploymentType === 'full_time' ? 'selected' : '' }}>
                                                                                    {{ __('Full Time') }}</option>
                                                                                <option value="part_time"
                                                                                    {{ $currentEmploymentType === 'part_time' ? 'selected' : '' }}>
                                                                                    {{ __('Part Time') }}</option>
                                                                                <option value="contract"
                                                                                    {{ $currentEmploymentType === 'contract' ? 'selected' : '' }}>
                                                                                    {{ __('Contract') }}</option>
                                                                                <option value="visiting"
                                                                                    {{ $currentEmploymentType === 'visiting' ? 'selected' : '' }}>
                                                                                    {{ __('Visiting') }}</option>
                                                                            </select>
                                                                            <!-- Show text input for non-teachers -->
                                                                            <input type="text"
                                                                                name="employee_type_text"
                                                                                class="form-control employment-type-text"
                                                                                id="employmentTypeText{{ $user->id }}"
                                                                                value="{{ $currentEmploymentType }}"
                                                                                placeholder="{{ __('e.g., full_time, administrative, etc.') }}"
                                                                                style="{{ $user->hasRole('Teacher') ? 'display:none;' : '' }}">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Department') }}</label>
                                                                            <select name="department_id"
                                                                                class="form-select employment-department-select"
                                                                                data-user-id="{{ $user->id }}">
                                                                                <option value="">
                                                                                    {{ __('— Select Department —') }}
                                                                                </option>
                                                                                @foreach ($departments ?? collect() as $department)
                                                                                    <option value="{{ $department->id }}"
                                                                                        {{ $employee?->department_id === $department->id ? 'selected' : '' }}>
                                                                                        {{ $department->name }}{{ $department->code ? ' (' . $department->code . ')' : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Position') }}</label>
                                                                            <select name="position_id"
                                                                                class="form-select employment-position-select"
                                                                                data-user-id="{{ $user->id }}">
                                                                                <option value="">
                                                                                    {{ __('— Select Position —') }}
                                                                                </option>
                                                                                @foreach ($positions ?? collect() as $position)
                                                                                    <option value="{{ $position->id }}"
                                                                                        data-department-id="{{ $position->department_id }}"
                                                                                        {{ $employee?->position_id === $position->id ? 'selected' : '' }}>
                                                                                        {{ $position->department?->name ? '[' . $position->department->name . '] ' : '' }}{{ $position->title }}{{ $position->code ? ' (' . $position->code . ')' : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="alert alert-info mb-0">
                                                                                <i class="bi bi-info-circle"></i>
                                                                                {{ __('If role is Teacher, you can optionally assign class and subjects right here.') }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Class (optional)') }}</label>
                                                                            <select name="class_id" class="form-select">
                                                                                <option value="">
                                                                                    {{ __('— Select —') }}</option>
                                                                                @foreach ($classes ?? collect() as $class)
                                                                                    <option value="{{ $class->id }}">
                                                                                        {{ $class->name }}
                                                                                        {{ $class->code ? '(' . $class->code . ')' : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Subjects (optional)') }}</label>
                                                                            <select name="subject_ids[]"
                                                                                class="form-select" multiple>
                                                                                @foreach ($subjects ?? collect() as $subj)
                                                                                    <option value="{{ $subj->id }}">
                                                                                        {{ $subj->educationLevel?->name ? '[' . $subj->educationLevel->name . '] ' : '' }}
                                                                                        {{ $subj->name }}
                                                                                        {{ $subj->code ? ' (' . $subj->code . ')' : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <small
                                                                                class="text-muted">{{ __('Hold Ctrl/Command to select multiple') }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">{{ __('Save Changes') }}</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Student Enrollment Modal (for Students) -->
                                            @if ($isStudent)
                                                @php
                                                    $student = \App\Models\Student::where(
                                                        'email',
                                                        $user->email,
                                                    )->first();
                                                    $studentClassId = $student->class_id ?? null;
                                                    $studentStreamId = $student->class_stream_id ?? null;
                                                    $studentSubjects = $student
                                                        ? $student->subjects->pluck('id')->toArray()
                                                        : [];
                                                @endphp
                                                <div class="modal fade" id="studentEnrollmentModal{{ $user->id }}"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <form method="POST"
                                                                action="{{ route('admin.user-approvals.student-enrollment', $user) }}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        {{ __('Edit Enrollment for') }}
                                                                        {{ $user->name }}</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">{{ __('Class') }}
                                                                                <span class="text-danger">*</span></label>
                                                                            <select name="class_id"
                                                                                class="form-select student-class-select"
                                                                                id="studentClassSelect{{ $user->id }}"
                                                                                data-user-id="{{ $user->id }}"
                                                                                required>
                                                                                <option value="">
                                                                                    {{ __('— Select Class —') }}</option>
                                                                                @foreach ($classes ?? collect() as $class)
                                                                                    <option value="{{ $class->id }}"
                                                                                        {{ $studentClassId == $class->id ? 'selected' : '' }}>
                                                                                        {{ $class->name }}
                                                                                        {{ $class->code ? '(' . $class->code . ')' : '' }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label">{{ __('Stream') }}</label>
                                                                            <select name="class_stream_id"
                                                                                class="form-select student-stream-select"
                                                                                id="studentStreamSelect{{ $user->id }}"
                                                                                data-user-id="{{ $user->id }}">
                                                                                <option value="">
                                                                                    {{ __('— No Stream —') }}</option>
                                                                                @foreach ($streams ?? collect() as $stream)
                                                                                    <option value="{{ $stream->id }}"
                                                                                        {{ $studentStreamId == $stream->id ? 'selected' : '' }}
                                                                                        data-class-id="{{ $stream->class_id }}">
                                                                                        {{ $stream->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <label
                                                                                class="form-label">{{ __('Subjects') }}</label>
                                                                            <select name="subject_ids[]"
                                                                                class="form-select" multiple
                                                                                size="8">
                                                                                @php($groupedSubjects = $subjectsByLevel ?? collect())
                                                                                @if ($groupedSubjects->isEmpty())
                                                                                    @foreach ($subjects ?? collect() as $subj)
                                                                                        <option
                                                                                            value="{{ $subj->id }}"
                                                                                            {{ in_array($subj->id, $studentSubjects) ? 'selected' : '' }}>
                                                                                            {{ $subj->educationLevel?->name ? '[' . $subj->educationLevel->name . '] ' : '' }}
                                                                                            {{ $subj->name }}
                                                                                            {{ $subj->code ? ' (' . $subj->code . ')' : '' }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                @else
                                                                                    @foreach ($groupedSubjects as $levelName => $levelSubjects)
                                                                                        <optgroup
                                                                                            label="{{ $levelName }}">
                                                                                            @foreach ($levelSubjects as $subj)
                                                                                                <option
                                                                                                    value="{{ $subj->id }}"
                                                                                                    {{ in_array($subj->id, $studentSubjects) ? 'selected' : '' }}>
                                                                                                    {{ $subj->name }}{{ $subj->code ? ' (' . $subj->code . ')' : '' }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </optgroup>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                            <small
                                                                                class="text-muted">{{ __('Hold Ctrl/Command to select multiple subjects') }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">{{ __('Save Changes') }}</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($status === 'pending')
                    <form id="bulkActionForm" method="POST" class="d-none">
                        @csrf
                    </form>
                @endif

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Reject Modal -->
    @if ($status === 'pending')
        <div class="modal fade" id="bulkRejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.user-approvals.bulk-reject') }}" id="bulkRejectForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Bulk Reject Registrations') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>{{ __('Please provide a reason for rejecting the selected registrations:') }}</p>
                            <textarea name="rejection_reason" class="form-control" rows="4" required
                                placeholder="{{ __('Reason for rejection...') }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('Reject Selected') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        // Select all checkboxes
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        // Bulk approve function
        function bulkApprove() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('{{ __('Please select at least one user to approve.') }}');
                return;
            }

            if (!confirm('{{ __('Approve selected users?') }}')) {
                return;
            }

            const form = document.getElementById('bulkActionForm');
            form.action = '{{ route('admin.user-approvals.bulk-approve') }}';
            form.submit();
        }

        // Bulk reject - copy selected user IDs to reject modal form
        const bulkRejectModal = document.getElementById('bulkRejectModal');
        if (bulkRejectModal) {
            bulkRejectModal.addEventListener('show.bs.modal', function(event) {
                const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('{{ __('Please select at least one user to reject.') }}');
                    event.preventDefault();
                    return;
                }

                // Copy checkboxes to reject form
                const rejectForm = document.getElementById('bulkRejectForm');
                document.querySelectorAll('#bulkRejectForm input[name="user_ids[]"]').forEach(el => el.remove());

                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = checkbox.value;
                    rejectForm.appendChild(input);
                });
            });
        }

        // Toggle employment type input based on role selection
        document.querySelectorAll('.employment-role-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const dropdown = document.getElementById('employmentTypeDropdown' + userId);
                const textInput = document.getElementById('employmentTypeText' + userId);

                if (this.value === 'Teacher') {
                    dropdown.style.display = '';
                    dropdown.disabled = false;
                    textInput.style.display = 'none';
                    textInput.disabled = true;
                } else {
                    dropdown.style.display = 'none';
                    dropdown.disabled = true;
                    textInput.style.display = '';
                    textInput.disabled = false;
                }
            });
        });

        // Handle employment form submission - use appropriate field based on role
        document.querySelectorAll('form[id^="employmentForm"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const roleSelect = this.querySelector('.employment-role-select');
                const dropdown = this.querySelector('.employment-type-dropdown');
                const textInput = this.querySelector('.employment-type-text');

                // If Teacher role, use dropdown value; otherwise use text input
                if (roleSelect.value === 'Teacher') {
                    textInput.disabled = true;
                    dropdown.name = 'employee_type';
                } else {
                    dropdown.disabled = true;
                    textInput.name = 'employee_type';
                }
            });
        });

        // Filter positions by selected department in employment modal
        document.querySelectorAll('.employment-department-select').forEach(function(deptSelect) {
            const userId = deptSelect.dataset.userId;
            const positionSelect = document.querySelector('.employment-position-select[data-user-id="' + userId +
                '"]');
            if (!positionSelect) {
                return;
            }

            const filterPositions = function() {
                const departmentId = deptSelect.value;
                let selectedVisible = false;

                Array.from(positionSelect.options).forEach(function(option) {
                    const optionDeptId = option.getAttribute('data-department-id');
                    if (!optionDeptId) {
                        option.hidden = false;
                        return;
                    }

                    const shouldShow = !departmentId || optionDeptId === departmentId;
                    option.hidden = !shouldShow;

                    if (shouldShow && option.value === positionSelect.value) {
                        selectedVisible = true;
                    }

                    if (!shouldShow && option.selected) {
                        option.selected = false;
                    }
                });

                if (positionSelect.value && !selectedVisible) {
                    positionSelect.value = '';
                }
            };

            deptSelect.addEventListener('change', filterPositions);
            filterPositions();
        });

        // Filter stream options based on selected class in student enrollment modals
        document.querySelectorAll('.student-class-select').forEach(function(classSelect) {
            const userId = classSelect.dataset.userId;
            const streamSelect = document.querySelector('.student-stream-select[data-user-id="' + userId + '"]');
            if (!streamSelect) {
                return;
            }

            const filterStreams = function() {
                const selectedClassId = classSelect.value;
                let visibleSelected = false;

                Array.from(streamSelect.options).forEach(function(option) {
                    const optionClassId = option.getAttribute('data-class-id');
                    if (!optionClassId) {
                        option.hidden = false;
                        return;
                    }

                    const shouldShow = !selectedClassId || optionClassId === selectedClassId;
                    option.hidden = !shouldShow;

                    if (shouldShow && option.value === streamSelect.value) {
                        visibleSelected = true;
                    }

                    if (!shouldShow && option.selected) {
                        option.selected = false;
                    }
                });

                if (streamSelect.value && !visibleSelected) {
                    streamSelect.value = '';
                }
            };

            classSelect.addEventListener('change', filterStreams);
            filterStreams();
        });
    </script>
@endpush
