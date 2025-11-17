@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-semibold">{{ __('Create Subject') }}</h1>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('tenant.academics.subjects.store') }}" method="POST">@csrf @include('tenant.academics.subjects._form', ['buttonText' => __('Create Subject')])
            </form>
        </div>
    </div>
@endsection
