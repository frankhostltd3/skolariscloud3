@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Create Education Level') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.education-levels.index') }}">{{ __('Education Levels') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('Create') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @includeWhen(session('error'), 'partials.toast')

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.education-levels.store') }}" method="POST">
                @csrf
                @include('tenant.academics.education-levels._form', [
                    'buttonText' => __('Create Education Level'),
                ])
            </form>
        </div>
    </div>
@endsection
