@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4">Shopping Cart</h1>

        @if (count($cart) > 0)
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th scope="col" class="ps-4 py-3">Product</th>
                                            <th scope="col" class="py-3">Price</th>
                                            <th scope="col" class="py-3" style="width: 150px;">Quantity</th>
                                            <th scope="col" class="py-3 text-end pe-4">Total</th>
                                            <th scope="col" class="py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cart as $id => $details)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        @if (isset($details['cover_image']))
                                                            <img src="{{ $details['cover_image'] }}"
                                                                alt="{{ $details['title'] }}" class="rounded me-3"
                                                                style="width: 50px; height: 75px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 75px;">
                                                                <i class="bi bi-book text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0 text-truncate" style="max-width: 200px;">
                                                                {{ $details['title'] }}</h6>
                                                            <small class="text-muted">{{ $details['author'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">{{ format_money($details['price']) }}</td>
                                                <td class="py-3">
                                                    <form action="{{ route('tenant.bookstore.cart.update') }}"
                                                        method="POST" class="d-flex align-items-center">
                                                        @csrf
                                                        <input type="hidden" name="book_id" value="{{ $id }}">
                                                        <input type="number" name="quantity"
                                                            value="{{ $details['quantity'] }}"
                                                            class="form-control form-control-sm text-center" min="1"
                                                            onchange="this.form.submit()">
                                                    </form>
                                                </td>
                                                <td class="py-3 text-end pe-4 fw-bold">
                                                    {{ format_money($details['price'] * $details['quantity']) }}
                                                </td>
                                                <td class="py-3 text-end pe-4">
                                                    <form action="{{ route('tenant.bookstore.cart.remove') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="book_id" value="{{ $id }}">
                                                        <button type="submit" class="btn btn-link text-danger p-0">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-bold">{{ format_money($total) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 mb-0">Total</span>
                                <span class="h5 mb-0 text-primary">{{ format_money($total) }}</span>
                            </div>
                            <a href="{{ route('tenant.bookstore.checkout') }}" class="btn btn-primary w-100 py-2">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any books to your cart yet.</p>
                <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-primary btn-lg">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
@endsection
