@extends('tenant.layouts.app')

@section('content')
<div class="text-center py-5">
  <div class="display-6 mb-3">{{ __('Thank you!') }}</div>
  <p class="lead">{{ session('success') ?? __('Your order has been placed successfully. We will contact you shortly.') }}</p>
  <a class="btn btn-primary mt-3" href="{{ route('tenant.storefront.home') }}">{{ __('Return to store') }}</a>
</div>
@endsection
