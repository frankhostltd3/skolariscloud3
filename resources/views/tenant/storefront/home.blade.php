@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 fw-semibold mb-0">{{ __('Bookstore') }}</h1>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('tenant.storefront.books') }}">{{ __('Browse books') }}</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('tenant.storefront.pamphlets') }}">{{ __('Browse pamphlets') }}</a>
  </div>
  </div>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h2 class="h6 fw-semibold mb-3">{{ __('New books') }}</h2>
        <div class="row row-cols-1 row-cols-md-2 g-3">
          @forelse($newBooks as $book)
            <div class="col">
              <div class="border rounded p-3 h-100">
                <div class="fw-semibold">{{ $book->title }}</div>
                <div class="text-secondary small mb-2">{{ $book->author }}</div>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-semibold">{{ format_money($book->price) }}</span>
                  <a class="btn btn-primary btn-sm" href="{{ route('tenant.storefront.books.buy', $book) }}">{{ __('Buy') }}</a>
                </div>
              </div>
            </div>
          @empty
            <div class="col text-secondary">{{ __('No books yet.') }}</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h2 class="h6 fw-semibold mb-3">{{ __('New pamphlets') }}</h2>
        <div class="row row-cols-1 row-cols-md-2 g-3">
          @forelse($newPamphlets as $pamphlet)
            <div class="col">
              <div class="border rounded p-3 h-100">
                <div class="fw-semibold">{{ $pamphlet->title }}</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <span class="fw-semibold">{{ format_money($pamphlet->price) }}</span>
                  <a class="btn btn-primary btn-sm" href="{{ route('tenant.storefront.pamphlets.buy', $pamphlet) }}">{{ __('Buy') }}</a>
                </div>
              </div>
            </div>
          @empty
            <div class="col text-secondary">{{ __('No pamphlets yet.') }}</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
