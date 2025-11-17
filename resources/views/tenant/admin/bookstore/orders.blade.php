@extends('tenant.layouts.app')

@section('title', 'Bookstore Orders')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-cart-check"></i> Bookstore Orders
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.bookstore.index') }}">Bookstore</a>
                    </li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-list-check text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small mb-1">Total Orders</h6>
                            <h4 class="mb-0">{{ $orders->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small mb-1">Pending</h6>
                            <h4 class="mb-0">{{ $pendingCount ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small mb-1">Confirmed</h6>
                            <h4 class="mb-0">{{ $confirmedCount ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-truck text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small mb-1">Shipped</h6>
                            <h4 class="mb-0">{{ $shippedCount ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookstore.orders') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search by order #, customer name, email..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Order Status Filter -->
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Order Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                            Confirmed
                        </option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                            Processing
                        </option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>
                            Shipped
                        </option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                            Delivered
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            Cancelled
                        </option>
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div class="col-md-3">
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment Status</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>
                            Paid
                        </option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>
                            Failed
                        </option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>
                            Refunded
                        </option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.bookstore.orders') }}" 
                           class="btn btn-outline-secondary"
                           title="Clear Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> Orders List
            </h5>
        </div>
        <div class="card-body p-0">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <!-- Order Number -->
                                <td>
                                    <a href="{{ route('admin.bookstore.orders.show', $order) }}" 
                                       class="text-decoration-none fw-bold text-primary">
                                        {{ $order->order_number }}
                                    </a>
                                </td>

                                <!-- Customer -->
                                <td>
                                    <div class="fw-bold">{{ $order->customer_name }}</div>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                    @if($order->customer_phone)
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-phone"></i> {{ $order->customer_phone }}
                                        </small>
                                    @endif
                                </td>

                                <!-- Items Count -->
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $order->items->count() }} 
                                        {{ Str::plural('item', $order->items->count()) }}
                                    </span>
                                </td>

                                <!-- Total -->
                                <td>
                                    <div class="fw-bold">${{ number_format($order->total, 2) }}</div>
                                    <small class="text-muted">
                                        Subtotal: ${{ number_format($order->subtotal, 2) }}
                                    </small>
                                </td>

                                <!-- Payment Method -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($order->payment_method === 'paypal')
                                            <i class="bi bi-paypal text-primary me-2"></i>
                                        @elseif($order->payment_method === 'flutterwave')
                                            <i class="bi bi-credit-card text-warning me-2"></i>
                                        @elseif($order->payment_method === 'pesapal')
                                            <i class="bi bi-wallet2 text-success me-2"></i>
                                        @else
                                            <i class="bi bi-cash me-2"></i>
                                        @endif
                                        <span class="small">{{ ucfirst($order->payment_method) }}</span>
                                    </div>
                                </td>

                                <!-- Order Status -->
                                <td>
                                    <form method="POST" 
                                          action="{{ route('admin.bookstore.orders.status', $order) }}"
                                          class="status-update-form">
                                        @csrf
                                        <select name="status" 
                                                class="form-select form-select-sm status-select"
                                                onchange="this.form.submit()"
                                                {{ $order->status === 'cancelled' ? 'disabled' : '' }}>
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
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                                Cancelled
                                            </option>
                                        </select>
                                    </form>
                                </td>

                                <!-- Payment Status -->
                                <td>
                                    <form method="POST" 
                                          action="{{ route('admin.bookstore.orders.payment', $order) }}"
                                          class="payment-update-form">
                                        @csrf
                                        <select name="payment_status" 
                                                class="form-select form-select-sm payment-select"
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
                                </td>

                                <!-- Date -->
                                <td>
                                    <div>{{ $order->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.bookstore.orders.show', $order) }}" 
                                           class="btn btn-outline-primary"
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($order->status !== 'cancelled')
                                            <button type="button" 
                                                    class="btn btn-outline-danger"
                                                    onclick="confirmCancelOrder('{{ route('admin.bookstore.orders.cancel', $order) }}')"
                                                    title="Cancel Order">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Orders Found</h5>
                    <p class="text-muted">
                        @if(request('search') || request('status') || request('payment_status'))
                            No orders match your search criteria.
                        @else
                            No orders have been placed yet.
                        @endif
                    </p>
                    @if(request('search') || request('status') || request('payment_status'))
                        <a href="{{ route('admin.bookstore.orders') }}" class="btn btn-outline-secondary mt-2">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.status-select {
    min-width: 120px;
    cursor: pointer;
}

.status-select option[value="pending"] { background-color: #fff3cd; }
.status-select option[value="confirmed"] { background-color: #d1ecf1; }
.status-select option[value="processing"] { background-color: #e2e3e5; }
.status-select option[value="shipped"] { background-color: #cfe2ff; }
.status-select option[value="delivered"] { background-color: #d1e7dd; }
.status-select option[value="cancelled"] { background-color: #f8d7da; }

.payment-select {
    min-width: 100px;
    cursor: pointer;
}

.payment-select option[value="pending"] { background-color: #fff3cd; }
.payment-select option[value="paid"] { background-color: #d1e7dd; }
.payment-select option[value="failed"] { background-color: #f8d7da; }
.payment-select option[value="refunded"] { background-color: #cfe2ff; }

.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}
</style>

<script>
function confirmCancelOrder(url) {
    if (confirm('Are you sure you want to cancel this order? Stock quantities will be restored.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('{{ session('success') }}');
    });
</script>
@endif
@endsection

