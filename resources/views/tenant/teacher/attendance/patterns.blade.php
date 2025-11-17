@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Attendance Patterns Analysis</h1>
            <p class="text-muted mb-0">Analyze attendance trends and identify patterns</p>
        </div>
        <a href="{{ route('tenant.teacher.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.teacher.attendance.patterns') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="class_id" class="form-label">Class</label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Analyze
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($patterns)
        <!-- Overview Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar3 text-primary" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $patterns['overview']['total_days'] }}</h3>
                        <p class="text-muted mb-0">Total Days</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-person-check text-success" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $patterns['overview']['present_count'] }}</h3>
                        <p class="text-muted mb-0">Total Present</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <i class="bi bi-person-x text-danger" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $patterns['overview']['absent_count'] }}</h3>
                        <p class="text-muted mb-0">Total Absent</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-clock text-warning" style="font-size: 2.5rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $patterns['overview']['late_count'] }}</h3>
                        <p class="text-muted mb-0">Total Late</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Percentage -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2">Overall Attendance Rate</h5>
                        <div class="progress" style="height: 30px;">
                            @php
                                $percentage = $patterns['overview']['overall_percentage'];
                                $colorClass = $percentage >= 90 ? 'success' : ($percentage >= 75 ? 'warning' : 'danger');
                            @endphp
                            <div class="progress-bar bg-{{ $colorClass }}" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%;"
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <strong>{{ $percentage }}%</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <h2 class="mb-0 text-{{ $colorClass }}">{{ $percentage }}%</h2>
                        <p class="text-muted mb-0">Average Attendance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student-wise Analysis -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Student-wise Analysis</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th class="text-center">Total Days</th>
                                <th class="text-center">Present</th>
                                <th class="text-center">Absent</th>
                                <th class="text-center">Late</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patterns['student_stats'] as $stat)
                                @php
                                    $statusColors = [
                                        'excellent' => 'success',
                                        'good' => 'primary',
                                        'average' => 'warning',
                                        'poor' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$stat['status']] ?? 'secondary';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($stat['student'] && $stat['student']->photo)
                                                <img src="{{ asset('storage/' . $stat['student']->photo) }}" 
                                                     alt="{{ $stat['student']->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="32" height="32">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px; font-size: 14px;">
                                                    {{ $stat['student'] ? strtoupper(substr($stat['student']->name, 0, 1)) : '?' }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $stat['student']->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $stat['total'] }}</td>
                                    <td class="text-center"><span class="badge bg-success">{{ $stat['present'] }}</span></td>
                                    <td class="text-center"><span class="badge bg-danger">{{ $stat['absent'] }}</span></td>
                                    <td class="text-center"><span class="badge bg-warning">{{ $stat['late'] }}</span></td>
                                    <td class="text-center">
                                        <strong class="text-{{ $statusColor }}">{{ $stat['percentage'] }}%</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusColor }}">{{ ucfirst($stat['status']) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Day of Week Pattern -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Day of Week Pattern</h6>
                    </div>
                    <div class="card-body">
                        @if($patterns['day_of_week_pattern']->count() > 0)
                            <canvas id="dayOfWeekChart" height="250"></canvas>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-graph-down" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">No data available for this period</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Trend</h6>
                    </div>
                    <div class="card-body">
                        @if($patterns['monthly_trend']->count() > 0)
                            <canvas id="monthlyTrendChart" height="250"></canvas>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-graph-down" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">No data available for this period</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif($selectedClass)
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            No attendance data found for the selected period. Please adjust the date range and try again.
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Please select a class and date range to view attendance patterns.
        </div>
    @endif
</div>

@if($patterns)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Day of Week Pattern Chart
    @if($patterns['day_of_week_pattern']->count() > 0)
    const dayLabels = @json($patterns['day_of_week_pattern']->pluck('day_name'));
    const dayPresent = @json($patterns['day_of_week_pattern']->pluck('present'));
    const dayAbsent = @json($patterns['day_of_week_pattern']->pluck('absent'));
    
    new Chart(document.getElementById('dayOfWeekChart'), {
        type: 'bar',
        data: {
            labels: dayLabels,
            datasets: [
                {
                    label: 'Present',
                    data: dayPresent,
                    backgroundColor: '#198754',
                    borderColor: '#146c43',
                    borderWidth: 1
                },
                {
                    label: 'Absent',
                    data: dayAbsent,
                    backgroundColor: '#dc3545',
                    borderColor: '#b02a37',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif

    // Monthly Trend Chart
    @if($patterns['monthly_trend']->count() > 0)
    const monthLabels = @json($patterns['monthly_trend']->pluck('month'));
    const monthPresent = @json($patterns['monthly_trend']->pluck('present'));
    const monthAbsent = @json($patterns['monthly_trend']->pluck('absent'));
    
    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Present',
                    data: monthPresent,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Absent',
                    data: monthAbsent,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
</script>
@endpush
@endif
@endsection

