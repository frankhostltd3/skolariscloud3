@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Fee management') }}</h1>
    <div class="small text-secondary">{{ __('Configure fee items, assign to cohorts, and track payments.') }}</div>
  </div>
  <a class="btn btn-outline-primary btn-sm" href="{{ route('tenant.modules.fees.index') }}">{{ __('Open legacy fees module') }}</a>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h2 class="h6 fw-semibold mb-3">{{ __('Quick actions') }}</h2>
    <div class="d-flex flex-wrap gap-2 small">
      <button class="btn btn-primary btn-sm" type="button" onclick="window.location.href='{{ route('tenant.modules.financials.fees.create') }}'">
        <i class="bi bi-plus-circle me-1"></i>{{ __('Create fee item') }}
      </button>
      <button class="btn btn-outline-secondary btn-sm" type="button" onclick="window.location.href='{{ route('tenant.modules.financials.fees.assign') }}'">
        <i class="bi bi-person-plus me-1"></i>{{ __('Assign to class/level') }}
      </button>
      <button class="btn btn-outline-secondary btn-sm" type="button" onclick="window.location.href='{{ route('tenant.modules.financials.fees.reminders') }}'">
        <i class="bi bi-envelope me-1"></i>{{ __('Send reminders') }}
      </button>
    </div>
    <p class="text-muted small mb-0 mt-3">{{ __('Full fee management workflows are being consolidated here.') }}</p>
  </div>
</div>
@endsection
