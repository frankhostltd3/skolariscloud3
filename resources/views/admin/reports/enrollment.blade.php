@extends('tenant.layouts.app')

@section('title', 'Enrollment Reports')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-chart-bar me-2" style="color: #667eea;"></i>
                            Enrollment Reports
                        </h2>
                        <p class="text-muted mb-0">Monitor student enrollment trends and capacity planning</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success" onclick="exportEnrollment()">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </button>
                        <button class="btn btn-outline-secondary" onclick="exportEnrollmentCsv()">
                            <i class="fas fa-file-csv me-2"></i>Export CSV
                        </button>
                        <button class="btn btn-outline-primary" onclick="exportEnrollmentExcel()">
                            <i class="fas fa-file-excel me-2"></i>Export Excel (XLSX)
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>New Enrollment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Overview -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Total Students</h6>
                                <h3 class="mb-0">{{ number_format($totalActive ?? 0) }}</h3>
                                <small>Active</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">New Enrollments</h6>
                                <h3 class="mb-0">{{ number_format($newEnrollments ?? 0) }}</h3>
                                <small>This academic year</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-user-plus fa-2x opacity-75"></i>
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
                                <h6 class="card-title mb-1">Capacity Utilized</h6>
                                <h3 class="mb-0">{{ number_format($capacityUtilized ?? 0, 2) }}%</h3>
                                <small>{{ number_format($totalEnrolled ?? 0) }} / {{ number_format($totalCapacity ?? 0) }}
                                    seats</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-chart-pie fa-2x opacity-75"></i>
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
                                <h6 class="card-title mb-1">Withdrawals</h6>
                                <h3 class="mb-0">{{ number_format($withdrawals ?? 0) }}</h3>
                                <small>This academic year</small>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-user-minus fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.enrollment') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="academic_year_id" class="form-label">Academic Year</label>
                            <select class="form-select" id="academic_year_id" name="academic_year_id">
                                <option value="">All Years</option>
                                @foreach ($academicYears ?? [] as $ay)
                                    <option value="{{ $ay->id }}"
                                        {{ (string) request('academic_year_id') === (string) $ay->id ? 'selected' : '' }}>
                                        {{ $ay->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="grade_level" class="form-label">Class</label>
                            <select class="form-select" id="grade_level" name="grade_level">
                                <option value="" {{ request('grade_level') == '' ? 'selected' : '' }}>All Classes
                                </option>
                                @foreach (curriculum_classes() as $label)
                                    <option value="{{ $label }}"
                                        {{ request('grade_level') == $label ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="enrollment_status" class="form-label">Status</label>
                            <select class="form-select" id="enrollment_status" name="enrollment_status">
                                <option value="" {{ request('enrollment_status') == '' ? 'selected' : '' }}>All
                                    Status</option>
                                <option value="active" {{ request('enrollment_status') == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="pending" {{ request('enrollment_status') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="withdrawn"
                                    {{ request('enrollment_status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                <option value="graduated"
                                    {{ request('enrollment_status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.reports.enrollment') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Enrollment Trends -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Enrollment Trends (Last 5 Years)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="enrollmentTrendsChart" width="400" height="200"
                            aria-label="New enrollments by year" role="img"
                            data-labels='@json($years ?? [])'
                            data-values='@json($yearTotals ?? [])'></canvas>
                        @if (empty($years))
                            <div class="text-muted small mt-3">No historical enrollment data.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grade Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2 text-success"></i>
                            Grade Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="gradeDistributionChart" width="400" height="300"
                            aria-label="Active students per class" role="img"
                            data-labels='@json($gradeLabels ?? [])'
                            data-values='@json($gradeCounts ?? [])'></canvas>
                        @if (empty($gradeLabels))
                            <div class="text-muted small mt-3">No active class distribution data.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Capacity Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2 text-info"></i>
                            Capacity Analysis by Grade
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse(($capacityData ?? []) as $grade)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-medium">{{ $grade['grade'] }}</span>
                                    <div class="text-end">
                                        <span class="fw-bold">{{ $grade['enrolled'] }}/{{ $grade['capacity'] }}</span>
                                        <small class="text-muted d-block">{{ $grade['utilization'] }}% utilized</small>
                                    </div>
                                </div>
                                <div class="progress mb-1" style="height: 12px;">
                                    @php
                                        $util = (float) ($grade['utilization'] ?? 0);
                                        $color = $util > 90 ? '#dc3545' : ($util > 80 ? '#ffc107' : '#28a745');
                                    @endphp
                                    <div class="progress-bar util-bar" data-util="{{ $util }}"
                                        data-color="{{ $color }}" style="width:0%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small
                                        class="text-{{ $grade['utilization'] > 90 ? 'danger' : ($grade['utilization'] > 80 ? 'warning' : 'success') }}">
                                        {{ $grade['utilization'] }}% capacity
                                    </small>
                                    <small class="text-muted">{{ $grade['capacity'] - $grade['enrolled'] }} seats
                                        available</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No capacity data available.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Monthly Enrollment Activity -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2 text-warning"></i>
                            Monthly Enrollment Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyActivityChart" width="400" height="250"
                            aria-label="Monthly enrollment vs withdrawals" role="img"
                            data-labels='@json($monthLabels ?? [])' data-new='@json($monthlyNew ?? [])'
                            data-withd='@json($monthlyWithdrawals ?? [])'></canvas>
                        @if (empty($monthLabels))
                            <div class="text-muted small mt-3">No monthly activity available.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Demographics -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Student Demographics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Gender Distribution</h6>
                                <canvas id="genderChart" width="300" height="200" aria-label="Gender distribution"
                                    role="img" data-labels='@json($genderLabels ?? [])'
                                    data-values='@json($genderCounts ?? [])'></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Age Distribution</h6>
                                <canvas id="ageChart" width="300" height="200" aria-label="Age distribution"
                                    role="img" data-labels='@json($ageLabels ?? [])'
                                    data-values='@json($ageCounts ?? [])'></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2 text-success"></i>
                            Recent Enrollments
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse(($recentEnrollments ?? []) as $enrollment)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                                <div>
                                    <h6 class="mb-1">{{ $enrollment['name'] }}</h6>
                                    <small class="text-muted">{{ $enrollment['grade'] }}</small>
                                    <div class="mt-1">
                                        <small class="text-muted">{{ $enrollment['date'] }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span
                                        class="badge bg-{{ $enrollment['status'] == 'Active' ? 'success' : 'warning' }}">
                                        {{ $enrollment['status'] }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No recent enrollments found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Enrollment Forecast -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-area me-2 text-info"></i>
                            Enrollment Forecast (Next 3 Years)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="enrollmentForecastChart" width="400" height="150"
                            aria-label="Enrollment forecast" role="img" data-labels='@json($years ?? [])'
                            data-hist='@json($yearTotals ?? [])' data-proj='@json($forecastProj ?? [])'></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="exportEnrollmentForm" action="{{ route('admin.reports.export-pdf') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="enrollment">
        <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
        <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
        <input type="hidden" name="enrollment_status" value="{{ request('enrollment_status') }}">
    </form>

    <form id="exportEnrollmentCsvForm" action="{{ route('admin.reports.export-excel') }}" method="POST"
        class="d-none">
        @csrf
        <input type="hidden" name="type" value="enrollment">
        <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
        <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
        <input type="hidden" name="enrollment_status" value="{{ request('enrollment_status') }}">
    </form>

    <form id="exportEnrollmentExcelForm" action="{{ route('admin.reports.export-excel') }}" method="POST"
        class="d-none">
        @csrf
        <input type="hidden" name="type" value="enrollment">
        <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
        <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
        <input type="hidden" name="enrollment_status" value="{{ request('enrollment_status') }}">
        <input type="hidden" name="output_format" value="excel">
        <input type="hidden" name="format" value="xlsx">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Enrollment Trends Chart (live)
        const trendsEl = document.getElementById('enrollmentTrendsChart');
        if (trendsEl) {
            const trendsCtx = trendsEl.getContext('2d');
            const trendsLabels = JSON.parse(trendsEl.dataset.labels || '[]');
            const trendsValues = JSON.parse(trendsEl.dataset.values || '[]');
            if (trendsLabels.length) {
                const minY = Math.max(0, Math.min.apply(null, trendsValues) - Math.round(Math.min.apply(null,
                    trendsValues) * 0.1));
                new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: trendsLabels,
                        datasets: [{
                            label: 'New Enrollments',
                            data: trendsValues,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,.12)',
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
                                suggestedMin: minY
                            }
                        }
                    }
                });
            }
        }
        // Replace existing gradeEl logic with guard
        const gradeEl = document.getElementById('gradeDistributionChart');
        if (gradeEl) {
            const gradeCtx = gradeEl.getContext('2d');
            const gradeLabels = JSON.parse(gradeEl.dataset.labels || '[]');
            const gradeValues = JSON.parse(gradeEl.dataset.values || '[]');
            if (gradeLabels.length) {
                new Chart(gradeCtx, {
                    type: 'pie',
                    data: {
                        labels: gradeLabels,
                        datasets: [{
                            data: gradeValues,
                            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6c757d', '#dc3545',
                                '#6610f2', '#20c997'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
        // Monthly Activity
        const activityEl = document.getElementById('monthlyActivityChart');
        if (activityEl) {
            const activityCtx = activityEl.getContext('2d');
            const activityLabels = JSON.parse(activityEl.dataset.labels || '[]');
            const activityNew = JSON.parse(activityEl.dataset.new || '[]');
            const activityWithd = JSON.parse(activityEl.dataset.withd || '[]');
            if (activityLabels.length) {
                new Chart(activityCtx, {
                    type: 'bar',
                    data: {
                        labels: activityLabels,
                        datasets: [{
                            label: 'New',
                            data: activityNew,
                            backgroundColor: 'rgba(25,135,84,.65)'
                        }, {
                            label: 'Withdrawals',
                            data: activityWithd,
                            backgroundColor: 'rgba(220,53,69,.65)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
        // Gender
        const genderEl = document.getElementById('genderChart');
        if (genderEl) {
            const genderCtx = genderEl.getContext('2d');
            const gLabels = JSON.parse(genderEl.dataset.labels || '[]');
            const gVals = JSON.parse(genderEl.dataset.values || '[]');
            if (gLabels.length) {
                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: gLabels,
                        datasets: [{
                            data: gVals,
                            backgroundColor: ['#0d6efd', '#dc3545', '#6c757d']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
        // Age
        const ageEl = document.getElementById('ageChart');
        if (ageEl) {
            const ageCtx = ageEl.getContext('2d');
            const aLabels = JSON.parse(ageEl.dataset.labels || '[]');
            const aVals = JSON.parse(ageEl.dataset.values || '[]');
            if (aLabels.length) {
                new Chart(ageCtx, {
                    type: 'bar',
                    data: {
                        labels: aLabels,
                        datasets: [{
                            label: 'Students',
                            data: aVals,
                            backgroundColor: 'rgba(255,193,7,.7)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
        // Forecast
        const forecastEl = document.getElementById('enrollmentForecastChart');
        if (forecastEl) {
            const fCtx = forecastEl.getContext('2d');
            const fLabels = JSON.parse(forecastEl.dataset.labels || '[]');
            const fHist = JSON.parse(forecastEl.dataset.hist || '[]');
            const fProj = JSON.parse(forecastEl.dataset.proj || '[]');
            if (fLabels.length) {
                new Chart(fCtx, {
                    type: 'line',
                    data: {
                        labels: fLabels,
                        datasets: [{
                            label: 'Historical',
                            data: fHist,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,.1)',
                            tension: .35,
                            fill: true
                        }, {
                            label: 'Projected',
                            data: fProj,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25,135,84,.1)',
                            tension: .35,
                            borderDash: [5, 5],
                            spanGaps: true
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });
            }
        }
        // Animate utilization bars
        document.querySelectorAll('.util-bar').forEach(el => {
            const w = parseFloat(el.dataset.util || '0');
            requestAnimationFrame(() => el.style.width = (isNaN(w) ? 0 : w) + '%');
            el.style.backgroundColor = el.dataset.color || '#0d6efd';
        });

        function exportEnrollment() {
            document.getElementById('exportEnrollmentForm').submit();
        }

        function exportEnrollmentCsv() {
            document.getElementById('exportEnrollmentCsvForm').submit();
        }

        function exportEnrollmentExcel() {
            document.getElementById('exportEnrollmentExcelForm').submit();
        }
    </script>
@endsection
