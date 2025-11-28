@extends('layouts.tenant.student')

@section('title', $class->title)

@section('content')
    <div class="container-fluid py-4">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('tenant.student.classroom.virtual.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Classes
            </a>
        </div>

        @php
            $now = \Carbon\Carbon::now();
            $isOngoing = $class->status === 'ongoing';
            $isUpcoming = $class->status === 'scheduled' && $now->isBefore($class->start_time);
            $isCompleted = $class->status === 'completed';
            $isCancelled = $class->status === 'cancelled';
            $canJoin = $isOngoing || ($isUpcoming && $now->diffInMinutes($class->start_time, false) <= 15);
            $minutesUntilStart = $now->diffInMinutes($class->start_time, false);

            // Get student's attendance
$attendance = $class->attendances->where('student_id', auth()->id())->first();
        @endphp

        <div class="row">
            <!-- Left Column: Class Details -->
            <div class="col-lg-8">
                <!-- Main Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="flex-grow-1">
                                <h2 class="mb-2">{{ $class->title }}</h2>
                                <div class="d-flex gap-3 flex-wrap text-muted small">
                                    <span>
                                        <i class="bi bi-book me-1"></i>{{ $class->subject->name }}
                                    </span>
                                    <span>
                                        <i class="bi bi-diagram-3 me-1"></i>{{ $class->class->name }}
                                    </span>
                                    <span>
                                        <i class="bi bi-person me-1"></i>{{ $class->teacher->name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Alert -->
                        @if ($isCancelled)
                            <div class="alert alert-secondary d-flex align-items-center">
                                <i class="bi bi-x-circle-fill me-3 fs-4"></i>
                                <div>
                                    <h5 class="mb-1">Class Cancelled</h5>
                                    <p class="mb-0">This class has been cancelled by the teacher.</p>
                                    @if ($class->cancellation_reason)
                                        <p class="mb-0 mt-2"><strong>Reason:</strong> {{ $class->cancellation_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        @elseif($isOngoing)
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="bi bi-broadcast me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Class is Live Now!</h5>
                                    <p class="mb-0">The class is currently in progress. Join now to participate.</p>
                                </div>
                                <a href="{{ $class->meeting_url }}" target="_blank" class="btn btn-success btn-lg">
                                    <i class="bi bi-box-arrow-up-right me-2"></i> Join Now
                                </a>
                            </div>
                        @elseif($canJoin && $isUpcoming)
                            <div class="alert alert-primary d-flex align-items-center">
                                <i class="bi bi-clock-fill me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">Class Starting Soon</h5>
                                    <p class="mb-0">Class starts {{ $class->start_time->diffForHumans() }}. You can join
                                        now.</p>
                                </div>
                                <a href="{{ $class->meeting_url }}" target="_blank" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-up-right me-2"></i> Join Class
                                </a>
                            </div>
                        @elseif($isUpcoming)
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                                <div>
                                    <h5 class="mb-1">Upcoming Class</h5>
                                    <p class="mb-0">This class starts {{ $class->start_time->diffForHumans() }}. Join link
                                        will be available 15 minutes before start time.</p>
                                </div>
                            </div>
                        @elseif($isCompleted)
                            @if ($attendance)
                                <div
                                    class="alert alert-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger') }} d-flex align-items-center">
                                    <i
                                        class="bi bi-{{ $attendance->status === 'present' ? 'check-circle-fill' : ($attendance->status === 'late' ? 'clock-fill' : 'x-circle-fill') }} me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-1">Class Completed - {{ ucfirst($attendance->status) }}</h5>
                                        <p class="mb-0">
                                            @if ($attendance->status === 'present')
                                                You attended this class on time.
                                            @elseif($attendance->status === 'late')
                                                You joined this class late ({{ $attendance->late_by }} minutes).
                                            @else
                                                You did not attend this class.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="bi bi-x-circle-fill me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-1">Class Completed - Absent</h5>
                                        <p class="mb-0">You did not attend this class.</p>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Description -->
                        @if ($class->description)
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Description</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($class->description)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Meeting Information -->
                        @if (!$isCancelled)
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="bi bi-link-45deg me-2"></i>Meeting Information</h5>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block mb-1">Platform</small>
                                                <strong>{{ ucfirst($class->platform) }}</strong>
                                            </div>
                                            @if ($class->meeting_id)
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block mb-1">Meeting ID</small>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <strong>{{ $class->meeting_id }}</strong>
                                                        <button class="btn btn-sm btn-outline-secondary"
                                                            onclick="copyToClipboard('{{ $class->meeting_id }}')">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($class->meeting_password)
                                                <div class="col-md-6">
                                                    <small class="text-muted d-block mb-1">Password</small>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <strong>{{ $class->meeting_password }}</strong>
                                                        <button class="btn btn-sm btn-outline-secondary"
                                                            onclick="copyToClipboard('{{ $class->meeting_password }}')">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                <small class="text-muted d-block mb-1">Meeting URL</small>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="text" class="form-control"
                                                        value="{{ $class->meeting_url }}" readonly>
                                                    <button class="btn btn-outline-secondary"
                                                        onclick="copyToClipboard('{{ $class->meeting_url }}')">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($canJoin)
                                            <div class="mt-4">
                                                <a href="{{ $class->meeting_url }}" target="_blank"
                                                    class="btn btn-{{ $isOngoing ? 'success' : 'primary' }} btn-lg w-100">
                                                    <i class="bi bi-box-arrow-up-right me-2"></i>
                                                    {{ $isOngoing ? 'Join Live Class' : 'Join Class' }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Recording -->
                        @if ($isCompleted && $class->recording_url)
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="bi bi-play-circle me-2"></i>Class Recording</h5>
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                                <i class="bi bi-play-circle-fill text-success fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong class="d-block">Recording Available</strong>
                                                <small class="text-muted">Watch the recorded session</small>
                                            </div>
                                            <a href="{{ $class->recording_url }}" target="_blank"
                                                class="btn btn-success">
                                                <i class="bi bi-play-circle me-1"></i> Watch Recording
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Countdown Timer (for upcoming classes) -->
                @if ($isUpcoming && $minutesUntilStart > 15)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-4">
                            <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i>Time Until Class Starts</h5>
                            <div id="countdown" class="d-flex justify-content-center gap-4 mb-3">
                                <div>
                                    <h2 class="mb-0 text-primary" id="days">00</h2>
                                    <small class="text-muted">Days</small>
                                </div>
                                <div>
                                    <h2 class="mb-0 text-primary" id="hours">00</h2>
                                    <small class="text-muted">Hours</small>
                                </div>
                                <div>
                                    <h2 class="mb-0 text-primary" id="minutes">00</h2>
                                    <small class="text-muted">Minutes</small>
                                </div>
                                <div>
                                    <h2 class="mb-0 text-primary" id="seconds">00</h2>
                                    <small class="text-muted">Seconds</small>
                                </div>
                            </div>
                            <p class="text-muted mb-0">Join link will be available 15 minutes before start time</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Summary -->
            <div class="col-lg-4">
                <!-- Schedule Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Schedule</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Date</small>
                            <strong>{{ optional($class->start_time)->format('l, M d, Y') ?? 'N/A' }}</strong>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">Start Time</small>
                            <strong>{{ optional($class->start_time)->format('h:i A') ?? 'N/A' }}</strong>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted d-block mb-1">End Time</small>
                            <strong>{{ optional($class->end_time)->format('h:i A') ?? 'N/A' }}</strong>
                        </div>
                        @if ($class->duration)
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Duration</small>
                                <strong>{{ $class->duration }} minutes</strong>
                            </div>
                        @endif
                        <div>
                            <small class="text-muted d-block mb-1">Status</small>
                            @if ($isCancelled)
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Cancelled
                                </span>
                            @elseif($isOngoing)
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-broadcast me-1"></i> Live Now
                                </span>
                            @elseif($isUpcoming)
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="bi bi-clock me-1"></i> Upcoming
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Completed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Teacher Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0"><i class="bi bi-person me-2"></i>Teacher</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-person-fill text-primary fs-4"></i>
                            </div>
                            <div>
                                <strong class="d-block">{{ $class->teacher->name }}</strong>
                                <small class="text-muted">{{ $class->subject->name }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Status -->
                @if ($attendance)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0"><i class="bi bi-check-square me-2"></i>My Attendance</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-3">
                                    <span
                                        class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : 'danger') }} px-4 py-3 fs-6">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </div>
                                @if ($attendance->joined_at)
                                    <small class="text-muted d-block">
                                        Joined at {{ $attendance->joined_at->format('h:i A') }}
                                    </small>
                                @endif
                                @if ($attendance->late_by)
                                    <small class="text-warning d-block mt-1">
                                        {{ $attendance->late_by }} minutes late
                                    </small>
                                @endif
                                @if ($attendance->remarks)
                                    <div class="alert alert-light mt-3 mb-0">
                                        <small>{{ $attendance->remarks }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Copy to clipboard function
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    alert('Copied to clipboard!');
                });
            }

            // Countdown timer for upcoming classes
            @if ($isUpcoming && $minutesUntilStart > 15)
                const countdownDate = new Date("{{ $class->start_time->format('Y-m-d H:i:s') }}").getTime();

                const x = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = countdownDate - now;

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                    document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                    document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                    document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');

                    if (distance < 900000) { // 15 minutes
                        clearInterval(x);
                        location.reload(); // Reload to show join button
                    }
                }, 1000);
            @endif
        </script>
    @endpush
@endsection
