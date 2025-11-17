@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Attendance Dashboard</h1>
            <p class="text-muted mb-0">{{ $today->format('l, F d, Y') }}</p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.attendance.take') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Take Roll Call
            </a>
            <a href="{{ route('tenant.teacher.attendance.reports') }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-text me-2"></i>Reports
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Today's Summary -->
    <div class="row mb-4">
        @forelse($attendanceSummary as $summary)
            @php
                $class = $summary['class'];
                $percentageClass = $summary['percentage'] >= 90 ? 'success' : ($summary['percentage'] >= 75 ? 'warning' : 'danger');
            @endphp
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-building me-2"></i>{{ $class->name }}
                            @if($class->section) - {{ $class->section }}@endif
                            @if($class->stream) ({{ $class->stream }})@endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Today's Attendance</span>
                                <span class="badge bg-{{ $percentageClass }} fs-6">{{ $summary['percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $percentageClass }}" 
                                     role="progressbar" 
                                     style="width: {{ $summary['percentage'] }}%"
                                     aria-valuenow="{{ $summary['percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row text-center g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <h4 class="mb-0 text-success">{{ $summary['present'] }}</h4>
                                    <small class="text-muted">Present</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <h4 class="mb-0 text-danger">{{ $summary['absent'] }}</h4>
                                    <small class="text-muted">Absent</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <h4 class="mb-0 text-warning">{{ $summary['late'] }}</h4>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded">
                                    <h4 class="mb-0 text-secondary">{{ $summary['pending'] }}</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Total Students:</span>
                            <strong class="fs-5">{{ $summary['total'] }}</strong>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            @if($summary['pending'] > 0)
                                <a href="{{ route('tenant.teacher.attendance.take', ['class_id' => $class->id]) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-clipboard-check me-2"></i>Mark Attendance
                                </a>
                            @else
                                <a href="{{ route('tenant.teacher.attendance.manual', ['class_id' => $class->id]) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil me-2"></i>Edit Attendance
                                </a>
                            @endif
                            <a href="{{ route('tenant.teacher.attendance.patterns', ['class_id' => $class->id]) }}" 
                               class="btn btn-outline-info btn-sm">
                                <i class="bi bi-graph-up me-2"></i>View Patterns
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-muted small">
                        <i class="bi bi-person-check me-1"></i>{{ $summary['marked'] }}/{{ $summary['total'] }} marked today
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No Classes Assigned</h5>
                        <p class="text-muted">You don't have any classes assigned yet.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Recent Activity -->
    @if($recentActivity->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>Recent Activity
                            <span class="badge bg-secondary ms-2">Last 10</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                        <th>Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivity as $activity)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $activity->attendance_date->format('M d, Y') }}
                                                    <br>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $activity->created_at->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($activity->student && $activity->student->photo)
                                                        <img src="{{ asset('storage/' . $activity->student->photo) }}" 
                                                             alt="{{ $activity->student->name }}" 
                                                             class="rounded-circle me-2" 
                                                             width="32" height="32">
                                                    @else
                                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 32px; height: 32px; font-size: 14px;">
                                                            {{ $activity->student ? strtoupper(substr($activity->student->name, 0, 1)) : '?' }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $activity->student->name ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $activity->class->name ?? 'N/A' }} {{ $activity->class->section ?? '' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'present' => 'success',
                                                        'absent' => 'danger',
                                                        'late' => 'warning',
                                                        'excused' => 'info'
                                                    ];
                                                    $statusColor = $statusColors[$activity->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $methodIcons = [
                                                        'manual' => 'bi-pencil-square',
                                                        'fingerprint' => 'bi-fingerprint',
                                                        'iris' => 'bi-eye',
                                                        'barcode' => 'bi-qr-code'
                                                    ];
                                                    $icon = $methodIcons[$activity->method ?? 'manual'] ?? 'bi-pencil-square';
                                                @endphp
                                                <small class="text-muted">
                                                    <i class="{{ $icon }} me-1"></i>
                                                    {{ ucfirst($activity->method ?? 'manual') }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

