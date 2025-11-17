@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold">{{ __('Create Examination Body') }}</h1>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.examination-bodies.store') }}" method="POST">
                @csrf
                @include('tenant.academics.examination-bodies._form', ['buttonText' => __('Create')])
            </form>
        </div>
    </div>
@endsection
