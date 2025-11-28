@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', 'Attendance Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-user-check text-success me-2"></i>
                    Attendance Details
                </h2>
                <p class="text-muted mb-0">
                    Session for {{ optional($attendance->class)->name ?? 'N/A' }}
                    on {{ optional($attendance->attendance_date)->format('M d, Y') ?? '—' }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('tenant.modules.attendance.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Sessions
                </a>
                <a href="{{ route('tenant.modules.attendance.mark', $attendance->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Update Records
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Total Marked</p>
                        <h3 class="fw-bold mb-0">{{ $statistics['total'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-success h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Present</p>
                        <h3 class="fw-bold text-success mb-0">{{ $statistics['present'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-warning h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Late</p>
                        <h3 class="fw-bold text-warning mb-0">{{ $statistics['late'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-danger h-100">
                    <div class="card-body">
                        <p class="text-muted text-uppercase small mb-1">Absent</p>
                        <h3 class="fw-bold text-danger mb-0">{{ $statistics['absent'] ?? 0 }}</h3>
                        <small class="text-muted">Attendance rate: {{ $statistics['rate'] ?? 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-1"></i> Session Details</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Class</dt>
                            <dd class="col-7">{{ optional($attendance->class)->name ?? '—' }}</dd>

                            <dt class="col-5 text-muted">Stream</dt>
                            <dd class="col-7">{{ optional($attendance->classStream)->name ?? '—' }}</dd>

                            <dt class="col-5 text-muted">Subject</dt>
                            <dd class="col-7">{{ optional($attendance->subject)->name ?? '—' }}</dd>

                            <dt class="col-5 text-muted">Teacher</dt>
                            <dd class="col-7">{{ optional($attendance->teacher)->name ?? '—' }}</dd>

                            <dt class="col-5 text-muted">Date</dt>
                            <dd class="col-7">{{ optional($attendance->attendance_date)->format('M d, Y') ?? '—' }}</dd>

                            <dt class="col-5 text-muted">Time In</dt>
                            <dd class="col-7">{{ $attendance->time_in ? $attendance->time_in->format('H:i') : '—' }}</dd>

                            <dt class="col-5 text-muted">Time Out</dt>
                            <dd class="col-7">{{ $attendance->time_out ? $attendance->time_out->format('H:i') : '—' }}
                            </dd>

                            <dt class="col-5 text-muted">Type</dt>
                            <dd class="col-7">{{ ucfirst($attendance->attendance_type ?? 'classroom') }}</dd>

                            <dt class="col-5 text-muted">Notes</dt>
                            <dd class="col-7">{{ $attendance->notes ?? '—' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-users me-1"></i> Student Records</h5>
                        <span class="badge bg-secondary">{{ $attendance->records->count() }} students</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th>Arrival Time</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendance->records as $record)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ optional($record->student)->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-muted small">{{ optional($record->student)->email }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($record->status) {
                                                    'present' => 'success',
                                                    'late' => 'warning',
                                                    'excused' => 'info',
                                                    'sick' => 'info',
                                                    'half_day' => 'primary',
                                                    default => 'danger',
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $statusClass }} text-uppercase">{{ ucfirst($record->status) }}</span>
                                        </td>
                                        <td>{{ $record->arrival_time ? \Carbon\Carbon::parse($record->arrival_time)->format('H:i') : '—' }}
                                        </td>
                                        <td class="text-muted">{{ $record->notes ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No attendance records yet. Click "Update Records" to start marking.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
