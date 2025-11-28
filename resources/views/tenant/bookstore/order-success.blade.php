@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                style="width: 100px; height: 100px;">
                <i class="bi bi-check-lg" style="font-size: 4rem;"></i>
            </div>
        </div>
        <h1 class="display-5 fw-bold mb-3">Thank You for Your Order!</h1>
        <p class="lead text-muted mb-5">Your order has been placed successfully. We have sent a confirmation email to
            <strong>{{ $order->customer_email }}</strong>.</p>

        <div class="card shadow-sm border-0 mx-auto text-start mb-5" style="max-width: 600px;">
            <div class="card-header bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                    <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="text-muted mb-2">Shipping Address</h6>
                        <p class="mb-0">
                            {{ $order->customer_name }}<br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_country }}<br>
                            {{ $order->shipping_zip_code }}<br>
                            {{ $order->customer_phone }}
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted mb-2">Payment Method</h6>
                        <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <h6 class="text-muted mt-3 mb-2">Order Date</h6>
                        <p class="mb-0">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <h6 class="text-muted mb-3">Order Items</h6>
                <ul class="list-group mb-3">
                    @foreach ($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">{{ $item->book_title }}</h6>
                                <small class="text-muted">Qty: {{ $item->quantity }}</small>
                            </div>
                            <span class="text-muted">{{ format_money($item->price * $item->quantity) }}</span>
                        </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span class="fw-bold">Total</span>
                        <strong class="text-primary">{{ format_money($order->total_amount) }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-primary btn-lg px-4 gap-3">Continue Shopping</a>
            <a href="/" class="btn btn-outline-secondary btn-lg px-4">Return to Home</a>
        </div>
    </div>
@endsection
