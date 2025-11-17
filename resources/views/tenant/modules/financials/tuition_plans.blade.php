@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Tuition plans') }}</h1>
    <div class="small text-secondary">{{ __('Define structured fee plans and installment schedules.') }}</div>
  </div>
  <button class="btn btn-outline-primary btn-sm" type="button" disabled>{{ __('Add plan') }}</button>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <p class="text-muted small mb-3">{{ __('Create tuition plan templates that bundle fee items, discounts, and payment timelines.') }}</p>
    <div class="alert alert-info small mb-0">{{ __('Plan designer is under construction. Continue using existing fee assignments until this module is live.') }}</div>
  </div>
</div>
@endsection
