@extends('landlord.layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <h1 class="h4 fw-semibold mb-3">{{ __('System health') }}</h1>
    <p class="text-secondary mb-4">{{ __('Monitor uptime, queues, and background jobs.') }}</p>

    <div class="row g-4">
      <div class="col-12 col-xl-6">
        <div class="border rounded p-3 h-100">
          <h2 class="h6 fw-semibold mb-3">{{ __('Queues & jobs') }}</h2>
          <p class="text-secondary small mb-0">{{ __('Queue metrics coming soon (failed jobs, pending jobs, last run).') }}</p>
        </div>
      </div>
      <div class="col-12 col-xl-6">
        <div class="border rounded p-3 h-100">
          <h2 class="h6 fw-semibold mb-3">{{ __('Recent logs') }}</h2>
          <p class="text-secondary small mb-0">{{ __('Log tail view coming soon.') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
