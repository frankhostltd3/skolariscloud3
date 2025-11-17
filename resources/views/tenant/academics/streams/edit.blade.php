@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Edit Stream') }}</h1>
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
                    <form action="{{ route('tenant.academics.streams.update', [$class, $stream]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('tenant.academics.streams._form', ['buttonText' => __('Update Stream')])
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">{{ __('Stream Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">{{ __('Enrolled Students') }}</label>
                        <p class="mb-0 fw-semibold">{{ $stream->students->count() }}</p>
                    </div>
                    @if ($stream->capacity)
                        <div class="mb-3">
                            <label class="text-muted small">{{ __('Capacity Used') }}</label>
                            @php
                                $percentage =
                                    $stream->capacity > 0 ? ($stream->students->count() / $stream->capacity) * 100 : 0;
                            @endphp
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success') }}"
                                    role="progressbar" style="width: {{ min($percentage, 100) }}%"
                                    aria-valuenow="{{ $stream->students->count() }}" aria-valuemin="0"
                                    aria-valuemax="{{ $stream->capacity }}">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    @endif
                    <div>
                        <label class="text-muted small">{{ __('Created') }}</label>
                        <p class="mb-0 text-muted">{{ $stream->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            @if ($stream->students->count() > 0)
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <small>{{ __('This stream has :count enrolled student(s). Be careful when making changes.', ['count' => $stream->students->count()]) }}</small>
                </div>
            @endif
        </div>
    </div>
@endsection
