@extends('tenant.layouts.app')

@section('content')
<h1 class="h4 fw-semibold mb-3">{{ __('Pamphlets') }}</h1>
<form method="get" class="row g-2 mb-3">
  <div class="col-auto"><input type="search" name="q" class="form-control" value="{{ $q }}" placeholder="{{ __('Search title or SKU') }}"></div>
  <div class="col-auto"><button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button></div>
  <div class="col-auto"><a class="btn btn-outline-secondary" href="{{ route('tenant.storefront.pamphlets') }}">{{ __('Reset') }}</a></div>
  <div class="col-auto ms-auto"><a class="btn btn-outline-secondary" href="{{ route('tenant.storefront.home') }}">{{ __('Storefront home') }}</a></div>
  </form>

<div class="row row-cols-1 row-cols-md-3 g-3">
  @forelse($pamphlets as $pamphlet)
    <div class="col">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <div class="fw-semibold">{{ $pamphlet->title }}</div>
          <div class="mt-auto d-flex justify-content-between align-items-center">
            <span class="fw-semibold">{{ format_money($pamphlet->price) }}</span>
            <div class="btn-group">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('tenant.storefront.pamphlets.show', $pamphlet) }}">{{ __('Details') }}</a>
              <a class="btn btn-sm btn-primary" href="{{ route('tenant.storefront.pamphlets.buy', $pamphlet) }}">{{ __('Buy') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col text-secondary">{{ __('No pamphlets found.') }}</div>
  @endforelse
</div>

<div class="mt-3">{{ $pamphlets->links() }}</div>
@endsection
