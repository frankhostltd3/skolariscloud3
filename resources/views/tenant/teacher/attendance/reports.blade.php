@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Attendance Reports</h1>
            <p class="text-muted mb-0">Generate and export attendance reports</p>
        </div>
        <a href="{{ route('tenant.teacher.attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Report Criteria</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.teacher.attendance.reports') }}" id="reportForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
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
                        <label for="type" class="form-label">Report Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="summary" {{ $reportType == 'summary' ? 'selected' : '' }}>Summary Report</option>
                            <option value="detailed" {{ $reportType == 'detailed' ? 'selected' : '' }}>Detailed Report</option>
                            <option value="defaulters" {{ $reportType == 'defaulters' ? 'selected' : '' }}>Defaulters Report</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-file-earmark-text"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData)
        <!-- Export Options -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0">
                            <i class="bi bi-download me-2"></i>Export Report
                        </h5>
                        <p class="text-muted mb-0 small">Download this report in your preferred format</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-success" onclick="exportReport('excel')">
                            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                        </button>
                        <button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        @if($reportType == 'summary')
            <!-- Summary Report -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>Summary Report
                    </h5>
                    <small>{{ $selectedClass->name }} {{ $selectedClass->section }} | {{ $startDate }} to {{ $endDate }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="reportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th class="text-center">Total Days</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center">Late</th>
                                    <th class="text-center">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['data'] as $index => $row)
                                    @php
                                        $percentageClass = $row['percentage'] >= 90 ? 'success' : ($row['percentage'] >= 75 ? 'warning' : 'danger');
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($row['student']->photo)
                                                    <img src="{{ asset('storage/' . $row['student']->photo) }}" 
                                                         alt="{{ $row['student']->name }}" 
                                                         class="rounded-circle me-2" 
                                                         width="32" height="32">
                                                @else
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 32px; height: 32px; font-size: 14px;">
                                                        {{ strtoupper(substr($row['student']->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="fw-medium">{{ $row['student']->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $row['student']->email }}</td>
                                        <td class="text-center">{{ $row['total_days'] }}</td>
                                        <td class="text-center"><span class="badge bg-success">{{ $row['present'] }}</span></td>
                                        <td class="text-center"><span class="badge bg-danger">{{ $row['absent'] }}</span></td>
                                        <td class="text-center"><span class="badge bg-warning">{{ $row['late'] }}</span></td>
                                        <td class="text-center">
                                            <strong class="text-{{ $percentageClass }}">{{ $row['percentage'] }}%</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($reportType == 'detailed')
            <!-- Detailed Report -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>Detailed Report
                    </h5>
                    <small>{{ $selectedClass->name }} {{ $selectedClass->section }} | {{ $startDate }} to {{ $endDate }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="reportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Method</th>
                                    <th>Marked By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['data'] as $index => $row)
                                    @php
                                        $statusColors = [
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'late' => 'warning',
                                            'excused' => 'info'
                                        ];
                                        $statusColor = $statusColors[$row->status] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $row->attendance_date->format('M d, Y') }}</td>
                                        <td>{{ $row->student->name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $statusColor }}">{{ ucfirst($row->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted">{{ ucfirst($row->method ?? 'manual') }}</small>
                                        </td>
                                        <td>{{ $row->markedBy->name ?? 'N/A' }}</td>
                                        <td><small class="text-muted">{{ $row->notes ?? '-' }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($reportType == 'defaulters')
            <!-- Defaulters Report -->
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Defaulters Report:</strong> Students with attendance below 75%
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-circle me-2"></i>Defaulters Report (< 75% Attendance)
                    </h5>
                    <small>{{ $selectedClass->name }} {{ $selectedClass->section }} | {{ $startDate }} to {{ $endDate }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="reportTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th class="text-center">Total Days</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center">Percentage</th>
                                    <th class="text-center">Risk Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['data'] as $index => $row)
                                    @php
                                        $riskColors = [
                                            'critical' => 'danger',
                                            'high' => 'warning',
                                            'medium' => 'info',
                                            'low' => 'secondary'
                                        ];
                                        $riskColor = $riskColors[$row['risk_level']] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($row['student']->photo)
                                                    <img src="{{ asset('storage/' . $row['student']->photo) }}" 
                                                         alt="{{ $row['student']->name }}" 
                                                         class="rounded-circle me-2" 
                                                         width="32" height="32">
                                                @else
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 32px; height: 32px; font-size: 14px;">
                                                        {{ strtoupper(substr($row['student']->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="fw-medium">{{ $row['student']->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $row['student']->email }}</td>
                                        <td>{{ $row['student']->phone ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $row['total_days'] }}</td>
                                        <td class="text-center"><span class="badge bg-success">{{ $row['present'] }}</span></td>
                                        <td class="text-center"><span class="badge bg-danger">{{ $row['absent'] }}</span></td>
                                        <td class="text-center">
                                            <strong class="text-danger">{{ $row['percentage'] }}%</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $riskColor }}">{{ ucfirst($row['risk_level']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-success py-4">
                                            <i class="bi bi-emoji-smile" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">Great news! No students below 75% attendance.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @elseif($selectedClass)
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            No attendance data found for the selected criteria. Please adjust the filters and try again.
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Please select a class and configure the report criteria above to generate a report.
        </div>
    @endif
</div>

<script>
function exportReport(format) {
    const form = document.getElementById('reportForm');
    const classId = document.getElementById('class_id').value;
    const type = document.getElementById('type').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!classId || !startDate || !endDate) {
        alert('Please fill in all required fields before exporting.');
        return;
    }
    
    // Build export URL
    const exportUrl = '{{ route("tenant.teacher.attendance.export") }}' + 
        '?class_id=' + encodeURIComponent(classId) +
        '&type=' + encodeURIComponent(type) +
        '&format=' + encodeURIComponent(format) +
        '&start_date=' + encodeURIComponent(startDate) +
        '&end_date=' + encodeURIComponent(endDate);
    
    // Open in new window or trigger download
    window.location.href = exportUrl;
}

// Report type description helper
document.getElementById('type').addEventListener('change', function() {
    const descriptions = {
        'summary': 'Shows total attendance statistics for each student with present, absent, and late counts.',
        'detailed': 'Shows day-by-day attendance records for all students with marking details.',
        'defaulters': 'Shows students with attendance below 75% - useful for follow-up actions.'
    };
    
    const selected = this.value;
    console.log('Report type:', selected, '-', descriptions[selected]);
});
</script>
@endsection

