@extends('tenant.layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Reports & Analytics Dashboard</h1>
                    <div>
                        <a href="{{ route('admin.reports.export-pdf', ['type' => 'financial']) }}"
                            class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-file-pdf me-1"></i>PDF Summary</a>
                        <a href="{{ route('admin.reports.export-excel', ['type' => 'financial', 'format' => 'excel']) }}"
                            class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-excel me-1"></i>Excel Summary</a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show"><i
                            class="bi bi-check-circle me-1"></i>{{ session('success') }}<button class="btn-close"
                            data-bs-dismiss="alert"></button></div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show"><i
                            class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}<button class="btn-close"
                            data-bs-dismiss="alert"></button></div>
                @endif

                {{-- KPI CARDS --}}
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-2">
                        <div class="card shadow-sm border-start border-primary border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-xs text-uppercase fw-bold text-primary mb-1">Students</div>
                                        <div class="h5 mb-0 fw-semibold">{{ number_format($kpis['totalStudents'] ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="text-primary"><i class="bi bi-mortarboard fs-3"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="card shadow-sm border-start border-success border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-uppercase fw-bold text-success mb-1">Teachers</div>
                                        <div class="h5 mb-0 fw-semibold">{{ number_format($kpis['totalTeachers'] ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="text-success"><i class="bi bi-person-badge fs-3"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="card shadow-sm border-start border-info border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-uppercase fw-bold text-info mb-1">Classes</div>
                                        <div class="h5 mb-0 fw-semibold">{{ number_format($kpis['activeClasses'] ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="text-info"><i class="bi bi-building fs-3"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="card shadow-sm border-start border-warning border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-uppercase fw-bold text-warning mb-1">30d Attendance</div>
                                        <div class="h5 mb-0 fw-semibold">
                                            {{ number_format($kpis['avgAttendance30'] ?? 0, 1) }}%</div>
                                    </div>
                                    <div class="text-warning"><i class="bi bi-person-check fs-3"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="card shadow-sm border-start border-danger border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-uppercase fw-bold text-danger mb-1">Outstanding Fees</div>
                                        <div class="h5 mb-0 fw-semibold">
                                            {{ number_format($kpis['outstandingFees'] ?? 0, 2) }}</div>
                                    </div>
                                    <div class="text-danger"><i class="bi bi-exclamation-circle fs-3"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-2">
                        <div class="card shadow-sm border-start border-secondary border-4 h-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs text-uppercase fw-bold text-secondary mb-1">Revenue 30d</div>
                                        <div class="h5 mb-0 fw-semibold">{{ number_format($kpis['revenue30'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="text-secondary"><i class="bi bi-cash-coin fs-3"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trend & Generation Row --}}
                <div class="row g-4 mb-4">
                    <div class="col-lg-7">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-graph-up me-1"></i>Revenue vs Expenses
                                    (Last 6 Months)</h6>
                                <small class="text-muted">Auto-cached 15m</small>
                            </div>
                            <div class="card-body">
                                <canvas id="revExpChart" height="160"></canvas>
                                @if (empty($trend['months']))
                                    <div class="text-center text-muted small mt-3">No financial activity recorded.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card shadow-sm h-100">
                            <div class="card-header">
                                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-lightning-charge me-1"></i>On‑Demand
                                    Report Generation</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.reports.generate') }}" class="row g-3"
                                    id="onDemandReportForm">
                                    @csrf
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Report Type</label>
                                        <select name="type" class="form-select" required>
                                            <option value="academic_performance">Academic Performance</option>
                                            <option value="attendance_summary">Attendance Summary</option>
                                            <option value="financial_summary">Financial Summary</option>
                                            <option value="enrollment_summary">Enrollment Summary</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">From</label>
                                        <input type="date" name="date_from" class="form-control"
                                            value="{{ now()->subMonth()->toDateString() }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">To</label>
                                        <input type="date" name="date_to" class="form-control"
                                            value="{{ now()->toDateString() }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Format</label>
                                        <select name="format" class="form-select" required>
                                            <option value="csv">CSV</option>
                                            <option value="xlsx">Excel (XLSX)</option>
                                            <option value="json">JSON</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex align-items-center">
                                        <div class="form-check me-auto">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="asyncFlag" name="async">
                                            <label class="form-check-label small" for="asyncFlag">Generate asynchronously
                                                (recommended for large ranges)</label>
                                        </div>
                                        <button class="btn btn-primary"><i
                                                class="bi bi-play-fill me-1"></i>Generate</button>
                                    </div>
                                </form>
                                <hr>
                                <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Large datasets
                                    (&gt;50k rows) are blocked. Use narrower date ranges to refine outputs.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Categories (navigation) -->
                <div class="row mb-4">
                    @foreach ($reportTypes as $key => $category)
                        <div class="col-lg-6 col-xl-3 mb-4">
                            <div class="card shadow-sm h-100 border-start border-primary border-4">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-primary"><i class="{{ $category['icon'] }}"></i></span>
                                        <span class="badge bg-light text-dark">{{ count($category['reports']) }}
                                            items</span>
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $category['title'] }}</h6>
                                    <p class="text-muted small flex-grow-1 mb-2">{{ $category['description'] }}</p>
                                    <div class="dropdown mt-auto">
                                        <a href="{{ route('admin.reports.' . $key) }}"
                                            class="btn btn-sm btn-outline-primary w-100 mb-2">Open</a>
                                        <button class="btn btn-sm btn-primary dropdown-toggle w-100"
                                            data-bs-toggle="dropdown">Quick Jump</button>
                                        <ul class="dropdown-menu">
                                            @foreach ($category['reports'] as $report)
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.reports.' . $key) }}">{{ $report }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Additional Trends -->
                <div class="row mb-4">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-graph-up-arrow me-1"></i>Attendance
                                    Trend (14 days)</h6>
                                <small class="text-muted">Updated ~10m</small>
                            </div>
                            <div class="card-body">
                                <canvas id="attendanceTrendChart" aria-label="Attendance percentage last 14 days"
                                    role="img"></canvas>
                                @if (empty($attendanceTrend['labels']))
                                    <div class="text-center text-muted small mt-3">No attendance data in the last 14 days.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary"><i class="bi bi-person-plus me-1"></i>New Enrollments
                                    (6 months)</h6>
                                <small class="text-muted">Updated ~15m</small>
                            </div>
                            <div class="card-body">
                                <canvas id="enrollmentTrendChart" aria-label="Enrollment counts last 6 months"
                                    role="img"></canvas>
                                @if (empty($enrollmentTrend['labels']))
                                    <div class="text-center text-muted small mt-3">No enrollment activity yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Generated Reports -->
                <div class="card shadow-sm mb-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-clock-history me-1"></i>Recent Generated
                            Files</h6>
                        <small class="text-muted">Latest 10</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Rows</th>
                                        <th>Size</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentReports as $log)
                                        <tr>
                                            <td class="small">{{ $log->name }}</td>
                                            <td><span
                                                    class="badge bg-secondary text-capitalize">{{ str_replace('_', ' ', $log->type) }}</span>
                                            </td>
                                            <td>{{ $log->rows_count }}</td>
                                            <td class="text-nowrap">
                                                @if ($log->size_bytes)
                                                    {{ number_format($log->size_bytes / 1024, 1) }} KB
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($log->status === 'running')
                                                    <span class="badge bg-warning text-dark">Running</span>
                                                @elseif($log->status === 'queued')
                                                    <span class="badge bg-secondary">Queued</span>
                                                @else
                                                    <span class="badge bg-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">
                                                {{ optional($log->generated_at)->diffForHumans() ?? '—' }}</td>
                                            <td class="text-end">
                                                @if ($log->status === 'completed' && $log->file_path)
                                                    <a href="{{ route('admin.reports.download', $log->id) }}"
                                                        class="btn btn-sm btn-outline-primary"><i
                                                            class="bi bi-download"></i></a>
                                                @elseif($log->status === 'failed')
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                                        title="{{ $log->error }}"><i
                                                            class="bi bi-exclamation-circle"></i></button>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4 small"><i
                                                    class="bi bi-inbox me-1"></i>No reports generated yet.</td>
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

    {{-- Chart.js CDN (scoped to this page) --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"
            integrity="sha256-vn6Z9erjRzCQXDpUe1koXSPo6e7iT730cdpShUMHbV8=" crossorigin="anonymous"></script>
        <script>
            (function() {
                // Revenue vs Expenses
                const ctx = document.getElementById('revExpChart');
                if (!ctx) return;
                const months = @json($trend['months'] ?? []);
                const rev = @json($trend['rev'] ?? []);
                const exp = @json($trend['exp'] ?? []);
                if (months.length === 0) return;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                                label: 'Revenue',
                                data: rev,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13,110,253,0.15)',
                                fill: true,
                                tension: .35,
                                pointRadius: 3
                            },
                            {
                                label: 'Expenses',
                                data: exp,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.15)',
                                fill: true,
                                tension: .35,
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        stacked: false,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ctx.dataset.label + ': ' + new Intl.NumberFormat().format(ctx
                                        .parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (v) => new Intl.NumberFormat().format(v)
                                }
                            }
                        }
                    }
                });
            })();
            (function() {
                const aEl = document.getElementById('attendanceTrendChart');
                if (!aEl) return;
                const labels = @json($attendanceTrend['labels'] ?? []);
                const values = @json($attendanceTrend['values'] ?? []);
                if (!labels.length) return;
                new Chart(aEl, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Attendance %',
                            data: values,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25,135,84,.15)',
                            tension: .35,
                            fill: true,
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                suggestedMin: Math.max(0, Math.min(...values) - 5),
                                suggestedMax: Math.min(100, Math.max(...values) + 5)
                            }
                        }
                    }
                });
            })();
            (function() {
                const eEl = document.getElementById('enrollmentTrendChart');
                if (!eEl) return;
                const labels = @json($enrollmentTrend['labels'] ?? []);
                const values = @json($enrollmentTrend['values'] ?? []);
                if (!labels.length) return;
                new Chart(eEl, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'New Enrollments',
                            data: values,
                            backgroundColor: 'rgba(13,110,253,.6)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            })();
        </script>
    @endpush
@endsection
