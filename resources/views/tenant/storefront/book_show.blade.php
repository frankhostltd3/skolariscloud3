@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h5 fw-semibold mb-0">{{ $book->title }}</h1>
  <a class="btn btn-outline-secondary btn-sm" href="{{ route('tenant.storefront.books') }}">{{ __('Back') }}</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="mb-2 text-secondary small">SKU: {{ $book->sku }}</div>
    @if($book->author)
      <div class="mb-2">{{ __('By') }} <strong>{{ $book->author }}</strong></div>
    @endif
    @if($book->description)
      <p class="mb-3">{{ $book->description }}</p>
    @endif
    <div class="d-flex justify-content-between align-items-center">
      <div class="display-6">{{ format_money($book->price) }}</div>
      <a class="btn btn-primary" href="{{ route('tenant.storefront.books.buy', $book) }}">{{ __('Buy now') }}</a>
    </div>
  </div>
  </div>
@endsection
