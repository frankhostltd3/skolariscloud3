@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', __('Class Students') . ' - ' . $class->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $class->name }}</h1>
            <p class="text-muted mb-0">
                {{ __('Students Overview') }} &middot; {{ __('Total Students:') }} {{ $students->count() }}
            </p>
        </div>
        <div>
            <a href="{{ route('tenant.teacher.classes.show', $class) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Class') }}
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people me-2"></i>{{ __('Students in this Class') }}
                </h5>
                <span class="badge bg-primary">{{ $students->count() }} {{ __('Students') }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($students->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-info-circle me-2"></i>{{ __('No students enrolled yet.') }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Student') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Average Grade') }}</th>
                                <th>{{ __('Grades Recorded') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                <span class="fw-semibold">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $student->name }}</div>
                                                <div class="text-muted small">
                                                    {{ $student->class?->name ?? __('No profile class assigned') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->email ?? __('N/A') }}</td>
                                    <td>{{ $student->phone ?? __('N/A') }}</td>
                                    <td>
                                        @if(!is_null($student->averageGrade))
                                            <span class="badge bg-{{ $student->averageGrade >= 70 ? 'success' : ($student->averageGrade >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($student->averageGrade, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('No grades yet') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $student->gradeCount }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

