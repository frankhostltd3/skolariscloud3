@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold mb-0">{{ __('Create Class') }}</h1>
        <a class="btn btn-outline-secondary" href="{{ url('/tenant/academics/classes') }}">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Classes') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ __('Validation Error') }}</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ url('/tenant/academics/classes') }}" method="POST">
                @csrf
                @include('tenant.academics.classes._form')

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>{{ __('Create Class') }}
                    </button>
                    <a href="{{ url('/tenant/academics/classes') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

