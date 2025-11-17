@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h5 fw-semibold mb-0">{{ __('Order') }} #{{ $order->id }}</h1>
  <a class="btn btn-outline-secondary btn-sm" href="{{ route('tenant.modules.bookstore.orders.index') }}">{{ __('Back') }}</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="mb-2"><span class="text-secondary small">{{ __('Item') }}</span><div class="fw-semibold">{{ $order->item_title }} ({{ ucfirst($order->item_type) }})</div></div>
        <div class="mb-2"><span class="text-secondary small">{{ __('Buyer') }}</span><div class="fw-semibold">{{ $order->buyer_name }} · {{ $order->buyer_email }}</div></div>
  <div class="mb-2"><span class="text-secondary small">{{ __('Price') }}</span><div class="fw-semibold">{{ format_money($order->price) }}</div></div>
        <div class="mb-2"><span class="text-secondary small">{{ __('Status') }}</span><div class="fw-semibold">{{ ucfirst($order->status) }}</div></div>
        @if($order->payment_method)
          <div class="mb-2"><span class="text-secondary small">{{ __('Payment method') }}</span><div class="fw-semibold">{{ ucfirst(str_replace('_',' ', $order->payment_method)) }}</div></div>
        @endif
        @if($order->paid_at)
          <div class="mb-2"><span class="text-secondary small">{{ __('Paid at') }}</span><div class="fw-semibold">{{ $order->paid_at->format('Y-m-d H:i') }}</div></div>
        @endif
        @if($order->receipt_email_sent_at)
          <div class="mb-2"><span class="text-secondary small">{{ __('Receipt emailed') }}</span><div class="fw-semibold">{{ $order->receipt_email_sent_at->format('Y-m-d H:i') }}</div></div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h2 class="h6 fw-semibold mb-3">{{ __('Admin notes') }}</h2>
        <form method="post" action="{{ route('tenant.modules.bookstore.orders.notes', $order) }}">
          @csrf
          <textarea name="admin_notes" class="form-control" rows="6">{{ old('admin_notes', $order->admin_notes) }}</textarea>
          <div class="mt-3 d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">{{ __('Save notes') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
