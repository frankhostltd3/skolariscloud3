@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Invoices') }}</h1>
    <div class="small text-secondary">{{ __('Generate, send, and reconcile billing documents for guardians or institutions.') }}</div>
  </div>
  <button class="btn btn-primary btn-sm" type="button" onclick="window.location.href='{{ route('tenant.modules.financials.invoices.create') }}'">
    <i class="bi bi-plus-circle me-1"></i>{{ __('Create invoice') }}
  </button>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h6 fw-semibold mb-0">{{ __('Invoice queue') }}</h2>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ __('Filter') }}</button>
        <button class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ __('Export') }}</button>
      </div>
    </div>
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          <th>{{ __('Invoice #') }}</th>
          <th>{{ __('Recipient') }}</th>
          <th>{{ __('Issued') }}</th>
          <th>{{ __('Due') }}</th>
          <th class="text-end">{{ __('Total') }}</th>
          <th>{{ __('Status') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6" class="text-center text-secondary">{{ __('Invoices have not been generated yet') }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
