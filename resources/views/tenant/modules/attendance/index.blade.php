@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('title', 'Attendance Management')

@push('styles')
<style>
    .stats-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    }
    .attendance-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .student-row {
        transition: background-color 0.2s;
    }
    .student-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .critical-alert {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1rem;
    }
    .trend-chart {
        height: 200px;
    }
    .percentage-ring {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: bold;
        position: relative;
    }
    .quick-filter-btn {
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fas fa-calendar-check me-2" style="color: #667eea;"></i>
                        Attendance Management
                    </h2>
                    <p class="text-muted mb-0">Comprehensive attendance tracking & analytics</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($selectedClass ?? false)
                    <button class="btn btn-outline-warning" onclick="notifyAbsentParents()">
                        <i class="fas fa-bell me-2"></i>Notify Parents
                    </button>
                    <button class="btn btn-outline-success" onclick="exportAttendance()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    @endif
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#attendanceReportModal">
                        <i class="fas fa-chart-bar me-2"></i>Reports
                    </button>
                    <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#comparativeStatsModal">
                        <i class="fas fa-chart-line me-2"></i>Analytics
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.modules.attendance.index') }}" id="attendanceFilterForm">
                <div class="row align-items-end">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="class_id" class="form-label fw-semibold">
                            <i class="fas fa-chalkboard-teacher text-primary me-1"></i>Select Class
                        </label>
                        <select class="form-select" id="class_id" name="class_id" required onchange="this.form.submit()">
                            <option value="">Choose a class...</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ ($selectedClass ?? false) && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} - {{ $class->section }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="date" class="form-label fw-semibold">
                            <i class="fas fa-calendar text-success me-1"></i>Date
                        </label>
                        <input type="date" class="form-control" id="date" name="date"
                            value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-lg-5 col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-filter text-info me-1"></i>Quick Filters
                        </label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary quick-filter-btn" onclick="setToday()">
                                <i class="fas fa-calendar-day me-1"></i>Today
                            </button>
                            <button type="button" class="btn btn-outline-secondary quick-filter-btn" onclick="setYesterday()">
                                <i class="fas fa-calendar-minus me-1"></i>Yesterday
                            </button>
                            <button type="button" class="btn btn-outline-info quick-filter-btn" onclick="setThisWeek()">
                                <i class="fas fa-calendar-week me-1"></i>This Week
                            </button>
                            <button type="button" class="btn btn-outline-success quick-filter-btn" onclick="setThisMonth()">
                                <i class="fas fa-calendar me-1"></i>This Month
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedClass ?? false)
    <!-- Attendance Stats -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card bg-success text-white stats-card shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="fas fa-check-circle fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-1 fw-bold" id="presentCount">{{ $todayStats['present'] }}</h3>
                    <small class="text-uppercase">Present Today</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card bg-danger text-white stats-card shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="fas fa-times-circle fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-1 fw-bold" id="absentCount">{{ $todayStats['absent'] }}</h3>
                    <small class="text-uppercase">Absent Today</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card bg-warning text-white stats-card shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="fas fa-clock fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-1 fw-bold" id="lateCount">{{ $todayStats['late'] }}</h3>
                    <small class="text-uppercase">Late Today</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card bg-info text-white stats-card shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="fas fa-user-shield fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-1 fw-bold" id="excusedCount">{{ $todayStats['excused'] }}</h3>
                    <small class="text-uppercase">Excused</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card bg-secondary text-white stats-card shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="fas fa-question-circle fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-1 fw-bold" id="notMarkedCount">{{ $todayStats['not_marked'] }}</h3>
                    <small class="text-uppercase">Not Marked</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card text-white stats-card shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-center py-4">
                    <div class="percentage-ring mx-auto mb-2" style="background: rgba(255,255,255,0.2);">
                        <span id="attendancePercentage">{{ $todayStats['percentage'] }}%</span>
                    </div>
                    <small class="text-uppercase">Attendance Rate</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Row -->
    <div class="row mb-4">
        <!-- Weekly Trend Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Weekly Attendance Trend
                    </h5>
                    <small class="text-muted">Last 7 days attendance pattern</small>
                </div>
                <div class="card-body">
                    <canvas id="weeklyTrendChart" class="trend-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Stats -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-success me-2"></i>Monthly Overview
                    </h5>
                    <small class="text-muted">Last 4 weeks attendance percentage</small>
                </div>
                <div class="card-body">
                    <canvas id="monthlyStatsChart" class="trend-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Students Alert -->
    @if(count($criticalStudents) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert critical-alert shadow-sm" role="alert">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-0">Attendance Alert: {{ count($criticalStudents) }} Student(s) Below 75%</h5>
                        <small>These students require immediate attention</small>
                    </div>
                </div>
                <div class="row mt-3">
                    @foreach($criticalStudents as $critical)
                    <div class="col-md-4 mb-2">
                        <div class="bg-white bg-opacity-10 rounded p-2">
                            <strong>{{ $critical['student']->name }}</strong>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="badge bg-danger">{{ $critical['percentage'] }}%</span>
                                <small>{{ $critical['absent_days'] }} absent days</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Attendance Form -->
    <div class="card shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2 text-primary"></i>
                {{ $selectedClass->name }} - {{ $selectedClass->section }}
                <span class="text-muted">- {{ $selectedDate->format('F d, Y') }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if(count($attendanceRecords) > 0)
            <form method="POST" action="{{ route('tenant.modules.attendance.mark') }}" id="attendanceForm">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success" onclick="markAll('present')">
                                <i class="fas fa-check me-1"></i>Mark All Present
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="markAll('absent')">
                                <i class="fas fa-times me-1"></i>Mark All Absent
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Attendance
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Photo</th>
                                <th width="20%">Student Name</th>
                                <th width="12%">Student ID</th>
                                <th width="15%">30-Day Stats</th>
                                <th width="38%">Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $index => $record)
                            @php 
                                $student = $record['student']; 
                                $attendance = $record['attendance']; 
                                $history = $record['history'];
                                $badgeClass = $history['percentage'] >= 90 ? 'success' : ($history['percentage'] >= 75 ? 'warning' : 'danger');
                            @endphp
                            <tr class="student-row" onclick="showStudentHistory({{ $student->id }})">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($student->profile_photo ?? false)
                                    <img src="{{ asset('storage/' . $student->profile_photo) }}"
                                        alt="{{ $student->name }}"
                                        class="rounded-circle shadow-sm"
                                        width="45" height="45"
                                        style="object-fit: cover;">
                                    @else
                                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $student->name }}</strong>
                                    <br>
                                    <small class="text-muted">Click for history</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $student->student_id ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $badgeClass }}" 
                                                role="progressbar" 
                                                style="width: {{ $history['percentage'] }}%"
                                                aria-valuenow="{{ $history['percentage'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ $history['percentage'] }}%
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle text-success"></i> {{ $history['present'] }}
                                        <i class="fas fa-times-circle text-danger ms-1"></i> {{ $history['absent'] }}
                                        <i class="fas fa-clock text-warning ms-1"></i> {{ $history['late'] }}
                                    </small>
                                </td>
                                <td onclick="event.stopPropagation()">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                            id="present_{{ $student->id }}" value="present"
                                            {{ $attendance && $attendance->status == 'present' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success btn-sm" for="present_{{ $student->id }}">
                                            <i class="fas fa-check me-1"></i>Present
                                        </label>

                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                            id="absent_{{ $student->id }}" value="absent"
                                            {{ $attendance && $attendance->status == 'absent' ? 'checked' : (!$attendance ? 'checked' : '') }}>
                                        <label class="btn btn-outline-danger btn-sm" for="absent_{{ $student->id }}">
                                            <i class="fas fa-times me-1"></i>Absent
                                        </label>

                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                            id="late_{{ $student->id }}" value="late"
                                            {{ $attendance && $attendance->status == 'late' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning btn-sm" for="late_{{ $student->id }}">
                                            <i class="fas fa-clock me-1"></i>Late
                                        </label>

                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                            id="excused_{{ $student->id }}" value="excused"
                                            {{ $attendance && $attendance->status == 'excused' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-info btn-sm" for="excused_{{ $student->id }}">
                                            <i class="fas fa-user-shield me-1"></i>Excused
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No students found in this class</h5>
                <p class="text-muted">Please check if students are properly enrolled in this class.</p>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Select a class to mark attendance</h5>
            <p class="text-muted">Choose a class from the dropdown above to start marking attendance.</p>
        </div>
    </div>
    @endif
</div>

<!-- Export Modal -->
<div class="modal fade" id="attendanceReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Attendance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('tenant.modules.attendance.export') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_class_id" class="form-label">Class</label>
                        <select class="form-select" id="export_class_id" name="class_id" required>
                            <option value="">Select a class...</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ ($selectedClass ?? false) && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} - {{ $class->section }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student History Modal -->
<div class="modal fade" id="studentHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Attendance History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="studentHistoryContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comparative Stats Modal -->
<div class="modal fade" id="comparativeStatsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>Comparative Class Analytics
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="comparativeStatsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function markAll(status) {
        const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
        radios.forEach(radio => {
            radio.checked = true;
        });
    }

    function setToday() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
        document.getElementById('attendanceFilterForm').submit();
    }

    function setYesterday() {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        document.getElementById('date').value = yesterday.toISOString().split('T')[0];
        document.getElementById('attendanceFilterForm').submit();
    }

    function setThisWeek() {
        const today = new Date();
        const monday = new Date(today);
        monday.setDate(today.getDate() - today.getDay() + 1);
        document.getElementById('date').value = monday.toISOString().split('T')[0];
        document.getElementById('attendanceFilterForm').submit();
    }

    function setThisMonth() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        document.getElementById('date').value = firstDay.toISOString().split('T')[0];
        document.getElementById('attendanceFilterForm').submit();
    }

    function exportAttendance() {
        const modal = new bootstrap.Modal(document.getElementById('attendanceReportModal'));
        modal.show();
    }

    function notifyAbsentParents() {
        if (!confirm('Send notifications to parents of absent students?')) return;
        
        const classId = document.getElementById('class_id').value;
        const date = document.getElementById('date').value;
        
        fetch('{{ route("tenant.modules.attendance.notify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ class_id: classId, date: date })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send notifications');
        });
    }

    function showStudentHistory(studentId) {
        const modal = new bootstrap.Modal(document.getElementById('studentHistoryModal'));
        modal.show();
        
        fetch(`{{ url('/modules/attendance/student') }}/${studentId}/history?days=30`)
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="text-center mb-4">
                        <h4>${data.student.name}</h4>
                        <p class="text-muted">${data.student.student_id || 'N/A'}</p>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body">
                                    <h3>${data.stats.present}</h3>
                                    <small>Present</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white text-center">
                                <div class="card-body">
                                    <h3>${data.stats.absent}</h3>
                                    <small>Absent</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white text-center">
                                <div class="card-body">
                                    <h3>${data.stats.late}</h3>
                                    <small>Late</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body">
                                    <h3>${data.stats.percentage}%</h3>
                                    <small>Attendance Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="mb-3">Last 30 Days History</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Class</th>
                                    <th>Status</th>
                                    <th>Marked By</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.history.forEach(record => {
                    const statusBadge = {
                        'present': '<span class="badge bg-success">Present</span>',
                        'absent': '<span class="badge bg-danger">Absent</span>',
                        'late': '<span class="badge bg-warning">Late</span>',
                        'excused': '<span class="badge bg-info">Excused</span>'
                    };
                    
                    html += `
                        <tr>
                            <td>${new Date(record.date).toLocaleDateString()}</td>
                            <td>${record.class ? record.class.name : 'N/A'}</td>
                            <td>${statusBadge[record.status] || record.status}</td>
                            <td>${record.marked_by ? record.marked_by.name : 'System'}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                document.getElementById('studentHistoryContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('studentHistoryContent').innerHTML = 
                    '<div class="alert alert-danger">Failed to load student history</div>';
            });
    }

    // Set default dates for export modal
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);

        if (document.getElementById('start_date')) {
            document.getElementById('start_date').value = weekAgo.toISOString().split('T')[0];
            document.getElementById('end_date').value = today;
        }

        // Initialize charts if data exists
        @if(($selectedClass ?? false) && count($weeklyStats) > 0)
        initializeCharts();
        @endif

        // Load comparative stats when modal is opened
        const comparativeModal = document.getElementById('comparativeStatsModal');
        if (comparativeModal) {
            comparativeModal.addEventListener('show.bs.modal', function() {
                loadComparativeStats();
            });
        }
    });

    @if(($selectedClass ?? false) && count($weeklyStats) > 0)
    function initializeCharts() {
        // Weekly Trend Chart
        const weeklyCtx = document.getElementById('weeklyTrendChart');
        if (weeklyCtx) {
            new Chart(weeklyCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($weeklyStats, 'date')) !!},
                    datasets: [
                        {
                            label: 'Present',
                            data: {!! json_encode(array_column($weeklyStats, 'present')) !!},
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Absent',
                            data: {!! json_encode(array_column($weeklyStats, 'absent')) !!},
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Late',
                            data: {!! json_encode(array_column($weeklyStats, 'late')) !!},
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Monthly Stats Chart
        const monthlyCtx = document.getElementById('monthlyStatsChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($monthlyStats, 'week')) !!},
                    datasets: [{
                        label: 'Attendance %',
                        data: {!! json_encode(array_column($monthlyStats, 'percentage')) !!},
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(118, 75, 162, 0.8)',
                            'rgba(237, 100, 166, 0.8)',
                            'rgba(255, 154, 158, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    @endif

    function loadComparativeStats() {
        const date = document.getElementById('date').value;
        
        fetch(`{{ route('tenant.modules.attendance.comparative') }}?date=${date}`)
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="row mb-4">
                        <div class="col-12">
                            <canvas id="comparativeChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Class</th>
                                    <th>Total Students</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Attendance %</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.forEach((classData, index) => {
                    const badgeClass = classData.percentage >= 90 ? 'success' : (classData.percentage >= 75 ? 'warning' : 'danger');
                    html += `
                        <tr>
                            <td><strong>#${index + 1}</strong></td>
                            <td>${classData.class_name}</td>
                            <td>${classData.total}</td>
                            <td><span class="badge bg-success">${classData.present}</span></td>
                            <td><span class="badge bg-danger">${classData.absent}</span></td>
                            <td><span class="badge bg-${badgeClass} fs-6">${classData.percentage}%</span></td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                document.getElementById('comparativeStatsContent').innerHTML = html;
                
                // Create comparative chart
                const ctx = document.getElementById('comparativeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.class_name),
                        datasets: [{
                            label: 'Attendance Percentage',
                            data: data.map(d => d.percentage),
                            backgroundColor: 'rgba(102, 126, 234, 0.8)',
                            borderColor: 'rgba(102, 126, 234, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('comparativeStatsContent').innerHTML = 
                    '<div class="alert alert-danger">Failed to load comparative statistics</div>';
            });
    }
</script>
@endpush
@endsection
