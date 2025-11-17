@extends('tenant.layouts.app')

@section('title', 'Academic Reports')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-1">
                            <i class="fas fa-chart-line me-2" style="color: #667eea;"></i>
                            Academic Reports
                        </h2>
                        <p class="text-muted mb-0">Generate and view academic performance reports</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn-group">
                            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="exportReport(); return false;">Export
                                        PDF</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="exportReportCsv(); return false;">Export CSV</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="exportReportXlsx(); return false;">Export Excel (XLSX)</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                            <i class="fas fa-plus me-2"></i>Generate New Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">Overall GPA</h6>
                                <h3 class="mb-0">{{ number_format($overallGpa ?? 0, 2) }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-star fa-2x opacity-75"></i>
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
                                <h6 class="card-title mb-1">Pass Rate</h6>
                                <h3 class="mb-0">{{ number_format($passRate ?? 0, 2) }}%</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
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
                                <h6 class="card-title mb-1">Honor Roll</h6>
                                <h3 class="mb-0">{{ $honorRollCount ?? 0 }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-medal fa-2x opacity-75"></i>
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
                                <h6 class="card-title mb-1">At Risk</h6>
                                <h3 class="mb-0">{{ $atRiskCount ?? 0 }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.academic') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <select class="form-select" id="academic_year" name="academic_year">
                                @php $ay = request('academic_year'); @endphp
                                <option value="">All</option>
                                <option value="2024-2025" {{ $ay === '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                <option value="2023-2024" {{ $ay === '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                                <option value="2022-2023" {{ $ay === '2022-2023' ? 'selected' : '' }}>2022-2023</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester">
                                @php $sem = request('semester'); @endphp
                                <option value="">All Semesters</option>
                                <option value="fall" {{ $sem === 'fall' ? 'selected' : '' }}>Fall Semester</option>
                                <option value="spring" {{ $sem === 'spring' ? 'selected' : '' }}>Spring Semester</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="grade_level" class="form-label">Class</label>
                            <select class="form-select" id="grade_level" name="grade_level">
                                <option value="">All Classes</option>
                                @foreach ($curriculumClasses as $label)
                                    <option value="{{ $label }}"
                                        {{ request('grade_level') === $label ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.reports.academic') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Grade Distribution Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            Grade Distribution by Subject
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="gradeDistributionChart" width="400" height="200"
                            aria-label="Stacked grade distribution by subject" role="img"
                            data-subject-labels="{{ e(json_encode($subjectLabels ?? [])) }}"
                            data-grade-letters="{{ e(json_encode($gradeLetters ?? [])) }}"
                            data-subject-datasets="{{ e(json_encode($subjectDatasets ?? new stdClass())) }}"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy me-2 text-warning"></i>
                            Top Performers
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse(($topPerformers ?? []) as $index => $student)
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-warning text-dark me-3">{{ $index + 1 }}</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $student['name'] }}</h6>
                                    <small class="text-muted">{{ $student['class'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($student['gpa'], 2) }}</span>
                                    <small class="text-muted d-block">GPA</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No data available for the selected filters.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Subject Performance -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-book me-2 text-success"></i>
                            Subject Performance Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (empty($subjectPerformance))
                            <p class="text-muted mb-0 small">No subject performance data found for the selected filters.
                            </p>
                        @else
                            @foreach ($subjectPerformance as $subject)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-medium">{{ $subject['name'] }}</span>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $subject['average'] }}%</span>
                                            @if ($subject['trend'] == 'up')
                                                <i class="fas fa-arrow-up text-success" aria-label="Improving"></i>
                                            @elseif($subject['trend'] == 'down')
                                                <i class="fas fa-arrow-down text-danger" aria-label="Declining"></i>
                                            @else
                                                <i class="fas fa-minus text-warning" aria-label="Stable"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 8px;" aria-hidden="true">
                                        <div class="progress-bar bg-success subject-avg-bar"
                                            data-width="{{ $subject['average'] }}" style="width:0%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Class Performance Comparison -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2 text-info"></i>
                            Class Performance Comparison
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="classPerformanceChart" width="400" height="300"
                            aria-label="Average performance by class" role="img"
                            data-class-labels="{{ e(json_encode($classLabels ?? [])) }}"
                            data-class-averages="{{ e(json_encode($classAverages ?? [])) }}"></canvas>
                    </div>
                </div>
            </div>

            <!-- Academic Trends -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Academic Performance Trends
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" width="400" height="150" aria-label="GPA trend over months"
                            role="img" data-months="{{ e(json_encode($months ?? [])) }}"
                            data-gpa-series="{{ e(json_encode($gpaSeries ?? [])) }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1" aria-labelledby="generateReportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.reports.export-pdf') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateReportModalLabel">
                            <i class="fas fa-plus me-2"></i>Generate Academic Report
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select class="form-select" id="report_type" name="report_type" required>
                                    <option value="">Select Report Type</option>
                                    <option value="grade_summary">Grade Summary</option>
                                    <option value="student_progress">Student Progress</option>
                                    <option value="subject_analysis">Subject Analysis</option>
                                    <option value="class_comparison">Class Comparison</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="report_period" class="form-label">Report Period</label>
                                <select class="form-select" id="report_period" name="report_period" required>
                                    <option value="">Select Period</option>
                                    <option value="current_semester">Current Semester</option>
                                    <option value="academic_year">Full Academic Year</option>
                                    <option value="custom">Custom Date Range</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="target_class" class="form-label">Target Class</label>
                                <select class="form-select" id="target_class" name="target_class">
                                    <option value="">All Classes</option>
                                    @foreach ($curriculumClasses as $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="output_format" class="form-label">Output Format</label>
                                <select class="form-select" id="output_format" name="output_format" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3" id="customDateRange" style="display: none;">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date">
                                    </div>
                                    <div class="col-6">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="report_description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="report_description" name="description" rows="3"
                                    placeholder="Add any specific notes or requirements for this report..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-cog me-2"></i>Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="exportAcademicForm" action="{{ route('admin.reports.export-pdf') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="academic">
        <input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
        <input type="hidden" name="semester" value="{{ request('semester') }}">
        <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
    </form>

    <form id="exportAcademicCsvForm" action="{{ route('admin.reports.export-excel') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="academic">
        <input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
        <input type="hidden" name="semester" value="{{ request('semester') }}">
        <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
        <input type="hidden" name="output_format" value="csv">
        <input type="hidden" name="report_type" value="grade_summary">
        <input type="hidden" name="report_period" value="current_semester">
    </form>

    <form id="exportAcademicXlsxForm" action="{{ route('admin.reports.export-excel') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="type" value="academic">
        <input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
        <input type="hidden" name="semester" value="{{ request('semester') }}">
        <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
        <input type="hidden" name="output_format" value="excel">
        <input type="hidden" name="report_type" value="grade_summary">
        <input type="hidden" name="report_period" value="current_semester">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grade Distribution Chart
        const gradeCanvas = document.getElementById('gradeDistributionChart');
        const gradeCtx = gradeCanvas.getContext('2d');
        const subjectLabels = JSON.parse(gradeCanvas.dataset.subjectLabels || '[]');
        const gradeLetters = JSON.parse(gradeCanvas.dataset.gradeLetters || '[]');
        const subjectDatasets = JSON.parse(gradeCanvas.dataset.subjectDatasets || '{}');
        const palette = {
            'A+': 'rgba(54, 162, 235, 0.8)',
            'A': 'rgba(75, 192, 192, 0.8)',
            'A-': 'rgba(153, 102, 255, 0.8)',
            'B+': 'rgba(255, 159, 64, 0.8)',
            'B': 'rgba(0, 123, 255, 0.8)',
            'B-': 'rgba(40, 167, 69, 0.8)',
            'C+': 'rgba(255, 205, 86, 0.8)',
            'C': 'rgba(108, 117, 125, 0.8)',
            'C-': 'rgba(201, 203, 207, 0.8)',
            'D+': 'rgba(255, 99, 132, 0.8)',
            'D': 'rgba(220, 53, 69, 0.8)',
            'F': 'rgba(123, 88, 77, 0.8)'
        };
        const datasets = (gradeLetters || []).map(gl => ({
            label: gl,
            data: (subjectDatasets[gl] || []),
            backgroundColor: palette[gl] || 'rgba(0,0,0,0.2)'
        }));
        const gradeChart = new Chart(gradeCtx, {
            type: 'bar',
            data: {
                labels: subjectLabels,
                datasets
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });

        // Class Performance Chart
        const classCanvas = document.getElementById('classPerformanceChart');
        const classCtx = classCanvas.getContext('2d');
        const classLabels = JSON.parse(classCanvas.dataset.classLabels || '[]');
        const classAverages = JSON.parse(classCanvas.dataset.classAverages || '[]');
        const classChart = new Chart(classCtx, {
            type: 'doughnut',
            data: {
                labels: classLabels,
                datasets: [{
                    data: classAverages,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
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

        // Trends Chart
        const trendsCanvas = document.getElementById('trendsChart');
        const trendsCtx = trendsCanvas.getContext('2d');
        const months = JSON.parse(trendsCanvas.dataset.months || '[]');
        const gpaSeries = JSON.parse(trendsCanvas.dataset.gpaSeries || '[]');
        const trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Overall GPA',
                    data: gpaSeries,
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 3.0,
                        max: 4.0
                    }
                }
            }
        });

        // Show/hide custom date range
        document.getElementById('report_period').addEventListener('change', function() {
            const customRange = document.getElementById('customDateRange');
            if (this.value === 'custom') {
                customRange.style.display = 'block';
            } else {
                customRange.style.display = 'none';
            }
        });

        function exportReport() {
            document.getElementById('exportAcademicForm').submit();
        }

        function exportReportCsv() {
            document.getElementById('exportAcademicCsvForm').submit();
        }

        function exportReportXlsx() {
            document.getElementById('exportAcademicXlsxForm').submit();
        }

        // Guard if no data
        if (!subjectLabels.length) {
            gradeCanvas.replaceWith(Object.assign(document.createElement('div'), {
                className: 'text-muted small',
                innerText: 'No grade data available.'
            }));
        }
        if (!classLabels.length) {
            classCanvas.replaceWith(Object.assign(document.createElement('div'), {
                className: 'text-muted small',
                innerText: 'No class performance data.'
            }));
        }
        if (!months.length) {
            trendsCanvas.replaceWith(Object.assign(document.createElement('div'), {
                className: 'text-muted small',
                innerText: 'No trend data.'
            }));
        }

        // Animation for subject performance bars
        document.querySelectorAll('.subject-avg-bar').forEach(function(el) {
            const w = parseFloat(el.dataset.width || '0');
            requestAnimationFrame(() => {
                el.style.width = (isNaN(w) ? 0 : w) + "%";
            });
        });
    </script>
@endsection
