@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.academics.terms.index') }}">{{ __('Terms') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
            </ol>
        </nav>
        <h1 class="h4 fw-semibold">{{ __('Edit Term') }}: {{ $term->name }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.terms.update', $term) }}" method="POST">
                @csrf
                @method('PUT')
                @include('tenant.academics.terms._form')

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('tenant.academics.terms.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>{{ __('Update Term') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
