@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-graph-up"></i> Attendance Analytics</h4>
                    <div>
                        <a href="{{ route('admin.attendance-analytics.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="btn btn-sm btn-success">
                            <i class="bi bi-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel"></i> Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="text-white-50">Manual Entries</h6>
                        <h3 class="mb-0">{{ $methodStats['manual'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="text-white-50">QR/Barcode Scans</h6>
                        <h3 class="mb-0">{{ $methodStats['qr'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="text-white-50">Fingerprint Scans</h6>
                        <h3 class="mb-0">{{ $methodStats['fingerprint'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="text-white-50">Optical Scans</h6>
                        <h3 class="mb-0">{{ $methodStats['optical'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daily Attendance Trend (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Method Performance -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Method Success Rate</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($methodSuccessRate as $method)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span
                                        class="text-capitalize">{{ str_replace('_', ' ', $method->attendance_method) }}</span>
                                    <span
                                        class="badge bg-{{ $method->avg_score >= 80 ? 'success' : ($method->avg_score >= 60 ? 'warning' : 'danger') }}">
                                        {{ number_format($method->avg_score, 1) }}%
                                    </span>
                                </div>
                                <div class="progress mt-1" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $method->avg_score >= 80 ? 'success' : ($method->avg_score >= 60 ? 'warning' : 'danger') }}"
                                        style="width: {{ $method->avg_score }}%">
                                        {{ $method->total }} records
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Peak Hours</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="peakHoursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent High-Quality Scans</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>User</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Quality Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentScans as $scan)
                                        <tr>
                                            <td>{{ $scan->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                @if ($scan->student)
                                                    <i class="bi bi-person-circle text-primary"></i>
                                                    {{ $scan->student->name }}
                                                @elseif($scan->staff)
                                                    <i class="bi bi-person-badge text-success"></i>
                                                    {{ $scan->staff->name }}
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($scan->attendance_method) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $scan->status == 'present' ? 'success' : ($scan->status == 'late' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($scan->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $scan->verification_score >= 80 ? 'success' : ($scan->verification_score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ number_format($scan->verification_score, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No scans with quality scores found</td>
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            // Daily Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendData = @json($dailyTrend);
            const dates = Object.keys(trendData);
            const methods = ['manual', 'qr', 'fingerprint', 'optical'];
            const datasets = methods.map((method, index) => ({
                label: method.charAt(0).toUpperCase() + method.slice(1),
                data: dates.map(date => {
                    const dayData = trendData[date].find(d => d.attendance_method === method);
                    return dayData ? dayData.count : 0;
                }),
                borderColor: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107'][index],
                backgroundColor: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107'][index] + '20',
                tension: 0.4
            }));

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusData = @json($statusStats);
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: ['#198754', '#dc3545', '#ffc107', '#0dcaf0', '#6f42c1', '#fd7e14']
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

            // Peak Hours Chart
            const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
            const peakData = @json($peakHours);
            new Chart(peakCtx, {
                type: 'bar',
                data: {
                    labels: peakData.map(p => `${p.hour}:00`),
                    datasets: [{
                        label: 'Records',
                        data: peakData.map(p => p.count),
                        backgroundColor: '#0d6efd'
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
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
