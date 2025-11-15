@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', 'Staff Attendance')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-user-tie me-2 text-success"></i>
                    Staff Attendance
                </h2>
                <p class="text-muted mb-0">Track and manage staff attendance records</p>
            </div>
            <a href="{{ route('admin.staff-attendance.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i>Record Attendance
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.staff-attendance.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ request('date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present
                                </option>
                                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="staff_id" class="form-label">Staff Member</label>
                            <select class="form-select" id="staff_id" name="staff_id">
                                <option value="">All Staff</option>
                                @foreach ($staff as $member)
                                    <option value="{{ $member->id }}"
                                        {{ request('staff_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.staff-attendance.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Staff Attendance Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Staff Attendance Records
                </h5>
            </div>
            <div class="card-body">
                @if ($attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Staff Member</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Hours</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                        <td>{{ $attendance->staff->full_name }}</td>
                                        <td><span
                                                class="badge {{ $attendance->getStatusBadgeClass() }}">{{ $attendance->getStatusLabel() }}</span>
                                        </td>
                                        <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}</td>
                                        <td>{{ $attendance->hours_worked ? number_format($attendance->hours_worked, 2) : '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.staff-attendance.show', $attendance->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.staff-attendance.edit', $attendance->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if (!$attendance->approved)
                                                <form
                                                    action="{{ route('admin.staff-attendance.approve', $attendance->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $attendances->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-clock fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No staff attendance records found.</p>
                        <a href="{{ route('admin.staff-attendance.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Record First Attendance
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
