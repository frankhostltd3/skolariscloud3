@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Manual Roll Call
                    </h4>
                    <div class="page-title-right">
                        <a href="{{ route('tenant.modules.attendance.show', $attendance->id) }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Info -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Class:</strong> {{ $attendance->class->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Subject:</strong> {{ $attendance->subject->name ?? 'General' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ $attendance->attendance_date->format('M d, Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Total Students:</strong> {{ $students->count() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="btn-toolbar justify-content-between" role="toolbar">
                    <div class="btn-group mb-2" role="group">
                        <button type="button" class="btn btn-success" onclick="bulkMark('present')">
                            <i class="bi bi-check-all"></i> Mark All Present
                        </button>
                        <button type="button" class="btn btn-danger" onclick="bulkMark('absent')">
                            <i class="bi bi-x-circle"></i> Mark All Absent
                        </button>
                        <button type="button" class="btn btn-warning" onclick="bulkMark('late')">
                            <i class="bi bi-clock"></i> Mark Selected Late
                        </button>
                    </div>
                    <div class="btn-group mb-2" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="selectAll()">
                            <i class="bi bi-check-square"></i> Select All
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                            <i class="bi bi-square"></i> Deselect All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Form -->
        <form action="{{ route('tenant.modules.attendance.save-records', $attendance->id) }}" method="POST"
            id="attendanceForm">
            @csrf

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Student Roster</h5>
                    <div>
                        <span class="badge bg-success me-2">Present: <span id="countPresent">0</span></span>
                        <span class="badge bg-danger me-2">Absent: <span id="countAbsent">0</span></span>
                        <span class="badge bg-warning me-2">Late: <span id="countLate">0</span></span>
                        <span class="badge bg-info">Unmarked: <span
                                id="countUnmarked">{{ $students->count() }}</span></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th width="5%">#</th>
                                    <th width="30%">Student Name</th>
                                    <th width="15%">Admission No.</th>
                                    <th width="30%">Status</th>
                                    <th width="15%">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr data-student-id="{{ $student->id }}">
                                        <td>
                                            <input type="checkbox" class="student-checkbox" value="{{ $student->id }}">
                                        </td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($student->photo)
                                                    <img src="{{ $student->photo }}" class="rounded-circle me-2"
                                                        width="32" height="32">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px; font-size: 14px;">
                                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="fw-medium">{{ $student->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $student->admission_number }}</td>
                                        <td>
                                            <input type="hidden" name="records[{{ $index }}][student_id]"
                                                value="{{ $student->id }}">
                                            <select name="records[{{ $index }}][status]"
                                                class="form-select form-select-sm status-select"
                                                data-student="{{ $student->id }}" required onchange="updateCounts()">
                                                <option value="">-- Select --</option>
                                                <option value="present"
                                                    {{ $student->attendance_status === 'present' ? 'selected' : '' }}>
                                                    ✓ Present
                                                </option>
                                                <option value="absent"
                                                    {{ $student->attendance_status === 'absent' ? 'selected' : '' }}>
                                                    ✗ Absent
                                                </option>
                                                <option value="late"
                                                    {{ $student->attendance_status === 'late' ? 'selected' : '' }}>
                                                    ⏰ Late
                                                </option>
                                                <option value="excused"
                                                    {{ $student->attendance_status === 'excused' ? 'selected' : '' }}>
                                                    📋 Excused
                                                </option>
                                                <option value="sick"
                                                    {{ $student->attendance_status === 'sick' ? 'selected' : '' }}>
                                                    🤒 Sick
                                                </option>
                                                <option value="half_day"
                                                    {{ $student->attendance_status === 'half_day' ? 'selected' : '' }}>
                                                    ½ Half Day
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="records[{{ $index }}][notes]"
                                                class="form-control form-control-sm" placeholder="Optional">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Tip: Use keyboard shortcuts: P=Present, A=Absent, L=Late
                            </small>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Save Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Update counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCounts();
        });

        // Update status counts
        function updateCounts() {
            const selects = document.querySelectorAll('.status-select');
            const counts = {
                present: 0,
                absent: 0,
                late: 0,
                unmarked: 0
            };

            selects.forEach(select => {
                const status = select.value;
                if (status === 'present') counts.present++;
                else if (status === 'absent') counts.absent++;
                else if (status === 'late') counts.late++;
                else counts.unmarked++;
            });

            document.getElementById('countPresent').textContent = counts.present;
            document.getElementById('countAbsent').textContent = counts.absent;
            document.getElementById('countLate').textContent = counts.late;
            document.getElementById('countUnmarked').textContent = counts.unmarked;
        }

        // Bulk mark selected students
        function bulkMark(status) {
            const checkboxes = document.querySelectorAll('.student-checkbox:checked');

            if (checkboxes.length === 0) {
                alert('Please select at least one student.');
                return;
            }

            checkboxes.forEach(checkbox => {
                const studentId = checkbox.value;
                const select = document.querySelector(`.status-select[data-student="${studentId}"]`);
                if (select) {
                    select.value = status;
                }
            });

            updateCounts();
        }

        // Select/Deselect all
        function selectAll() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAllCheckbox').checked = true;
        }

        function deselectAll() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAllCheckbox').checked = false;
        }

        function toggleSelectAll() {
            const checked = document.getElementById('selectAllCheckbox').checked;
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Only if not in input field
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            const key = e.key.toLowerCase();
            const focused = document.activeElement;

            if (focused && focused.classList.contains('status-select')) {
                if (key === 'p') focused.value = 'present';
                else if (key === 'a') focused.value = 'absent';
                else if (key === 'l') focused.value = 'late';
                else if (key === 'e') focused.value = 'excused';
                else if (key === 's') focused.value = 'sick';

                updateCounts();
            }
        });

        // Auto-save warning
        let formChanged = false;
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', () => formChanged = true);
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        document.getElementById('attendanceForm').addEventListener('submit', function() {
            formChanged = false;
        });
    </script>
@endsection
