@extends('layouts.tenant.student')

@section('title', 'Today\'s Classes')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-calendar-day me-2"></i>Today's Classes
                </h2>
                <p class="text-muted mb-0">
                    {{ now()->format('l, F j, Y') }}
                </p>
            </div>
            <a href="{{ route('tenant.student.classroom.virtual.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to All Classes
            </a>
        </div>

        <!-- Classes List -->
        @if ($classes->count() > 0)
            <div class="row g-4">
                @foreach ($classes as $class)
                    @php
                        $now = \Carbon\Carbon::now();
                        $isOngoing = $class->status === 'ongoing';
                        $isUpcoming = $class->status === 'scheduled';
                        $canJoin =
                            $isOngoing || ($isUpcoming && $now->diffInMinutes($class->scheduled_at, false) <= 15);
                    @endphp

                    <div class="col-12">
                        <div
                            class="card border-0 shadow-sm hover-card {{ $isOngoing ? 'border-start border-success border-5' : '' }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <!-- Title & Meta -->
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div
                                                class="bg-{{ $isOngoing ? 'success' : 'primary' }} bg-opacity-10 p-3 rounded">
                                                <i
                                                    class="bi bi-camera-video text-{{ $isOngoing ? 'success' : 'primary' }} fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $class->title }}</h5>
                                                <p class="text-muted mb-2 small">
                                                    <i class="bi bi-book me-1"></i>{{ $class->subject->name }} •
                                                    <i class="bi bi-diagram-3 ms-2 me-1"></i>{{ $class->class->name }} •
                                                    <i class="bi bi-person ms-2 me-1"></i>{{ $class->teacher->name }}
                                                </p>
                                                @if ($class->description)
                                                    <p class="text-muted small mb-0">
                                                        {{ Str::limit($class->description, 150) }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Schedule & Platform -->
                                        <div class="d-flex gap-3 flex-wrap">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $class->scheduled_at->format('h:i A') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-globe me-1"></i>
                                                {{ ucfirst($class->platform ?? 'Online') }}
                                            </small>
                                            @if ($class->duration)
                                                <small class="text-muted">
                                                    <i class="bi bi-hourglass me-1"></i>
                                                    {{ $class->duration }} min
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="text-end">
                                            <!-- Status Badge -->
                                            @if ($isOngoing)
                                                <span class="badge bg-success px-3 py-2 mb-3">
                                                    <i class="bi bi-broadcast me-1"></i>
                                                    Live Now
                                                </span>
                                            @else
                                                <span class="badge bg-primary px-3 py-2 mb-3">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Starts {{ $class->scheduled_at->diffForHumans() }}
                                                </span>
                                            @endif

                                            <!-- Action Button -->
                                            @if ($canJoin)
                                                <a href="{{ route('tenant.student.classroom.virtual.join', $class) }}"
                                                    class="btn btn-{{ $isOngoing ? 'success' : 'primary' }} d-block mb-2">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                                    {{ $isOngoing ? 'Join Now' : 'Join Class' }}
                                                </a>
                                            @else
                                                <a href="{{ route('tenant.student.classroom.virtual.show', $class) }}"
                                                    class="btn btn-outline-primary d-block">
                                                    <i class="bi bi-eye me-1"></i> View Details
                                                </a>
                                                <small class="text-muted d-block mt-2">
                                                    Link available 15 mins before start
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-check text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Classes Today</h4>
                    <p class="text-muted">You don't have any virtual classes scheduled for today.</p>
                    <a href="{{ route('tenant.student.classroom.virtual.index') }}" class="btn btn-primary mt-3">
                        View All Classes
                    </a>
                </div>
            </div>
        @endif
    </div>

    <style>
        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection
