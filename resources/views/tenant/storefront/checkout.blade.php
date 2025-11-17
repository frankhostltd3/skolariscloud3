@extends('tenant.layouts.app')

@section('content')
<h1 class="h5 fw-semibold mb-3">{{ __('Checkout') }}</h1>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <div class="small text-secondary mb-1">{{ __('Item') }}</div>
        <div class="fw-semibold">
          @if($type === 'book')
            {{ $item->title }}
          @else
            {{ $item->title }}
          @endif
        </div>
        @if($type === 'book' && $item->author)
          <div class="small text-secondary">{{ __('By') }} {{ $item->author }}</div>
        @endif
      </div>
      <div class="text-end">
        <div class="small text-secondary mb-1">{{ __('Total') }}</div>
        <div class="display-6">{{ format_money($item->price) }}</div>
      </div>
    </div>
    <hr>
    <form method="post" action="{{ $type === 'book' ? route('tenant.storefront.books.purchase', $item) : route('tenant.storefront.pamphlets.purchase', $item) }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('Your name') }}</label>
          <input class="form-control" name="buyer_name" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Email') }}</label>
          <input type="email" class="form-control" name="buyer_email" required>
        </div>
        @if(!empty($paymentMethods))
        <div class="col-12">
          <label class="form-label">{{ __('Payment method') }}</label>
          <select class="form-select" name="payment_method">
            @foreach($paymentMethods as $pm)
              <option value="{{ $pm['key'] }}">{{ $pm['label'] }}</option>
            @endforeach
          </select>
          <div class="form-text">{{ __('Payment is simulated for now; status will be Pending.') }}</div>
        </div>
        @endif
      </div>
      <div class="mt-3 d-flex justify-content-between">
        <a class="btn btn-outline-secondary" href="{{ $type === 'book' ? route('tenant.storefront.books.show', $item) : route('tenant.storefront.pamphlets.show', $item) }}">{{ __('Back') }}</a>
        <button class="btn btn-primary" type="submit">{{ __('Place order') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
