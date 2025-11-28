@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Order Details</h2>
            <a href="{{ route('tenant.bookstore.my-orders') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="ps-4 py-3">Book</th>
                                        <th scope="col" class="py-3 text-center">Price</th>
                                        <th scope="col" class="py-3 text-center">Quantity</th>
                                        <th scope="col" class="pe-4 py-3 text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->book->cover_image)
                                                        <img src="{{ Storage::url($item->book->cover_image) }}"
                                                            alt="{{ $item->book->title }}" class="me-3 rounded"
                                                            style="width: 40px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center me-3 rounded"
                                                            style="width: 40px; height: 60px;">
                                                            <i class="bi bi-book text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->book->title }}</h6>
                                                        <small class="text-muted">{{ $item->book->author }}</small>
                                                        @if ($item->book->is_digital)
                                                            <span class="badge bg-info text-dark ms-2">Digital</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center py-3">{{ format_money($item->price) }}</td>
                                            <td class="text-center py-3">{{ $item->quantity }}</td>
                                            <td class="text-end pe-4 py-3">
                                                {{ format_money($item->price * $item->quantity) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold py-3">Total:</td>
                                        <td class="text-end pe-4 fw-bold py-3">{{ format_money($order->total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Digital Downloads Section -->
                @php
                    $digitalItems = $order->items->filter(function ($item) {
                        return $item->book->is_digital;
                    });
                @endphp

                @if ($digitalItems->count() > 0 && $order->status === 'completed')
                    <div class="card shadow-sm mb-4 border-info">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0"><i class="bi bi-cloud-download me-2"></i> Digital Downloads</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Your digital products are ready for download.</p>
                            <div class="list-group">
                                @foreach ($digitalItems as $item)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $item->book->title }}</h6>
                                            <small class="text-muted">Format: PDF/EPUB</small>
                                        </div>
                                        <a href="{{ route('tenant.bookstore.download', ['order' => $order->id, 'book' => $item->book->id]) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Order Number</label>
                            <p class="fw-bold mb-0">{{ $order->order_number }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Date Placed</label>
                            <p class="mb-0">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Status</label>
                            <div>
                                <span class="badge bg-{{ $order->status_badge_color }} fs-6">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Payment Method</label>
                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="fw-bold mb-1">{{ $order->customer_name }}</p>
                        <p class="mb-1"><a href="mailto:{{ $order->customer_email }}"
                                class="text-decoration-none">{{ $order->customer_email }}</a></p>
                        <p class="mb-0">{{ $order->customer_phone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
