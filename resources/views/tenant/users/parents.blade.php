{{-- This view is rendered by ParentsController which extends the shared index view --}}
@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.users.partials.sidebar')
@endsection

@section('content')
  <h1 class="h4 fw-semibold mb-3">{{ __('Parents') }}</h1>
  <p class="text-secondary">{{ __('Managing parent and guardian accounts.') }}</p>
@endsection
