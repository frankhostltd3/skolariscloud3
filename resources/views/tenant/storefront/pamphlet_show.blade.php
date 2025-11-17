@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h5 fw-semibold mb-0">{{ $pamphlet->title }}</h1>
  <a class="btn btn-outline-secondary btn-sm" href="{{ route('tenant.storefront.pamphlets') }}">{{ __('Back') }}</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="mb-2 text-secondary small">SKU: {{ $pamphlet->sku }}</div>
    @if($pamphlet->description)
      <p class="mb-3">{{ $pamphlet->description }}</p>
    @endif
    <div class="d-flex justify-content-between align-items-center">
      <div class="display-6">{{ format_money($pamphlet->price) }}</div>
      <a class="btn btn-primary" href="{{ route('tenant.storefront.pamphlets.buy', $pamphlet) }}">{{ __('Buy now') }}</a>
    </div>
  </div>
  </div>
@endsection
