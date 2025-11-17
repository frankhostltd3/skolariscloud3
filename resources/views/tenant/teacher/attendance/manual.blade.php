@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manual Attendance Entry</h1>
            <p class="text-muted mb-0">{{ $class->name }} {{ $class->section }} - {{ $today->format('l, F d, Y') }}</p>
        </div>
        <a href="{{ route('tenant.teacher.attendance.take', ['class_id' => $class->id]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('tenant.teacher.attendance.store') }}" method="POST" id="attendanceForm">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="attendance_date" value="{{ $today->toDateString() }}">

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="bi bi-people me-2"></i>{{ $students->count() }} Students
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm" onclick="markAll('present')">
                                <i class="bi bi-check-all me-1"></i>All Present
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="markAll('absent')">
                                <i class="bi bi-x-circle me-1"></i>All Absent
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="resetAll()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Mark Attendance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Student</th>
                                <th class="text-center" style="width: 120px;">Present</th>
                                <th class="text-center" style="width: 120px;">Absent</th>
                                <th class="text-center" style="width: 120px;">Late</th>
                                <th class="text-center" style="width: 120px;">Excused</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                @php
                                    $existing = $existingAttendance[$student->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            @if($student->photo)
                                                <img src="{{ asset('storage/' . $student->photo) }}" 
                                                     alt="{{ $student->name }}" 
                                                     class="rounded-circle me-3" 
                                                     width="40" height="40">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px; font-size: 16px;">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $student->name }}</div>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-check-inline d-flex justify-content-center">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="attendance[{{ $student->id }}]" 
                                                   id="present_{{ $student->id }}" 
                                                   value="present"
                                                   {{ $existing == 'present' ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label visually-hidden" for="present_{{ $student->id }}">Present</label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-check-inline d-flex justify-content-center">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="attendance[{{ $student->id }}]" 
                                                   id="absent_{{ $student->id }}" 
                                                   value="absent"
                                                   {{ $existing == 'absent' ? 'checked' : '' }}>
                                            <label class="form-check-label visually-hidden" for="absent_{{ $student->id }}">Absent</label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-check-inline d-flex justify-content-center">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="attendance[{{ $student->id }}]" 
                                                   id="late_{{ $student->id }}" 
                                                   value="late"
                                                   {{ $existing == 'late' ? 'checked' : '' }}>
                                            <label class="form-check-label visually-hidden" for="late_{{ $student->id }}">Late</label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="form-check form-check-inline d-flex justify-content-center">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="attendance[{{ $student->id }}]" 
                                                   id="excused_{{ $student->id }}" 
                                                   value="excused"
                                                   {{ $existing == 'excused' ? 'checked' : '' }}>
                                            <label class="form-check-label visually-hidden" for="excused_{{ $student->id }}">Excused</label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-person-x" style="font-size: 3rem;"></i>
                                        <p class="mt-3">No students found in this class.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Additional Notes (Optional)</h6>
            </div>
            <div class="card-body">
                <textarea name="notes" 
                          class="form-control" 
                          rows="3" 
                          placeholder="Add any additional notes about today's attendance..."></textarea>
                <small class="form-text text-muted">Maximum 500 characters</small>
            </div>
        </div>

        <!-- Submit -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted">
                            <i class="bi bi-info-circle me-2"></i>
                            Please ensure all students are marked before submitting.
                        </p>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-2"></i>Save Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function markAll(status) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}

function resetAll() {
    const form = document.getElementById('attendanceForm');
    const radios = form.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        radio.checked = false;
    });
}

// Confirm before leaving if form is dirty
let formModified = false;
document.getElementById('attendanceForm').addEventListener('change', function() {
    formModified = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formModified) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Remove warning when form is submitted
document.getElementById('attendanceForm').addEventListener('submit', function() {
    formModified = false;
});
</script>

<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection

