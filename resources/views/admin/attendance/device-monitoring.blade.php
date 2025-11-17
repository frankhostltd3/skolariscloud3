@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="bi bi-hdd-network"></i> Device Monitoring</h4>
                    <div>
                        <button id="refreshBtn" class="btn btn-sm btn-primary">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <a href="{{ route('tenant.attendance.settings.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-gear"></i> Device Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Status -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Device Status</h5>
                    </div>
                    <div class="card-body">
                        @if ($deviceConfig['enabled'])
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="status-indicator bg-{{ $deviceStatus && $deviceStatus['connected'] ? 'success' : 'danger' }}">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">
                                            {{ $deviceStatus && $deviceStatus['connected'] ? 'Connected' : 'Disconnected' }}
                                        </h6>
                                        <small class="text-muted">Last checked: <span id="lastChecked">Just
                                                now</span></small>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <dl class="row mb-0">
                                <dt class="col-sm-5">Device Type:</dt>
                                <dd class="col-sm-7">{{ ucfirst($deviceConfig['device_type']) }}</dd>

                                <dt class="col-sm-5">IP Address:</dt>
                                <dd class="col-sm-7">{{ $deviceConfig['device_ip'] }}</dd>

                                <dt class="col-sm-5">Port:</dt>
                                <dd class="col-sm-7">{{ $deviceConfig['device_port'] }}</dd>

                                @if ($deviceStatus && isset($deviceStatus['error']))
                                    <dt class="col-sm-5">Error:</dt>
                                    <dd class="col-sm-7 text-danger">{{ $deviceStatus['error'] }}</dd>
                                @endif

                                @if ($deviceStatus && isset($deviceStatus['firmware']))
                                    <dt class="col-sm-5">Firmware:</dt>
                                    <dd class="col-sm-7">{{ $deviceStatus['firmware'] }}</dd>
                                @endif

                                @if ($deviceStatus && isset($deviceStatus['users']))
                                    <dt class="col-sm-5">Enrolled Users:</dt>
                                    <dd class="col-sm-7">{{ $deviceStatus['users'] }}</dd>
                                @endif
                            </dl>

                            <button id="testConnectionBtn" class="btn btn-primary w-100 mt-3">
                                <i class="bi bi-plug"></i> Test Connection
                            </button>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Fingerprint device is disabled. Enable it in settings to start monitoring.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Live Statistics -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Live Statistics (Today)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h3 class="mb-0" id="todayScans">0</h3>
                                    <p class="text-muted mb-0">Total Scans</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h3 class="mb-0" id="successRate">0%</h3>
                                    <p class="text-muted mb-0">Success Rate</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h3 class="mb-0" id="lastScan">N/A</h3>
                                    <p class="text-muted mb-0">Last Scan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Performance Metrics (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Uptime -->
        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Device Activity (Last 24 Hours)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="uptimeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Errors -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Low-Quality Scans</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentErrors as $error)
                                        <tr>
                                            <td>{{ $error->created_at->format('M d H:i') }}</td>
                                            <td>
                                                @if ($error->student)
                                                    {{ $error->student->name }}
                                                @elseif($error->staff)
                                                    {{ $error->staff->name }}
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-danger">{{ number_format($error->verification_score, 1) }}%</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No errors found</td>
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
            // Performance Chart
            const performanceCtx = document.getElementById('performanceChart').getContext('2d');
            const metricsData = @json($metrics);
            new Chart(performanceCtx, {
                type: 'line',
                data: {
                    labels: metricsData.map(m => m.date),
                    datasets: [{
                            label: 'Total Scans',
                            data: metricsData.map(m => m.total_scans),
                            borderColor: '#0d6efd',
                            backgroundColor: '#0d6efd20',
                            yAxisID: 'y'
                        },
                        {
                            label: 'Success Rate (%)',
                            data: metricsData.map(m => m.avg_score),
                            borderColor: '#198754',
                            backgroundColor: '#19875420',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });

            // Uptime Chart
            const uptimeCtx = document.getElementById('uptimeChart').getContext('2d');
            const uptimeData = @json($uptimeData);
            const allHours = Array.from({
                length: 24
            }, (_, i) => i);
            const uptimeScans = allHours.map(hour => {
                const data = uptimeData.find(d => d.hour == hour);
                return data ? data.scans : 0;
            });

            new Chart(uptimeCtx, {
                type: 'bar',
                data: {
                    labels: allHours.map(h => `${h}:00`),
                    datasets: [{
                        label: 'Scans',
                        data: uptimeScans,
                        backgroundColor: '#0dcaf0'
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

            // Test Connection
            document.getElementById('testConnectionBtn')?.addEventListener('click', function() {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Testing...';

                fetch('{{ route('admin.device-monitoring.test') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) location.reload();
                    })
                    .catch(err => alert('Connection test failed'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-plug"></i> Test Connection';
                    });
            });

            // Auto-refresh stats every 30 seconds
            function refreshStats() {
                fetch('{{ route('admin.device-monitoring.stats') }}')
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('todayScans').textContent = data.today_scans;
                        document.getElementById('successRate').textContent = data.success_rate ? data.success_rate.toFixed(
                            1) + '%' : '0%';
                        document.getElementById('lastScan').textContent = data.last_scan ? new Date(data.last_scan
                            .created_at).toLocaleTimeString() : 'N/A';
                        document.getElementById('lastChecked').textContent = 'Just now';
                    });
            }

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', refreshStats);

            // Auto-refresh
            setInterval(refreshStats, 30000);
            refreshStats();

            // Status indicator styles
            const style = document.createElement('style');
            style.textContent = `
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
`;
            document.head.appendChild(style);
        </script>
    @endpush
@endsection
