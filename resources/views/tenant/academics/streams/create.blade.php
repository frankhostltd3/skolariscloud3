@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Create New Stream') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.classes.index') }}">{{ __('Classes') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.classes.show', $class) }}">{{ $class->name }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.streams.index', $class) }}">{{ __('Streams') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
                </ol>
            </nav>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.streams.index', $class) }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Streams') }}
        </a>
    </div>

    @includeWhen(session('error'), 'partials.toast')

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Stream Details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.academics.streams.store', $class) }}" method="POST">
                        @csrf
                        @include('tenant.academics.streams._form', ['buttonText' => __('Create Stream')])
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Class Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Class Name') }}</label>
                        <p class="mb-0 fw-semibold">{{ $class->name }}</p>
                    </div>
                    @if ($class->code)
                        <div class="mb-3">
                            <label class="text-muted small">{{ __('Class Code') }}</label>
                            <p class="mb-0"><code>{{ $class->code }}</code></p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Current Streams') }}</label>
                        <p class="mb-0 fw-semibold">{{ $class->streams->count() }}</p>
                    </div>
                    <div>
                        <label class="text-muted small">{{ __('Total Students') }}</label>
                        <p class="mb-0 fw-semibold">{{ $class->active_students_count ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Common Stream Names') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">{{ __('Popular naming patterns:') }}</p>
                    <div class="mb-2">
                        <strong>{{ __('Alphabetic:') }}</strong><br>
                        <small class="text-muted">A, B, C, D, E</small>
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('Numeric:') }}</strong><br>
                        <small class="text-muted">1, 2, 3, 4, 5</small>
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('Cardinal:') }}</strong><br>
                        <small class="text-muted">East, West, North, South</small>
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('Colors:') }}</strong><br>
                        <small class="text-muted">Red, Blue, Green, Yellow</small>
                    </div>
                    <div>
                        <strong>{{ __('Houses:') }}</strong><br>
                        <small class="text-muted">Lions, Tigers, Eagles, Panthers</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
