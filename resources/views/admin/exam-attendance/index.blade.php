@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', 'Exam Attendance')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-file-alt me-2 text-warning"></i>
                    Exam Attendance
                </h2>
                <p class="text-muted mb-0">Track student attendance during examinations</p>
            </div>
            <a href="{{ route('admin.exam-attendance.create') }}" class="btn btn-warning">
                <i class="fas fa-plus me-1"></i>New Exam Session
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.exam-attendance.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ request('date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="class_id" class="form-label">Class</label>
                            <select class="form-select" id="class_id" name="class_id">
                                <option value="">All Classes</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select class="form-select" id="subject_id" name="subject_id">
                                <option value="">All Subjects</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.exam-attendance.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Exam Attendance Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Exam Attendance Sessions
                </h5>
            </div>
            <div class="card-body">
                @if ($attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Time</th>
                                    <th>Invigilator</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                        <td><span class="badge bg-primary">{{ $attendance->class->name ?? 'N/A' }}</span>
                                        </td>
                                        <td><span class="badge bg-info">{{ $attendance->subject->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            {{ $attendance->time_in ? $attendance->time_in->format('H:i') : '-' }}
                                            @if ($attendance->time_out)
                                                - {{ $attendance->time_out->format('H:i') }}
                                            @endif
                                        </td>
                                        <td>{{ $attendance->teacher->full_name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.exam-attendance.show', $attendance->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.exam-attendance.mark', $attendance->id) }}"
                                                class="btn btn-sm btn-outline-success" title="Mark Attendance">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <form action="{{ route('admin.exam-attendance.destroy', $attendance->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $attendances->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-signature fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No exam attendance sessions found.</p>
                        <a href="{{ route('admin.exam-attendance.create') }}" class="btn btn-warning">
                            <i class="fas fa-plus me-1"></i>Create First Session
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
