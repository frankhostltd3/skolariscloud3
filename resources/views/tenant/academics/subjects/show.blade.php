@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold">{{ $subject->name }}</h1>
            <div class="mt-1"><span class="badge bg-{{ $subject->type_badge_color }}">{{ $subject->type_label }}</span>
                <span class="badge {{ $subject->status_badge }}">{{ $subject->status_text }}</span></div>
        </div>
        <div class="d-flex gap-2"><a class="btn btn-success"
                href="{{ route('tenant.academics.subjects.assign_classes', $subject) }}"><i
                    class="bi bi-plus-circle me-1"></i>{{ __('Assign to Classes') }}</a><a class="btn btn-primary"
                href="{{ route('tenant.academics.subjects.edit', $subject) }}"><i
                    class="bi bi-pencil me-1"></i>{{ __('Edit') }}</a><a class="btn btn-outline-secondary"
                href="{{ route('tenant.academics.subjects.index') }}"><i
                    class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a></div>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small mb-3">{{ __('Subject Details') }}</h6>
                    <div class="mb-3"><label class="text-muted small">{{ __('Name') }}</label>
                        <p class="fw-semibold mb-0">{{ $subject->name }}</p>
                    </div>
                    @if ($subject->code)
                        <div class="mb-3"><label class="text-muted small">{{ __('Code') }}</label>
                            <p class="mb-0"><code>{{ $subject->code }}</code></p>
                        </div>
                        @endif @if ($subject->educationLevel)
                            <div class="mb-3"><label class="text-muted small">{{ __('Education Level') }}</label>
                                <p class="mb-0">{{ $subject->educationLevel->name }}</p>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="text-muted small">{{ __('Type') }}</label>
                            <p class="mb-0"><span
                                    class="badge bg-{{ $subject->type_badge_color }}">{{ $subject->type_label }}</span>
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-6"><label class="text-muted small">{{ __('Pass Mark') }}</label>
                                <p class="fw-semibold mb-0">{{ $subject->pass_mark }}/{{ $subject->max_marks }}</p>
                            </div>
                            @if ($subject->credit_hours)
                                <div class="col-6"><label class="text-muted small">{{ __('Credit Hours') }}</label>
                                    <p class="fw-semibold mb-0">{{ $subject->credit_hours }}</p>
                                </div>
                            @endif
                        </div>
                        @if ($subject->description)
                            <div class="mt-3"><label class="text-muted small">{{ __('Description') }}</label>
                                <p class="mb-0">{{ $subject->description }}</p>
                            </div>
                        @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Assigned Classes') }} ({{ $subject->classes->count() }})</h5><a
                        href="{{ route('tenant.academics.subjects.assign_classes', $subject) }}"
                        class="btn btn-sm btn-outline-primary"><i
                            class="bi bi-plus-circle me-1"></i>{{ __('Manage') }}</a>
                </div>
                <div class="card-body">
                    @if ($subject->classes->isEmpty())
                        <div class="text-center py-4"><i class="bi bi-building text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('Not assigned to any classes yet.') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Class') }}</th>
                                        <th>{{ __('Level') }}</th>
                                        <th>{{ __('Streams') }}</th>
                                        <th>{{ __('Compulsory') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subject->classes as $class)
                                        <tr>
                                            <td><a href="{{ route('tenant.academics.classes.show', $class) }}"
                                                    class="text-decoration-none">{{ $class->name }}</a></td>
                                            <td>{{ optional($class->educationLevel)->name ?? '—' }}</td>
                                            <td><span class="badge bg-secondary">{{ $class->streams->count() }}</span></td>
                                            <td>
                                                @if ($class->pivot->is_compulsory)
                                                <span class="badge bg-success">{{ __('Yes') }}</span>@else<span
                                                        class="badge bg-info">{{ __('Optional') }}</span>
                                                @endif
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
    </div>
@endsection
