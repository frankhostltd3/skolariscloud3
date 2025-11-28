@extends('tenant.layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Virtual Class Attendance</h1>
                <p class="text-muted mb-0">{{ $virtual->title }} - {{ $virtual->scheduled_at->format('M d, Y h:i A') }}</p>
            </div>
            <a href="{{ route('tenant.teacher.classroom.virtual.show', $virtual) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Class
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Attendance List</h5>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-primary">{{ $students->where('attended', true)->count() }} Present</span>
                        <span class="badge bg-secondary">{{ $students->where('attended', false)->count() }} Absent</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Join Time</th>
                            <th>Leave Time</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $record)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial rounded-circle bg-light text-primary me-3"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($record['student']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $record['student']->name }}</div>
                                            <div class="small text-muted">{{ $record['student']->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($record['attended'])
                                        <span class="badge bg-success-subtle text-success">Present</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Absent</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $record['attendance']?->joined_at ? \Carbon\Carbon::parse($record['attendance']->joined_at)->format('h:i A') : '-' }}
                                </td>
                                <td>
                                    {{ $record['attendance']?->left_at ? \Carbon\Carbon::parse($record['attendance']->left_at)->format('h:i A') : '-' }}
                                </td>
                                <td>
                                    @if ($record['attendance']?->duration_minutes)
                                        {{ $record['attendance']->duration_minutes }} mins
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No students enrolled in this class.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
