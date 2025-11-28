@extends('tenant.layouts.app')

@section('title', 'Attendance Reports')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-user-check me-2" style="color: #667eea;"></i>
                            Attendance Reports
                        </h2>
                        <p class="text-muted mb-0">Monitor and analyze student attendance patterns</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn-group">
                            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"
                                        onclick="exportAttendance(); return false;">Export PDF</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="exportAttendanceCsv(); return false;">Export CSV</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="exportAttendanceXlsx(); return false;">Export Excel (XLSX)</a></li>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-calendar-check me-2"></i>Mark Attendance
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('tenant.modules.attendance.index') }}">
                                        <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Classroom Attendance
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.staff-attendance.index') }}">
                                        <i class="fas fa-user-tie me-2 text-success"></i>Staff Attendance
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.exam-attendance.index') }}">
                                        <i class="fas fa-file-alt me-2 text-warning"></i>Exam Attendance
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('tenant.modules.attendance.kiosk') }}">
                                        <i class="fas fa-fingerprint me-2 text-info"></i>Fingerprint/Kiosk Mode
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Present Today</h6>
                                <h3 class="mb-0">{{ $presentToday ?? 0 }}</h3>
                                <small>Marked today</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Absent Today</h6>
                                <h3 class="mb-0">{{ $absentToday ?? 0 }}</h3>
                                <small>Marked today</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Late Arrivals</h6>
                                <h3 class="mb-0">{{ $lateToday ?? 0 }}</h3>
                                <small>Marked today</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Avg Attendance</h6>
                                <h3 class="mb-0">{{ number_format($avgAttendance ?? 0, 2) }}%</h3>
                                <small>In selected range</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-percentage fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.attendance') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ request('date_from', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ request('date_to', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="class_filter" class="form-label">Class</label>
                            <select class="form-select" id="class_filter" name="class">
                                <option value="">All Classes</option>
                                @foreach (curriculum_classes() as $label)
                                    <option value="{{ $label }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.reports.attendance') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Attendance Trends (Daily %) -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Attendance Trend (Daily %)
                        </h5>
                        <small class="text-muted">{{ $dateFrom->toDateString() }} – {{ $dateTo->toDateString() }}</small>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceTrendsChart" height="200" aria-label="Daily attendance percentage"
                            role="img" data-labels='@json($dailyPatternDays ?? [])'
                            data-values='@json($dailyPatternValues ?? [])'></canvas>
                        @if (empty($dailyPatternDays))
                            <div class="text-center text-muted small mt-3">No attendance entries for the selected range.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Class Attendance Comparison -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2 text-success"></i>
                            Class Comparison
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (empty($classAttendance))
                            <p class="text-muted small mb-0">No class attendance data available.</p>
                        @else
                            @foreach ($classAttendance as $class)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-medium">{{ $class['class'] }}</span>
                                        <span
                                            class="fw-bold text-{{ $class['rate'] >= 95 ? 'success' : ($class['rate'] >= 90 ? 'warning' : 'danger') }}">{{ $class['rate'] }}%</span>
                                    </div>
                                    <div class="progress" style="height:8px" aria-hidden="true">
                                        <div class="progress-bar bg-{{ $class['rate'] >= 95 ? 'success' : ($class['rate'] >= 90 ? 'warning' : 'danger') }} attendance-rate-bar"
                                            data-width="{{ $class['rate'] }}" style="width:0%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Students with Poor Attendance -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                            Students Requiring Attention
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (empty($poorAttendance))
                            <p class="text-muted small mb-0">No students flagged under current criteria.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm" aria-label="Students with poor attendance">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Rate</th>
                                            <th>Absences</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($poorAttendance as $student)
                                            <tr>
                                                <td>{{ $student['name'] }}</td>
                                                <td><span class="badge bg-secondary">{{ $student['class'] }}</span></td>
                                                <td><span
                                                        class="badge bg-{{ $student['rate'] >= 80 ? 'warning' : 'danger' }}">{{ $student['rate'] }}%</span>
                                                </td>
                                                <td>{{ $student['absences'] }}</td>
                                                <td><button class="btn btn-sm btn-outline-primary"
                                                        title="Contact Parent"><i class="fas fa-phone"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Daily Attendance Pattern by Class (Bar) -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-day me-2 text-info"></i>
                            Class Attendance Snapshot
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="classAttendanceBar" height="200" aria-label="Class attendance rates"
                            role="img"></canvas>
                        @if (empty($classAttendance))
                            <div class="text-center text-muted small mt-3">No class data available.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Summary -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2 text-primary"></i>
                            Summary (Range Aggregate)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" aria-label="Attendance summary per class">
                                <thead class="table-light">
                                    <tr>
                                        <th>Class</th>
                                        <th>Avg Attendance</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthlyData as $data)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $data['class'] }}</span></td>
                                            <td><span
                                                    class="fw-bold text-{{ $data['avg'] >= 95 ? 'success' : ($data['avg'] >= 90 ? 'warning' : 'danger') }}">{{ $data['avg'] }}%</span>
                                            </td>
                                            <td>
                                                @if ($data['trend'] === 'up')
                                                    <i class="fas fa-arrow-up text-success"></i>
                                                @elseif($data['trend'] === 'down')
                                                    <i class="fas fa-arrow-down text-danger"></i>
                                                @else
                                                    <i class="fas fa-minus text-warning"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted small">No summary data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="exportAttendanceForm" action="{{ route('admin.reports.export-pdf') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="attendance">
        <input type="hidden" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}">
        <input type="hidden" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}">
        <input type="hidden" name="class" value="{{ request('class') }}">
    </form>

    <form id="exportAttendanceCsvForm" action="{{ route('admin.reports.export-excel') }}" method="POST"
        class="d-none">
        @csrf
        <input type="hidden" name="type" value="attendance">
        <input type="hidden" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}">
        <input type="hidden" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}">
        <input type="hidden" name="class" value="{{ request('class') }}">
    </form>

    <form id="exportAttendanceXlsxForm" action="{{ route('admin.reports.export-excel') }}" method="POST"
        class="d-none">
        @csrf
        <input type="hidden" name="type" value="attendance">
        <input type="hidden" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}">
        <input type="hidden" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}">
        <input type="hidden" name="class" value="{{ request('class') }}">
        <input type="hidden" name="output_format" value="excel">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const trendsEl = document.getElementById('attendanceTrendsChart');
        if (trendsEl) {
            const labels = JSON.parse(trendsEl.dataset.labels || '[]');
            const values = JSON.parse(trendsEl.dataset.values || '[]');
            if (labels.length) {
                new Chart(trendsEl.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Attendance %',
                            data: values,
                            borderColor: 'rgba(0,123,255,1)',
                            backgroundColor: 'rgba(0,123,255,0.1)',
                            tension: .35,
                            fill: true,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: false,
                                suggestedMin: Math.max(0, Math.min(...values) - 5),
                                suggestedMax: Math.min(100, Math.max(...values) + 5)
                            }
                        }
                    }
                });
            }
        }
        // Class attendance bar chart
        const classBarEl = document.getElementById('classAttendanceBar');
        if (classBarEl) {
            const cls = @json(collect($classAttendance ?? [])->pluck('class'));
            const rates = @json(collect($classAttendance ?? [])->pluck('rate'));
            if (cls.length) {
                new Chart(classBarEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: cls,
                        datasets: [{
                            label: 'Attendance %',
                            data: rates,
                            backgroundColor: 'rgba(25,135,84,.6)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            }
        }

        function exportAttendance() {
            document.getElementById('exportAttendanceForm').submit();
        }

        function exportAttendanceCsv() {
            document.getElementById('exportAttendanceCsvForm').submit();
        }

        function exportAttendanceXlsx() {
            document.getElementById('exportAttendanceXlsxForm').submit();
        }

        // Animate progress bars
        document.querySelectorAll('.attendance-rate-bar').forEach(el => {
            const w = parseFloat(el.dataset.width || '0');
            requestAnimationFrame(() => el.style.width = (isNaN(w) ? 0 : w) + '%');
        });
    </script>
@endsection
