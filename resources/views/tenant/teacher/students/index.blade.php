@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'My Students')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">{{ __('My Students') }}</h1>
    <span class="badge bg-primary-subtle text-primary border border-primary">
        {{ $classes->count() }} {{ Str::plural('Class', $classes->count()) }}
    </span>
</div>

@if($classes->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted mb-2">{{ __('No Students') }}</h5>
            <p class="text-muted mb-0">{{ __('You have not been assigned to any classes yet.') }}</p>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($classes as $class)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-semibold">
                                <i class="bi bi-journal-bookmark text-primary me-2"></i>{{ $class->name }}
                                @if($class->section)
                                    <span class="text-muted">- {{ $class->section }}</span>
                                @endif
                            </h5>
                            <small class="text-muted">
                                @if($class->class_teacher_id === auth()->id())
                                    <span class="badge bg-success-subtle text-success border border-success me-2">
                                        <i class="bi bi-star-fill"></i> {{ __('Class Teacher') }}
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info me-2">
                                        <i class="bi bi-book"></i> {{ __('Subject Teacher') }}
                                    </span>
                                @endif
                                <i class="bi bi-people me-1"></i>{{ $class->students_count ?? 0 }} {{ Str::plural('Student', $class->students_count ?? 0) }}
                                @if($class->room_number)
                                    <span class="ms-2"><i class="bi bi-door-open me-1"></i>{{ __('Room') }} {{ $class->room_number }}</span>
                                @endif
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('tenant.teacher.classes.show', $class->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>{{ __('View Class') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($class->students && $class->students->count() > 0)
                        <div class="row g-3">
                            @foreach($class->students->take(6) as $student)
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center p-3 border rounded hover-bg">
                                    <div class="me-3">
                                        @if($student->profile_photo)
                                            <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <span class="text-primary fw-bold">{{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $student->name }}</div>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                    <a href="{{ route('tenant.teacher.students.show', ['student' => $student->id, 'class_id' => $class->id]) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($class->students->count() > 6)
                        <div class="text-center mt-3">
                            <a href="{{ route('tenant.teacher.classes.students', $class->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-people me-1"></i>{{ __('View All') }} {{ $class->students->count() }} {{ __('Students') }}
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-inbox me-2"></i>{{ __('No students enrolled in this class') }}
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light border-0">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">{{ __('Students') }}</div>
                            <div class="fw-semibold">{{ $class->students_count ?? 0 }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">{{ __('Subjects') }}</div>
                            <div class="fw-semibold">{{ $class->subjects_count ?? 0 }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">{{ __('Academic Year') }}</div>
                            <div class="fw-semibold">
                                @if($class->academicYear)
                                    {{ $class->academicYear->name ?? '-' }}
                                @else
                                    -
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
    <div class="card shadow-sm mt-4 border-primary">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="mb-2">
                        <i class="bi bi-journal-bookmark-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $classes->count() }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Classes') }}</p>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <i class="bi bi-people-fill text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $classes->sum('students_count') }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Students') }}</p>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <i class="bi bi-book-fill text-info" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $classes->sum('subjects_count') }}</h3>
                    <p class="text-muted mb-0 small">{{ __('Total Subjects') }}</p>
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

