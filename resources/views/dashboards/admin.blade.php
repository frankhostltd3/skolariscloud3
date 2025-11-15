@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('Admin dashboard') }}</h1>
        <span class="badge text-bg-light text-secondary">{{ $school->name ?? 'Workspace' }}</span>
    </div>

    {{-- Critical Alerts --}}
    @if (count($criticalAlerts ?? []) > 0)
        <div class="row mb-3">
            <div class="col-12">
                @foreach ($criticalAlerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show d-flex justify-content-between align-items-center"
                        role="alert">
                        <div>
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>{{ $alert['message'] }}</strong>
                        </div>
                        <a href="{{ $alert['action'] }}" class="btn btn-sm btn-{{ $alert['type'] }}">
                            {{ $alert['action_text'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Active students') }}</div>
                    <div class="display-6">{{ number_format($activeStudentsCount ?? 0) }}</div>
                    @if (($recentEnrollments ?? 0) > 0)
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> {{ $recentEnrollments }} {{ __('this month') }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Staff members') }}</div>
                    <div class="display-6">{{ number_format($staffCount ?? 0) }}</div>
                    <small class="text-muted">{{ __('Teachers & Staff') }}</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Attendance today') }}</div>
                    <div class="display-6">{{ $attendancePercentage ?? 0 }}%</div>
                    <small class="text-muted">
                        @if (($attendancePercentage ?? 0) >= 90)
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> {{ __('Excellent') }}</span>
                        @elseif(($attendancePercentage ?? 0) >= 75)
                            <span class="text-info"><i class="bi bi-info-circle-fill"></i> {{ __('Good') }}</span>
                        @else
                            <span class="text-danger"><i class="bi bi-exclamation-circle-fill"></i>
                                {{ __('Needs attention') }}</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Fees outstanding') }}</div>
                    <div class="display-6">${{ number_format($feesOutstanding ?? 0, 0) }}</div>
                    <small class="text-muted">{{ __('Pending payments') }}</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Exam body') }}</div>
                    <div class="fw-semibold">{{ __('Not set') }}</div>
                    <a class="small d-inline-block mt-1" href="#">{{ __('Set now') }}</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Total classes') }}</div>
                    <div class="display-6">{{ number_format($totalClasses ?? 0) }}</div>
                    <small class="text-muted">{{ __('Active classes') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 fw-semibold mb-3">{{ __('Quick links') }}</h2>
            <div class="d-flex flex-wrap gap-2 small">
                <a class="btn btn-outline-primary btn-sm" href="#">{{ __('Fees') }}</a>
                <a class="btn btn-outline-secondary btn-sm" href="#">{{ __('Add new teacher') }}</a>
                <a class="btn btn-outline-secondary btn-sm" href="#">{{ __('Add new student') }}</a>
                <a class="btn btn-outline-secondary btn-sm" href="#">{{ __('Add new subject') }}</a>
                <a class="btn btn-outline-secondary btn-sm" href="#">{{ __('Create a new class') }}</a>
                <a class="btn btn-outline-secondary btn-sm" href="#">{{ __('Create a new class stream') }}</a>
            </div>
        </div>
    </div>

    {{-- Weekly Attendance Chart --}}
    @if (count($weeklyAttendance ?? []) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up text-primary me-2"></i>{{ __('Weekly Attendance Trend') }}
                        </h5>
                        <small class="text-muted">{{ __('Last 7 days') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th>{{ __('Day') }}</th>
                                        @foreach ($weeklyAttendance as $day)
                                            <th class="text-center">{{ $day['day'] }}<br><small
                                                    class="text-muted">{{ $day['date'] }}</small></th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">{{ __('Attendance') }}</td>
                                        @foreach ($weeklyAttendance as $day)
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $day['percentage'] >= 90 ? 'success' : ($day['percentage'] >= 75 ? 'warning' : 'danger') }}">
                                                    {{ $day['percentage'] }}%
                                                </span>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $day['present'] }}/{{ $day['total'] }}</small>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Top Classes by Student Count --}}
    @if (count($studentsByClass ?? []) > 0)
        <div class="row mb-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-pie-chart text-success me-2"></i>{{ __('Top Classes') }}
                        </h5>
                        <small class="text-muted">{{ __('By student enrollment') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach ($studentsByClass as $class)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <strong>{{ $class->name }}</strong>
                                        @if ($class->section ?? null)
                                            <small class="text-muted">{{ $class->section }}</small>
                                        @endif
                                    </div>
                                    <span
                                        class="badge bg-primary rounded-pill">{{ $class->active_students_count ?? 0 }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shop text-warning me-2"></i>{{ __('Bookstore') }}
                        </h5>
                        <small class="text-muted">{{ __('Recent orders & inventory') }}</small>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">{{ __('Bookstore coming soon') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Activity --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history text-info me-2"></i>{{ __('Recent Activity') }}
            </h5>
            <small class="text-muted">{{ __('Latest updates from your school') }}</small>
        </div>
        <div class="card-body">
            @if (count($recentActivities ?? []) > 0)
                <div class="activity-list">
                    @foreach ($recentActivities as $activity)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="rounded-circle bg-{{ $activity['color'] }} bg-opacity-10 p-2">
                                    <i class="bi bi-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $activity['message'] }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $activity['time']->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">{{ __('No recent activity') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
