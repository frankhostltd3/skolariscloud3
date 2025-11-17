@extends('tenant.layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-receipt"></i> Order Details
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.bookstore.index') }}">Bookstore</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.bookstore.orders') }}">Orders</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.bookstore.orders') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Order Information -->
        <div class="col-lg-8 mb-4">
            <!-- Order Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $order->order_number }}</h4>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-calendar"></i> Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                            </p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $order->status_badge_color }} fs-6 px-3 py-2">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam"></i> Order Items ({{ $order->items->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Book</th>
                                    <th class="text-center" style="width: 100px;">Quantity</th>
                                    <th class="text-end" style="width: 120px;">Unit Price</th>
                                    <th class="text-end" style="width: 100px;">Discount</th>
                                    <th class="text-end" style="width: 120px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0">
                                                @if($item->book && $item->book->cover_image_path)
                                                    <img src="{{ Storage::disk('public')->url($item->book->cover_image_path) }}" 
                                                         alt="{{ $item->book_title }}" 
                                                         class="rounded"
                                                         style="width: 50px; height: 70px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 70px;">
                                                        <i class="bi bi-book text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold">{{ $item->book_title }}</div>
                                                <small class="text-muted">
                                                    <i class="bi bi-person"></i> {{ $item->book_author }}
                                                </small>
                                                @if($item->book_isbn)
                                                    <br>
                                                    <small class="text-muted">ISBN: {{ $item->book_isbn }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark fs-6">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">
                                        @if($item->discount_percentage > 0)
                                            <span class="text-success">
                                                -{{ $item->discount_percentage }}%<br>
                                                <small>(${{ number_format($item->discount_amount, 2) }})</small>
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-success">Order Discount:</td>
                                    <td class="text-end text-success">-${{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-end">Tax (5%):</td>
                                    <td class="text-end">${{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Shipping:</td>
                                    <td class="text-end">
                                        @if($order->shipping_cost > 0)
                                            ${{ number_format($order->shipping_cost, 2) }}
                                        @else
                                            <span class="text-success">FREE</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="4" class="text-end fw-bold fs-5">Total:</td>
                                    <td class="text-end fw-bold text-primary fs-5">${{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Notes (if any) -->
            @if($order->notes)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-chat-left-text"></i> Customer Notes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">{{ $order->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Admin Notes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Admin Notes
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bookstore.orders.status', $order) }}">
                        @csrf
                        <input type="hidden" name="status" value="{{ $order->status }}">
                        <div class="mb-3">
                            <textarea name="admin_notes" 
                                      class="form-control" 
                                      rows="4" 
                                      placeholder="Add internal notes about this order...">{{ $order->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Notes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4 mb-4">
            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle"></i> Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Name</label>
                        <div class="fw-bold">{{ $order->customer_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <div>
                            <a href="mailto:{{ $order->customer_email }}" class="text-decoration-none">
                                {{ $order->customer_email }}
                            </a>
                        </div>
                    </div>
                    @if($order->customer_phone)
                    <div class="mb-3">
                        <label class="text-muted small">Phone</label>
                        <div>
                            <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none">
                                {{ $order->customer_phone }}
                            </a>
                        </div>
                    </div>
                    @endif
                    @if($order->user)
                    <div>
                        <label class="text-muted small">User Account</label>
                        <div>
                            <span class="badge bg-info">Registered User</span>
                        </div>
                    </div>
                    @else
                    <div>
                        <span class="badge bg-secondary">Guest Checkout</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-geo-alt"></i> Shipping Address
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;">{{ $order->shipping_address }}</p>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-credit-card"></i> Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Payment Method</label>
                        <div class="d-flex align-items-center">
                            @if($order->payment_method === 'paypal')
                                <i class="bi bi-paypal text-primary fs-4 me-2"></i>
                            @elseif($order->payment_method === 'flutterwave')
                                <i class="bi bi-credit-card text-warning fs-4 me-2"></i>
                            @elseif($order->payment_method === 'pesapal')
                                <i class="bi bi-wallet2 text-success fs-4 me-2"></i>
                            @elseif($order->payment_method === 'dpo')
                                <i class="bi bi-cash fs-4 me-2"></i>
                            @elseif($order->payment_method === 'mtn_momo')
                                <i class="bi bi-phone text-warning fs-4 me-2"></i>
                            @elseif($order->payment_method === 'airtel_money')
                                <i class="bi bi-phone text-danger fs-4 me-2"></i>
                            @endif
                            <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Payment Status</label>
                        <form method="POST" action="{{ route('admin.bookstore.orders.payment', $order) }}">
                            @csrf
                            <select name="payment_status" 
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>
                                    Paid
                                </option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>
                                    Failed
                                </option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>
                                    Refunded
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Status Update -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-arrow-repeat"></i> Update Order Status
                    </h6>
                </div>
                <div class="card-body">
                    @if($order->status !== 'cancelled')
                        <form method="POST" action="{{ route('admin.bookstore.orders.status', $order) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Change Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>
                                        Confirmed
                                    </option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                        Processing
                                    </option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>
                                        Shipped
                                    </option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>
                                        Delivered
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> Update Status
                            </button>
                        </form>
                    @else
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle"></i> This order has been cancelled
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history"></i> Order Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $order->created_at ? 'completed' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="fw-bold small">Order Placed</div>
                                <small class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->confirmed_at ? 'completed' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="fw-bold small">Confirmed</div>
                                @if($order->confirmed_at)
                                    <small class="text-muted">{{ $order->confirmed_at->format('M d, Y h:i A') }}</small>
                                @else
                                    <small class="text-muted">Pending</small>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->shipped_at ? 'completed' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="fw-bold small">Shipped</div>
                                @if($order->shipped_at)
                                    <small class="text-muted">{{ $order->shipped_at->format('M d, Y h:i A') }}</small>
                                @else
                                    <small class="text-muted">Not yet shipped</small>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->delivered_at ? 'completed' : '' }}">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="fw-bold small">Delivered</div>
                                @if($order->delivered_at)
                                    <small class="text-muted">{{ $order->delivered_at->format('M d, Y h:i A') }}</small>
                                @else
                                    <small class="text-muted">Not yet delivered</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancel Order -->
            @if($order->status !== 'cancelled' && $order->status !== 'delivered')
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger bg-opacity-10 py-3">
                    <h6 class="mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Danger Zone
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Cancelling this order will restore the stock quantities for all items.
                    </p>
                    <form method="POST" 
                          action="{{ route('admin.bookstore.orders.cancel', $order) }}"
                          onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-x-circle"></i> Cancel Order
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #dee2e6;
}

.timeline-item.completed:not(:last-child)::before {
    background-color: #198754;
}

.timeline-marker {
    position: absolute;
    left: -28px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background-color: #dee2e6;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item.completed .timeline-marker {
    background-color: #198754;
    box-shadow: 0 0 0 2px #198754;
}

.timeline-content {
    padding-top: 0;
}
</style>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('{{ session('success') }}');
    });
</script>
@endif
@endsection

