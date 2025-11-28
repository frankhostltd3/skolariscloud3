@extends('layouts.dashboard-teacher')

@section('title', 'My Subjects')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">{{ __('My Subjects') }}</h1>
    <span class="badge bg-success-subtle text-success border border-success">
        {{ $subjects->count() }} {{ Str::plural('Subject', $subjects->count()) }}
    </span>
</div>

@if($subjects->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted mb-2">{{ __('No Subjects') }}</h5>
            <p class="text-muted mb-0">{{ __('You have not been assigned to teach any subjects yet.') }}</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($subjects as $subject)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-semibold">
                                <i class="bi bi-book text-success me-2"></i>{{ $subject->name }}
                                @if($subject->code)
                                    <span class="text-muted">- {{ $subject->code }}</span>
                                @endif
                            </h5>
                            <small class="text-muted">
                                <i class="bi bi-journal-bookmark me-1"></i>{{ $subject->classes_count ?? 0 }} {{ Str::plural('Class', $subject->classes_count ?? 0) }}
                                @if($subject->description)
                                    <span class="ms-2"><i class="bi bi-info-circle me-1"></i>{{ Str::limit($subject->description, 50) }}</span>
                                @endif
                            </small>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check-circle-fill me-1"></i>{{ __('Assigned') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($subject->classes && $subject->classes->count() > 0)
                        <div class="row g-3">
                            @foreach($subject->classes as $class)
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center p-3 border rounded hover-bg">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <span class="text-success fw-bold">{{ strtoupper(substr($class->name ?? 'C', 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $class->name }}</div>
                                        <small class="text-muted">
                                            @if($class->class_teacher_id === auth()->id())
                                                <span class="badge bg-success-subtle text-success border border-success me-1">
                                                    <i class="bi bi-star-fill"></i> {{ __('Class Teacher') }}
                                                </span>
                                            @endif
                                            <i class="bi bi-people me-1"></i>{{ $class->students_count ?? 0 }} {{ Str::plural('Student', $class->students_count ?? 0) }}
                                        </small>
                                    </div>
                                    <a href="{{ route('tenant.teacher.classes.show', $class->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-inbox me-2"></i>{{ __('No classes assigned for this subject') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light border-0">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">{{ __('Classes') }}</div>
                            <div class="fw-semibold">{{ $subject->classes_count ?? 0 }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">{{ __('Total Students') }}</div>
                            <div class="fw-semibold">{{ $subject->classes->sum('students_count') ?? 0 }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">{{ __('Subject Type') }}</div>
                            <div class="fw-semibold">
                                @if($subject->is_core)
                                    {{ __('Core') }}
                                @else
                                    {{ __('Optional') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary Card -->
    <div class="card shadow-sm mt-4 border-success">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="mb-2">
                        <i class="bi bi-book-fill text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $subjects->count() }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Subjects') }}</p>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <i class="bi bi-journal-bookmark-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $subjects->sum('classes_count') }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Classes') }}</p>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <i class="bi bi-people-fill text-info" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $subjects->sum(function($subject) { return $subject->classes->sum('students_count'); }) }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Students') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
.hover-bg {
    transition: all 0.2s ease;
}
.hover-bg:hover {
    background-color: #f8f9fa;
}
</style>
@endsection