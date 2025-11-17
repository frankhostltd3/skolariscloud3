@extends('tenant.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Bookstore</h1>
    <p class="text-muted">Manage items in the store: Books and Pamphlets.</p>
    <div class="mt-4">
    <a class="btn btn-primary me-2" href="{{ route('tenant.modules.bookstore.books.index') }}">Books</a>
    <a class="btn btn-secondary" href="{{ route('tenant.modules.bookstore.pamphlets.index') }}">Pamphlets</a>
        <a href="{{ route('tenant.modules.bookstore.orders.index') }}" class="list-group-item list-group-item-action">
            <span class="bi bi-receipt-cutoff me-2"></span>{{ __('Orders') }}
        </a>
    </div>
@endsection
