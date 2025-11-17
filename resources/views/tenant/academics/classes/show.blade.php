@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ $class->name }}</h1>
            <div class="text-muted">
                @if ($class->code)
                    <span class="badge bg-secondary">{{ $class->code }}</span>
                @endif
                @if ($class->educationLevel)
                    <span class="badge bg-info">{{ $class->educationLevel->name }}</span>
                @endif
                <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-warning' }}">
                    {{ $class->is_active ? __('Active') : __('Inactive') }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('tenant.academics.classes.edit', $class) }}">
                <i class="bi bi-pencil me-1"></i>{{ __('Edit Class') }}
            </a>
            <a class="btn btn-outline-secondary" href="{{ url('/tenant/academics/classes') }}">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Classes') }}
            </a>
        </div>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    <div class="row g-4">
        {{-- Class Information --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Class Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Class Name') }}</label>
                            <p class="mb-0 fw-semibold">{{ $class->name }}</p>
                        </div>

                        @if ($class->code)
                            <div class="col-md-6">
                                <label class="text-muted small">{{ __('Class Code') }}</label>
                                <p class="mb-0 fw-semibold">{{ $class->code }}</p>
                            </div>
                        @endif

                        @if ($class->educationLevel)
                            <div class="col-md-6">
                                <label class="text-muted small">{{ __('Education Level') }}</label>
                                <p class="mb-0 fw-semibold">
                                    {{ $class->educationLevel->name }}
                                    @if ($class->educationLevel->min_grade && $class->educationLevel->max_grade)
                                        <span class="text-muted">({{ __('Years') }}
                                            {{ $class->educationLevel->min_grade }}-{{ $class->educationLevel->max_grade }})</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Capacity') }}</label>
                            <p class="mb-0">
                                <span class="fw-semibold">{{ $class->capacity ?? __('Not set') }}</span>
                                @if ($class->capacity)
                                    <span class="text-muted">{{ __('students max') }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Current Enrollment') }}</label>
                            <p class="mb-0">
                                <span class="fw-semibold">{{ $class->active_students_count ?? 0 }}</span>
                                <span class="text-muted">{{ __('students') }}</span>
                                @if ($class->capacity && $class->active_students_count)
                                    <span
                                        class="text-muted">({{ number_format(($class->active_students_count / $class->capacity) * 100, 1) }}%
                                        {{ __('full') }})</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Status') }}</label>
                            <p class="mb-0">
                                <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-warning' }}">
                                    {{ $class->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </p>
                        </div>

                        @if ($class->description)
                            <div class="col-12">
                                <label class="text-muted small">{{ __('Description') }}</label>
                                <p class="mb-0">{{ $class->description }}</p>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Created') }}</label>
                            <p class="mb-0 text-muted">{{ $class->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Last Updated') }}</label>
                            <p class="mb-0 text-muted">{{ $class->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Class Streams --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Class Streams') }}</h5>
                    <a href="{{ route('tenant.academics.streams.index', $class) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-diagram-3 me-1"></i>{{ __('Manage Streams') }}
                    </a>
                </div>
                <div class="card-body">
                    @if ($class->streams->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-diagram-3 text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">{{ __('No streams created yet.') }}</p>
                            <small
                                class="text-muted">{{ __('Streams allow you to divide classes into sections (e.g., Class A, Class B).') }}</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Stream Name') }}</th>
                                        <th>{{ __('Code') }}</th>
                                        <th>{{ __('Capacity') }}</th>
                                        <th>{{ __('Students') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($class->streams as $stream)
                                        <tr>
                                            <td class="fw-semibold">{{ $stream->name }}</td>
                                            <td>{{ $stream->code ?? '-' }}</td>
                                            <td>{{ $stream->capacity ?? '-' }}</td>
                                            <td>{{ $stream->active_students_count ?? 0 }}</td>
                                            <td>
                                                <span class="badge {{ $stream->is_active ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $stream->is_active ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('tenant.academics.streams.show', [$class, $stream]) }}"
                                                    class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                                                <a href="{{ route('tenant.academics.streams.edit', [$class, $stream]) }}"
                                                    class="btn btn-sm btn-outline-secondary">{{ __('Edit') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assigned Subjects --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Assigned Subjects') }}</h5>
                    <button class="btn btn-sm btn-outline-primary" disabled>
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Assign Subject') }}
                    </button>
                </div>
                <div class="card-body">
                    @if ($class->subjects->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">{{ __('No subjects assigned yet.') }}</p>
                            <small
                                class="text-muted">{{ __('Assign subjects that will be taught in this class.') }}</small>
                        </div>
                    @else
                        <div class="row g-2">
                            @foreach ($class->subjects as $subject)
                                <div class="col-md-6">
                                    <div class="border rounded p-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold">{{ $subject->name }}</div>
                                                <small class="text-muted">{{ $subject->code ?? '' }}</small>
                                            </div>
                                            @if ($subject->is_compulsory)
                                                <span class="badge bg-info">{{ __('Compulsory') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Quick Stats Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.academics.classes.edit', $class) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>{{ __('Edit Class Details') }}
                        </a>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-people me-2"></i>{{ __('View Students') }}
                        </button>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-book me-2"></i>{{ __('Manage Subjects') }}
                        </button>
                        <a href="{{ route('tenant.academics.streams.index', $class) }}" class="btn btn-outline-primary">
                            <i class="bi bi-diagram-3 me-2"></i>{{ __('Manage Streams') }}
                        </a>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-calendar-check me-2"></i>{{ __('View Timetable') }}
                        </button>
                        <hr class="my-2">
                        <form action="{{ route('tenant.academics.classes.destroy', $class) }}" method="POST"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this class? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>{{ __('Delete Class') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">{{ __('Capacity Used') }}</span>
                            @if ($class->capacity)
                                <span
                                    class="fw-semibold">{{ number_format(($class->active_students_count / $class->capacity) * 100, 1) }}%</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        @if ($class->capacity)
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $class->active_students_count / $class->capacity >= 0.9 ? 'bg-danger' : ($class->active_students_count / $class->capacity >= 0.7 ? 'bg-warning' : 'bg-success') }}"
                                    role="progressbar"
                                    style="width: {{ min(($class->active_students_count / $class->capacity) * 100, 100) }}%"
                                    aria-valuenow="{{ $class->active_students_count }}" aria-valuemin="0"
                                    aria-valuemax="{{ $class->capacity }}">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $class->streams->count() }}</div>
                                <small class="text-muted">{{ __('Streams') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $class->subjects->count() }}</div>
                                <small class="text-muted">{{ __('Subjects') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $class->active_students_count ?? 0 }}</div>
                                <small class="text-muted">{{ __('Students') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $class->capacity ?? 0 }}</div>
                                <small class="text-muted">{{ __('Max Capacity') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

