@extends('layouts.tenant.student')

@section('title', 'My Attendance History')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>My Attendance History
                </h2>
                <p class="text-muted mb-0">Track your virtual class attendance</p>
            </div>
            <a href="{{ route('tenant.student.classroom.virtual.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to Classes
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Attendance Rate</p>
                                <h3
                                    class="mb-0 {{ $stats['attendance_rate'] >= 75 ? 'text-success' : ($stats['attendance_rate'] >= 50 ? 'text-warning' : 'text-danger') }}">
                                    {{ $stats['attendance_rate'] }}%
                                </h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                <i class="bi bi-pie-chart text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 5px;">
                            <div class="progress-bar {{ $stats['attendance_rate'] >= 75 ? 'bg-success' : ($stats['attendance_rate'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                role="progressbar" style="width: {{ $stats['attendance_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Present</p>
                                <h3 class="mb-0 text-success">{{ $stats['present'] }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-2 rounded">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Late</p>
                                <h3 class="mb-0 text-warning">{{ $stats['late'] }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-2 rounded">
                                <i class="bi bi-clock-history text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Absent</p>
                                <h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-2 rounded">
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Attendance Log</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date & Time</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Status</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $attendance->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $attendance->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>{{ $attendance->virtualClass->title }}</td>
                                    <td>{{ $attendance->virtualClass->subject->name }}</td>
                                    <td>{{ $attendance->virtualClass->teacher->name }}</td>
                                    <td>
                                        @if ($attendance->status === 'present')
                                            <span class="badge bg-success">Present</span>
                                        @elseif($attendance->status === 'late')
                                            <span class="badge bg-warning">Late</span>
                                        @else
                                            <span class="badge bg-danger">Absent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($attendance->duration_minutes)
                                            {{ $attendance->duration_minutes }} mins
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3 mb-0">No attendance records found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($attendances->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
