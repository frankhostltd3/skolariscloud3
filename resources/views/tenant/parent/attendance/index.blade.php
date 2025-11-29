@extends('layouts.tenant.parent')

@section('title', __('Attendance Records'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Attendance Records') }}</h4>
    </div>

    <div class="row g-4">
        @forelse ($students as $student)
            @php
                $attendanceRecords = optional(optional($student)->account)->attendanceRecords ?? collect([]);
                $totalDays = $attendanceRecords->count();
                $presentDays = $attendanceRecords->where('status', 'present')->count();
                $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
            @endphp
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @if (optional($student)->profile_photo)
                                <img src="{{ $student->profile_photo }}" alt="{{ $student->name }}" class="rounded-circle me-2"
                                    width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center me-2"
                                    style="width: 40px; height: 40px;">
                                    <span class="fw-bold text-muted">{{ substr($student->name ?? 'S', 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $student->name ?? 'Student' }}</h6>
                                <small class="text-muted">{{ optional($student->class)->name ?? 'No Class' }}</small>
                            </div>
                        </div>
                        <div>
                            <span
                                class="badge bg-{{ $attendancePercentage >= 80 ? 'success' : ($attendancePercentage >= 60 ? 'warning' : 'danger') }} fs-6">
                                {{ __('Attendance') }}: {{ $attendancePercentage }}%
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Remarks') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendanceRecords->sortByDesc('attendance_date')->take(10) as $record)
                                        <tr>
                                            <td class="ps-4">
                                                {{ $record->attendance_date ? \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') : ($record->created_at ? $record->created_at->format('M d, Y') : '-') }}
                                            </td>
                                            <td>
                                                @if ($record->status == 'present')
                                                    <span class="badge bg-success">{{ __('Present') }}</span>
                                                @elseif($record->status == 'absent')
                                                    <span class="badge bg-danger">{{ __('Absent') }}</span>
                                                @elseif($record->status == 'late')
                                                    <span class="badge bg-warning">{{ __('Late') }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary">{{ ucfirst($record->status ?? 'Unknown') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $record->remarks ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                {{ __('No attendance records found for this student.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($attendanceRecords->count() > 10)
                            <div class="card-footer bg-white text-center py-2">
                                <small class="text-muted">{{ __('Showing last 10 records') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>{{ __('No children linked to your account.') }}
                </div>
            </div>
        @endforelse
    </div>
@endsection
