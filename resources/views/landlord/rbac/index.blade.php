@extends('landlord.layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <h1 class="h4 fw-semibold mb-3">{{ __('Roles & Permissions') }}</h1>
    <p class="text-secondary mb-4">{{ __('Manage roles and team-aware permissions.') }}</p>
    <div class="alert alert-info">{{ __('RBAC editor coming soon.') }}</div>
  </div>
</div>
@endsection
