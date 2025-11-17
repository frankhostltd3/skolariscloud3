@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', "Today's Schedule")

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h4 fw-semibold mb-1">{{ __("Today's Schedule") }}</h1>
        <p class="text-muted mb-0">
            <i class="bi bi-calendar-day me-1"></i>{{ now()->format('l, F d, Y') }}
            <span class="badge bg-primary ms-2">{{ $todayCode }}</span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tenant.teacher.timetable.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Timetable') }}
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-printer me-1"></i>{{ __('Print') }}
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Current Time Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-primary">
            <div class="card-body text-center py-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <i class="bi bi-clock text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mb-0 fw-bold mt-2" id="current-time">{{ now()->format('H:i:s') }}</h2>
                        <p class="text-muted mb-0">{{ __('Current Time') }}</p>
                    </div>
                    <div class="col-md-4">
                        <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                        <h2 class="mb-0 fw-bold mt-2">{{ count($events) }}</h2>
                        <p class="text-muted mb-0">{{ __('Classes Today') }}</p>
                    </div>
                    <div class="col-md-4">
                        <i class="bi bi-journal-bookmark text-info" style="font-size: 3rem;"></i>
                        <h2 class="mb-0 fw-bold mt-2">{{ now()->format('l') }}</h2>
                        <p class="text-muted mb-0">{{ __('Day of Week') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(empty($events))
    <!-- No Classes Today -->
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="bi bi-calendar-x text-muted" style="font-size: 5rem;"></i>
            </div>
            <h4 class="text-muted mb-2">{{ __('No Classes Today') }}</h4>
            <p class="text-muted mb-4">{{ __('You have no scheduled classes for today. Enjoy your free day!') }}</p>
            <a href="{{ route('tenant.teacher.timetable.index') }}" class="btn btn-primary">
                <i class="bi bi-calendar-week me-2"></i>{{ __('View Full Timetable') }}
            </a>
        </div>
    </div>
@else
    <!-- Today's Schedule -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check text-primary me-2"></i>{{ __('Schedule Timeline') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            $currentTime = now()->format('H:i');
                        @endphp
                        @foreach($events as $index => $event)
                            @php
                                $startTime = \Carbon\Carbon::parse($event['start'])->format('H:i');
                                $endTime = \Carbon\Carbon::parse($event['end'])->format('H:i');
                                $isNow = $currentTime >= $startTime && $currentTime <= $endTime;
                                $isPast = $currentTime > $endTime;
                                $isUpcoming = $currentTime < $startTime;
                                
                                $statusClass = $isNow ? 'border-success bg-success-subtle' : ($isPast ? 'border-secondary' : 'border-primary');
                                $statusBadge = $isNow ? 'success' : ($isPast ? 'secondary' : 'primary');
                                $statusText = $isNow ? __('In Progress') : ($isPast ? __('Completed') : __('Upcoming'));
                            @endphp
                            <div class="list-group-item {{ $statusClass }} border-start border-4">
                                <div class="row align-items-center">
                                    <!-- Time Column -->
                                    <div class="col-md-2 text-center">
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-{{ $statusBadge }} mb-1">{{ $startTime }}</span>
                                            <small class="text-muted">to</small>
                                            <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }} mt-1">{{ $endTime }}</span>
                                        </div>
                                        @if($isNow)
                                            <span class="badge bg-success mt-2 pulse-badge">
                                                <i class="bi bi-broadcast me-1"></i>{{ __('LIVE') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Class Info Column -->
                                    <div class="col-md-6">
                                        <h5 class="mb-1 fw-semibold">
                                            <i class="bi bi-book me-2 text-primary"></i>{{ $event['subject']->name }}
                                        </h5>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <span class="badge bg-info-subtle text-info">
                                                <i class="bi bi-journal-bookmark me-1"></i>{{ $event['class']->name }}
                                                @if($event['class']->section)
                                                    - {{ $event['class']->section }}
                                                @endif
                                            </span>
                                            @if($event['room'])
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    <i class="bi bi-door-open me-1"></i>{{ __('Room') }} {{ $event['room'] }}
                                                </span>
                                            @endif
                                            @if($event['subject']->code)
                                                <small class="text-muted">{{ $event['subject']->code }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Duration & Status Column -->
                                    <div class="col-md-2 text-center">
                                        @php
                                            $start = \Carbon\Carbon::parse($event['start']);
                                            $end = \Carbon\Carbon::parse($event['end']);
                                            $duration = $start->diffInMinutes($end);
                                        @endphp
                                        <div class="mb-2">
                                            <i class="bi bi-hourglass-split text-muted"></i>
                                            <div class="fw-semibold">{{ $duration }} {{ __('min') }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions Column -->
                                    <div class="col-md-2 text-end">
                                        <span class="badge bg-{{ $statusBadge }} px-3 py-2">
                                            {{ $statusText }}
                                        </span>
                                        @if($isNow || $isUpcoming)
                                            <div class="mt-2">
                                                <a href="{{ route('tenant.teacher.attendance.take', ['class_id' => $event['class']->id]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check2-square me-1"></i>{{ __('Attendance') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mt-2">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="bi bi-calendar-check-fill text-primary" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2 mb-0">{{ count($events) }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Classes') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="bi bi-hourglass-split text-success" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2 mb-0">
                        @php
                            $totalMinutes = 0;
                            foreach($events as $evt) {
                                $s = \Carbon\Carbon::parse($evt['start']);
                                $e = \Carbon\Carbon::parse($evt['end']);
                                $totalMinutes += $s->diffInMinutes($e);
                            }
                            $hours = floor($totalMinutes / 60);
                            $mins = $totalMinutes % 60;
                        @endphp
                        {{ $hours }}h {{ $mins }}m
                    </h3>
                    <p class="text-muted mb-0 small">{{ __('Total Duration') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="bi bi-door-open-fill text-info" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2 mb-0">
                        {{ count(array_unique(array_column($events, 'room'))) }}
                    </h3>
                    <p class="text-muted mb-0 small">{{ __('Different Rooms') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="bi bi-journal-bookmark-fill text-warning" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2 mb-0">
                        {{ count(array_unique(array_column(array_column($events, 'class'), 'id'))) }}
                    </h3>
                    <p class="text-muted mb-0 small">{{ __('Different Classes') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.pulse-badge {
    animation: pulse 2s ease-in-out infinite;
}
@media print {
    .btn, .card-header, .no-print {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
}
</style>
<script>
// Update current time every second
setInterval(function() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}, 1000);

// Auto-refresh page every 5 minutes to update status
setTimeout(function() {
    window.location.reload();
}, 300000);
</script>
@endpush

