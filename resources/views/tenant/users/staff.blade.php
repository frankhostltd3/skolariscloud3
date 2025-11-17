{{-- This view is rendered by StaffController which extends the shared index view --}}
@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.users.partials.sidebar')
@endsection

@section('content')
  <h1 class="h4 fw-semibold mb-3">{{ __('Staff') }}</h1>
  <p class="text-secondary">{{ __('Managing staff members and their roles.') }}</p>
@endsection
