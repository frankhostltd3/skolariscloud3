@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ $stream->full_name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.classes.index') }}">{{ __('Classes') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.classes.show', $class) }}">{{ $class->name }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.streams.index', $class) }}">{{ __('Streams') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $stream->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('tenant.academics.streams.edit', [$class, $stream]) }}">
                <i class="bi bi-pencil me-1"></i>{{ __('Edit Stream') }}
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.streams.index', $class) }}">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Streams') }}
            </a>
        </div>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Stream Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Stream Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Stream Name') }}</label>
                            <p class="mb-0 fw-semibold">{{ $stream->name }}</p>
                        </div>

                        @if ($stream->code)
                            <div class="col-md-6">
                                <label class="text-muted small">{{ __('Stream Code') }}</label>
                                <p class="mb-0"><code>{{ $stream->code }}</code></p>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Class') }}</label>
                            <p class="mb-0 fw-semibold">{{ $class->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Status') }}</label>
                            <p class="mb-0">
                                <span class="badge {{ $stream->is_active ? 'bg-success' : 'bg-warning' }}">
                                    {{ $stream->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Capacity') }}</label>
                            <p class="mb-0">
                                <span class="fw-semibold">{{ $stream->capacity ?? __('Not set') }}</span>
                                @if ($stream->capacity)
                                    <span class="text-muted">{{ __('students max') }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Current Enrollment') }}</label>
                            <p class="mb-0">
                                <span class="fw-semibold">{{ $stream->students->count() }}</span>
                                <span class="text-muted">{{ __('students') }}</span>
                                @if ($stream->capacity && $stream->students->count())
                                    <span
                                        class="text-muted">({{ number_format(($stream->students->count() / $stream->capacity) * 100, 1) }}%
                                        {{ __('full') }})</span>
                                @endif
                            </p>
                        </div>

                        @if ($stream->description)
                            <div class="col-12">
                                <label class="text-muted small">{{ __('Description') }}</label>
                                <p class="mb-0">{{ $stream->description }}</p>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Created') }}</label>
                            <p class="mb-0 text-muted">{{ $stream->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small">{{ __('Last Updated') }}</label>
                            <p class="mb-0 text-muted">{{ $stream->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enrolled Students --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Enrolled Students') }}</h5>
                    <button class="btn btn-sm btn-outline-primary" disabled>
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Student') }}
                    </button>
                </div>
                <div class="card-body">
                    @if ($stream->students->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0">{{ __('No students enrolled in this stream yet.') }}</p>
                            <small class="text-muted">{{ __('Students will appear here once enrolled.') }}</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Student Name') }}</th>
                                        <th>{{ __('Student ID') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stream->students as $student)
                                        <tr>
                                            <td class="fw-semibold">{{ $student->name }}</td>
                                            <td>{{ $student->student_id ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    disabled>{{ __('View') }}</button>
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

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.academics.streams.edit', [$class, $stream]) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>{{ __('Edit Stream Details') }}
                        </a>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-people me-2"></i>{{ __('Manage Students') }}
                        </button>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-calendar-check me-2"></i>{{ __('View Attendance') }}
                        </button>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-file-earmark-text me-2"></i>{{ __('Generate Report') }}
                        </button>
                        <hr class="my-2">
                        <form action="{{ route('tenant.academics.streams.destroy', [$class, $stream]) }}" method="POST"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this stream? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                {{ $stream->students->count() > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash me-2"></i>{{ __('Delete Stream') }}
                            </button>
                        </form>
                        @if ($stream->students->count() > 0)
                            <small class="text-muted text-center">{{ __('Cannot delete stream with students') }}</small>
                        @endif
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
                            @if ($stream->capacity)
                                <span
                                    class="fw-semibold">{{ number_format(($stream->students->count() / $stream->capacity) * 100, 1) }}%</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        @if ($stream->capacity)
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $stream->students->count() / $stream->capacity >= 0.9 ? 'bg-danger' : ($stream->students->count() / $stream->capacity >= 0.7 ? 'bg-warning' : 'bg-success') }}"
                                    role="progressbar"
                                    style="width: {{ min(($stream->students->count() / $stream->capacity) * 100, 100) }}%"
                                    aria-valuenow="{{ $stream->students->count() }}" aria-valuemin="0"
                                    aria-valuemax="{{ $stream->capacity }}">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $stream->students->count() }}</div>
                                <small class="text-muted">{{ __('Students') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $stream->capacity ?? 0 }}</div>
                                <small class="text-muted">{{ __('Max Capacity') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                @if ($stream->capacity)
                                    <div class="h4 mb-0 fw-bold">
                                        {{ max(0, $stream->capacity - $stream->students->count()) }}</div>
                                @else
                                    <div class="h4 mb-0 fw-bold">-</div>
                                @endif
                                <small class="text-muted">{{ __('Available') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h4 mb-0 fw-bold">{{ $stream->is_active ? __('Yes') : __('No') }}</div>
                                <small class="text-muted">{{ __('Active') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
