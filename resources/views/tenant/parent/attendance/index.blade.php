@extends('layouts.dashboard-parent')

@section('title', __('Attendance Overview'))

@section('content')
@php
    $guardianName = auth()->user()->name ?? __('Guardian');
    $startDate = $dateFilters['start'];
    $endDate = $dateFilters['end'];
    $rangeDays = $dateFilters['days'];
    $presetRanges = [
        7 => __('Last 7 days'),
        30 => __('Last 30 days'),
        60 => __('Last 60 days'),
        90 => __('Last 90 days'),
    ];
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ __('Attendance overview') }}</h2>
        <p class="text-muted mb-0">{{ __('Track your child\'s daily attendance with confidence.') }}</p>
    </div>
    <div class="text-md-end">
        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">{{ __('Welcome, :name', ['name' => $guardianName]) }}</span>
    </div>
</div>

@if($wards->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">{{ __('No linked students yet') }}</h5>
            <p class="text-muted mb-0">{{ __('Once the school links students to your account, their attendance records will appear here.') }}</p>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.parent.attendance.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="student_id" class="form-label fw-semibold">{{ __('Select child') }}</label>
                    <select name="student_id" id="student_id" class="form-select" onchange="this.form.submit()">
                        @foreach($wards as $ward)
                            <option value="{{ $ward->id }}" {{ optional($selectedWard)->id === $ward->id ? 'selected' : '' }}>
                                {{ $ward->full_name ?? $ward->name }}
                                @if($ward->class?->name)
                                    - {{ $ward->class->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="days" class="form-label fw-semibold">{{ __('Preset range') }}</label>
                    <select name="days" id="days" class="form-select" onchange="this.form.submit()">
                        @foreach($presetRanges as $value => $label)
                            <option value="{{ $value }}" {{ $rangeDays === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label for="start_date" class="form-label fw-semibold">{{ __('From') }}</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control" onchange="this.form.submit()">
                </div>
                <div class="col-6 col-md-3">
                    <label for="end_date" class="form-label fw-semibold">{{ __('To') }}</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control" onchange="this.form.submit()">
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="small text-white-75 mb-1">{{ __('Attendance rate') }}</div>
                    <div class="display-6 fw-bold">{{ $summary['percentage'] !== null ? $summary['percentage'] . '%' : __('N/A') }}</div>
                    <div class="small text-white-75">{{ __('Across :count recorded days', ['count' => $summary['total_days']]) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-muted">{{ __('Present / Late') }}</span>
                        <span class="badge bg-success bg-opacity-10 text-success">{{ $summary['present'] + $summary['late'] }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        @php $positivePercent = $summary['total_days'] > 0 ? min(100, round((($summary['present'] + $summary['late']) / $summary['total_days']) * 100)) : 0; @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $positivePercent }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-2">{{ __('Present: :present · Late: :late', ['present' => $summary['present'], 'late' => $summary['late']]) }}</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-muted">{{ __('Absent / Excused') }}</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger">{{ $summary['absent'] + $summary['excused'] }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        @php $negativePercent = $summary['total_days'] > 0 ? min(100, round((($summary['absent'] + $summary['excused']) / $summary['total_days']) * 100)) : 0; @endphp
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $negativePercent }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-2">{{ __('Absent: :absent · Excused: :excused', ['absent' => $summary['absent'], 'excused' => $summary['excused']]) }}</small>
                </div>
            </div>
        </div>
    </div>

    @if(!$selectedWardUser)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-25 p-3 text-warning">
                        <i class="fas fa-info"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ __('Attendance data not linked yet') }}</h5>
                        <p class="text-muted mb-0">{{ __('This student does not have a connected portal account, so attendance records are not available online yet. Please contact the school if you need assistance.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($attendanceRecords->isEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('No attendance recorded in this period') }}</h5>
                <p class="text-muted mb-0">{{ __('Try expanding the date range or check back after teachers mark attendance for upcoming days.') }}</p>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-12 col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-check me-2 text-success"></i>{{ __('Daily attendance log') }}</h5>
                        <span class="badge bg-light text-dark">{{ $attendanceRecords->count() }} {{ __('entries') }}</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Class') }}</th>
                                        <th>{{ __('Marked by') }}</th>
                                        <th class="text-end">{{ __('Notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceRecords as $record)
                                        @php
                                            $statusBadge = match($record->status) {
                                                'present' => 'success',
                                                'late' => 'warning',
                                                'excused' => 'info',
                                                'absent' => 'danger',
                                                default => 'secondary',
                                            };
                                            $statusLabel = match($record->status) {
                                                'present' => __('Present'),
                                                'late' => __('Late'),
                                                'excused' => __('Excused'),
                                                'absent' => __('Absent'),
                                                default => ucfirst($record->status),
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $record->attendance_date->format('M j, Y') }}</td>
                                            <td><span class="badge bg-{{ $statusBadge }} bg-opacity-10 text-{{ $statusBadge }} fw-semibold">{{ $statusLabel }}</span></td>
                                            <td>{{ $record->class?->name ?? __('Class update pending') }}</td>
                                            <td>{{ $record->markedBy?->name ?? __('System') }}</td>
                                            <td class="text-end">{{ $record->notes ? \Illuminate\Support\Str::limit($record->notes, 40) : '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-success"></i>{{ __('10-day trend') }}</h5>
                        <span class="badge bg-light text-dark">{{ __('Most recent first') }}</span>
                    </div>
                    <div class="card-body">
                        @if($trend->isEmpty())
                            <div class="text-center py-4 text-muted">{{ __('Trend data will appear as soon as attendance records exist.') }}</div>
                        @else
                            <div class="d-grid gap-3">
                                @foreach($trend->sortByDesc(fn ($item) => $item['date'])->take(10) as $snapshot)
                                    @php
                                        $dayPercent = $snapshot['percentage'];
                                        $label = $snapshot['date']->format('M j, Y');
                                    @endphp
                                    <div class="border rounded-3 p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong>{{ $label }}</strong>
                                                <div class="small text-muted">{{ __('Present/Late: :present | Absent: :absent | Excused: :excused', [
                                                    'present' => $snapshot['present'] + $snapshot['late'],
                                                    'absent' => $snapshot['absent'],
                                                    'excused' => $snapshot['excused'],
                                                ]) }}</div>
                                            </div>
                                            <span class="badge bg-success bg-opacity-10 text-success">{{ $dayPercent }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $dayPercent) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
