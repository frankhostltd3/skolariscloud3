@extends('layouts.tenant.student')

@section('title', 'My Attendance')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <h4 class="mb-0">My Attendance</h4>
                <small class="text-muted">Overview of your attendance records</small>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="status" class="form-label mb-0">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                            <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                            <option value="excused" {{ $status === 'excused' ? 'selected' : '' }}>Excused</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_range" class="form-label mb-0">Date Range</label>
                        <select name="date_range" id="date_range" class="form-select"
                            onchange="toggleCustomDates(this.value)">
                            <option value="this_term" {{ $dateRange === 'this_term' ? 'selected' : '' }}>This Term</option>
                            <option value="this_month" {{ $dateRange === 'this_month' ? 'selected' : '' }}>This Month
                            </option>
                            <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div class="col-md-3 custom-date-fields {{ $dateRange === 'custom' ? '' : 'd-none' }}">
                        <label for="start_date" class="form-label mb-0">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3 custom-date-fields {{ $dateRange === 'custom' ? '' : 'd-none' }}">
                        <label for="end_date" class="form-label mb-0">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-4">{{ $percentPresent }}%</div>
                        <div class="small">Present</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-4">{{ $present }}</div>
                        <div class="small">Days Present</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-4">{{ $absent }}</div>
                        <div class="small">Days Absent</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-4">{{ $late }}</div>
                        <div class="small">Late</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Records</h6>
            </div>
            <div class="card-body p-0">
                @if ($records->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Class</th>
                                    <th>Marked By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $rec)
                                    <tr>
                                        <td>{{ $rec->attendance_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($rec->status === 'present')
                                                <span class="badge bg-success">Present</span>
                                            @elseif($rec->status === 'absent')
                                                <span class="badge bg-danger">Absent</span>
                                            @elseif($rec->status === 'late')
                                                <span class="badge bg-warning text-dark">Late</span>
                                            @elseif($rec->status === 'excused')
                                                <span class="badge bg-info text-dark">Excused</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($rec->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $rec->class?->name ?? '-' }}</td>
                                        <td>{{ $rec->markedBy?->name ?? '-' }}</td>
                                        <td>{{ $rec->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info m-3 mb-0">
                        No attendance records found for the selected filters.
                    </div>
                @endif
            </div>
        </div>
    @section('scripts')
        <script>
            function toggleCustomDates(val) {
                var fields = document.querySelectorAll('.custom-date-fields');
                fields.forEach(function(f) {
                    f.style.display = (val === 'custom') ? 'block' : 'none';
                });
            }
            document.addEventListener('DOMContentLoaded', function() {
                toggleCustomDates(document.getElementById('date_range').value);
            });
        </script>
    @endsection
</div>
@endsection
