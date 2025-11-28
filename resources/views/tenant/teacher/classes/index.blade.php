@extends('layouts.dashboard-teacher')

@section('title', 'My Classes')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">{{ __('My Classes') }}</h1>
    <span class="badge bg-primary-subtle text-primary border border-primary">
        {{ $classes->count() }} {{ Str::plural('Class', $classes->count()) }}
    </span>
</div>

@if($classes->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="bi bi-journal-bookmark text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted mb-2">{{ __('No Classes Assigned') }}</h5>
            <p class="text-muted mb-0">{{ __('You have not been assigned to any classes yet.') }}</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($classes as $class)
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1 fw-semibold">
                                {{ $class->name }}
                            </h5>
                            @if($class->section)
                                <small class="text-muted">{{ __('Section') }}: {{ $class->section }}</small>
                            @endif
                        </div>
                        @if($class->class_teacher_id === auth()->id())
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-star-fill"></i> {{ __('Class Teacher') }}
                            </span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info">
                                <i class="bi bi-book"></i> {{ __('Subject Teacher') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted small">
                                <i class="bi bi-people me-1"></i>{{ __('Students') }}
                            </span>
                            <span class="fw-semibold">{{ $class->students_count ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted small">
                                <i class="bi bi-book me-1"></i>{{ __('Subjects') }}
                            </span>
                            <span class="fw-semibold">{{ $class->subjects_count ?? 0 }}</span>
                        </div>
                        @if($class->room_number)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <i class="bi bi-door-open me-1"></i>{{ __('Room') }}
                            </span>
                            <span class="fw-semibold">{{ $class->room_number }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.teacher.classes.show', $class->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>
                            <i class="bi bi-calendar-event me-1"></i>
                            @if($class->academicYear)
                                {{ $class->academicYear->name ?? __('No Academic Year') }}
                            @else
                                {{ __('No Academic Year') }}
                            @endif
                        </span>
                        @if($class->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

<style>
.hover-lift {
    transition: all 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}
</style>
@endsection
