@extends('layouts.tenant.student')

@section('title', 'Exams')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-journal-check me-2"></i>{{ __('Exams') }}
            </h4>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($exams->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5>{{ __('No Exams Available') }}</h5>
                    <p class="text-muted">{{ __('There are no exams scheduled for your class at the moment.') }}</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach ($exams as $exam)
                    @php
                        $attempt = $attempts->get($exam->id);
                        $status = 'upcoming';
                        $statusClass = 'secondary';
                        $now = now();

                        if ($attempt) {
                            if ($attempt->completed_at) {
                                $status = 'completed';
                                $statusClass = 'success';
                            } else {
                                $status = 'in_progress';
                                $statusClass = 'warning';
                            }
                        } elseif ($now->between($exam->starts_at, $exam->ends_at)) {
                            $status = 'active';
                            $statusClass = 'primary';
                        } elseif ($now->gt($exam->ends_at)) {
                            $status = 'missed';
                            $statusClass = 'danger';
                        }
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span
                                        class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                    <small class="text-muted">{{ $exam->duration_minutes }} mins</small>
                                </div>
                                <h5 class="card-title mb-2">{{ $exam->title }}</h5>
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($exam->description, 100) }}
                                </p>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center text-muted small mb-1">
                                        <i class="bi bi-calendar-event me-2"></i>
                                        {{ $exam->starts_at->format('M d, Y h:i A') }}
                                    </div>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-clock me-2"></i>
                                        Due: {{ $exam->ends_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <a href="{{ route('tenant.student.exams.show', $exam) }}"
                                    class="btn btn-outline-primary w-100">
                                    {{ $status === 'completed' ? 'View Results' : 'View Details' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
@endsection
