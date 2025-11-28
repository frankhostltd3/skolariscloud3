@extends('layouts.tenant.app')

@section('title', 'Assignment Analytics - ' . $exercise->title)

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Assignment Analytics
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.teacher.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('tenant.teacher.classroom.exercises.index') }}">Assignments</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}">{{ Str::limit($exercise->title, 30) }}</a>
                        </li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.teacher.classroom.exercises.show', $exercise) }}"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Assignment
                </a>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item"
                                href="{{ route('tenant.teacher.classroom.exercises.export', ['exercise' => $exercise, 'format' => 'csv']) }}">
                                <i class="bi bi-file-earmark-spreadsheet me-2"></i>CSV Format
                            </a></li>
                        <li><a class="dropdown-item"
                                href="{{ route('tenant.teacher.classroom.exercises.export', ['exercise' => $exercise, 'format' => 'pdf']) }}">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF Report
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Assignment Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-2">{{ $exercise->title }}</h5>
                        <p class="text-muted mb-0">
                            <span class="badge bg-info me-2">{{ $exercise->class->name }}</span>
                            <span class="badge bg-secondary me-2">{{ $exercise->subject->name }}</span>
                            <span class="text-muted">Due: {{ $exercise->due_date->format('M d, Y h:i A') }}</span>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="h2 mb-0 text-primary">
                            {{ number_format($analytics['overview']['completion_rate'], 1) }}%</div>
                        <small class="text-muted">Completion Rate</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card h-100 border-primary">
                    <div class="card-body text-center">
                        <div class="display-6 text-primary mb-2">{{ $analytics['overview']['total_students'] }}</div>
                        <p class="text-muted mb-0">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-success">
                    <div class="card-body text-center">
                        <div class="display-6 text-success mb-2">{{ $analytics['overview']['submitted'] }}</div>
                        <p class="text-muted mb-0">Submitted</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-info">
                    <div class="card-body text-center">
                        <div class="display-6 text-info mb-2">{{ $analytics['overview']['graded'] }}</div>
                        <p class="text-muted mb-0">Graded</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-warning">
                    <div class="card-body text-center">
                        <div class="display-6 text-warning mb-2">{{ $analytics['overview']['pending'] }}</div>
                        <p class="text-muted mb-0">Pending</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Score Analytics -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Score Distribution</h5>
                    </div>
                    <div class="card-body">
                        @if ($analytics['scores']['graded_count'] > 0)
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="text-primary">{{ number_format($analytics['scores']['average'], 1) }}%
                                        </h3>
                                        <small class="text-muted">Average Score</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="text-success">{{ number_format($analytics['scores']['highest'], 1) }}%
                                        </h3>
                                        <small class="text-muted">Highest Score</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="text-danger">{{ number_format($analytics['scores']['lowest'], 1) }}%
                                        </h3>
                                        <small class="text-muted">Lowest Score</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h3 class="text-info">{{ number_format($analytics['scores']['median'], 1) }}%</h3>
                                        <small class="text-muted">Median Score</small>
                                    </div>
                                </div>
                            </div>

                            <canvas id="scoreChart" height="80"></canvas>

                            <div class="mt-4">
                                <h6>Performance Bands</h6>
                                <div class="progress mb-2" style="height: 30px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $analytics['distribution']['excellent_percent'] }}%">
                                        {{ $analytics['distribution']['excellent'] }} (90-100%)
                                    </div>
                                    <div class="progress-bar bg-info"
                                        style="width: {{ $analytics['distribution']['good_percent'] }}%">
                                        {{ $analytics['distribution']['good'] }} (75-89%)
                                    </div>
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $analytics['distribution']['average_percent'] }}%">
                                        {{ $analytics['distribution']['average'] }} (60-74%)
                                    </div>
                                    <div class="progress-bar bg-danger"
                                        style="width: {{ $analytics['distribution']['below_average_percent'] }}%">
                                        {{ $analytics['distribution']['below_average'] }} (<60%) </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-graph-up display-1"></i>
                                    <p class="mt-3">No graded submissions yet</p>
                                </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Time Analytics -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Submission Timing</h5>
                    </div>
                    <div class="card-body">
                        @if ($analytics['overview']['submitted'] > 0)
                            <div class="mb-4">
                                <h6 class="text-muted">Average Submission Time</h6>
                                <p class="h4">{{ number_format($analytics['time']['avg_days_before_due'], 1) }} days
                                    before due</p>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted">Submission Status</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>On Time</span>
                                    <span class="badge bg-success">{{ $analytics['time']['on_time'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Late</span>
                                    <span class="badge bg-warning">{{ $analytics['time']['late'] }}</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $analytics['time']['on_time_percent'] }}%"></div>
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $analytics['time']['late_percent'] }}%"></div>
                                </div>
                            </div>

                            <canvas id="timeChart" height="150"></canvas>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history display-1"></i>
                                <p class="mt-3">No submissions yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Details -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Submission Details</h5>
            </div>
            <div class="card-body">
                @if ($submissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Grade</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $submission)
                                    <tr>
                                        <td>{{ $submission->student->name }}</td>
                                        <td>
                                            @if ($submission->submitted_at)
                                                {{ $submission->submitted_at->format('M d, Y') }}
                                                <br><small
                                                    class="text-muted">{{ $submission->submitted_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">Not submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($submission->is_late)
                                                <span class="badge bg-warning">Late</span>
                                            @elseif($submission->submitted_at)
                                                <span class="badge bg-success">On Time</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($submission->score !== null)
                                                {{ $submission->score }} / {{ $exercise->max_score }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($submission->score !== null)
                                                @php
                                                    $percentage = ($submission->score / $exercise->max_score) * 100;
                                                @endphp
                                                <strong class="text-primary">{{ number_format($percentage, 1) }}%</strong>
                                            @else
                                                <span class="text-muted">Not graded</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($submission->score !== null)
                                                @php
                                                    $percentage = ($submission->score / $exercise->max_score) * 100;
                                                    $color =
                                                        $percentage >= 90
                                                            ? 'success'
                                                            : ($percentage >= 75
                                                                ? 'info'
                                                                : ($percentage >= 60
                                                                    ? 'warning'
                                                                    : 'danger'));
                                                    $label =
                                                        $percentage >= 90
                                                            ? 'Excellent'
                                                            : ($percentage >= 75
                                                                ? 'Good'
                                                                : ($percentage >= 60
                                                                    ? 'Average'
                                                                    : 'Below Average'));
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $label }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-1"></i>
                        <p class="mt-3">No submissions yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if ($analytics['scores']['graded_count'] > 0)
                    // Score Distribution Chart
                    const scoreCtx = document.getElementById('scoreChart');
                    if (scoreCtx) {
                        new Chart(scoreCtx, {
                            type: 'bar',
                            data: {
                                labels: ['Excellent\n(90-100%)', 'Good\n(75-89%)', 'Average\n(60-74%)',
                                    'Below Avg\n(<60%)'
                                ],
                                datasets: [{
                                    label: 'Number of Students',
                                    data: [
                                        {{ $analytics['distribution']['excellent'] }},
                                        {{ $analytics['distribution']['good'] }},
                                        {{ $analytics['distribution']['average'] }},
                                        {{ $analytics['distribution']['below_average'] }}
                                    ],
                                    backgroundColor: [
                                        'rgba(25, 135, 84, 0.7)',
                                        'rgba(13, 202, 240, 0.7)',
                                        'rgba(255, 193, 7, 0.7)',
                                        'rgba(220, 53, 69, 0.7)'
                                    ],
                                    borderColor: [
                                        'rgb(25, 135, 84)',
                                        'rgb(13, 202, 240)',
                                        'rgb(255, 193, 7)',
                                        'rgb(220, 53, 69)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    }
                @endif

                @if ($analytics['overview']['submitted'] > 0) // Time Distribution Chart
    const timeCtx = document.getElementById('timeChart');
    if (timeCtx) {
        new Chart(timeCtx, {
            type: 'doughnut',
            data: {
                labels: ['On Time', 'Late'],
                datasets: [{
                    data: [
                        {{ $analytics['time']['on_time'] }},
                        {{ $analytics['time']['late'] }}
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(255, 193, 7)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } @endif
            });
        </script>
    @endpush
@endsection
