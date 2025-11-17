@extends('tenant.layouts.app')

@section('title', 'Bookstore Dashboard')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-shop"></i> Bookstore Dashboard
            </h2>
            <p class="text-muted mb-0">Monitor sales, inventory, and orders</p>
        </div>
        <div>
            <a href="{{ route('tenant.modules.library.books.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Book
            </a>
            <a href="{{ route('admin.bookstore.inventory') }}" class="btn btn-outline-secondary">
                <i class="bi bi-box-seam"></i> Manage Inventory
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Books -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Books</h6>
                            <h3 class="mb-0">{{ $totalBooks }}</h3>
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i> {{ $inStockBooks }} in stock
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="bi bi-currency-dollar fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Revenue</h6>
                            <h3 class="mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> All time
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Orders -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="bi bi-cart-check fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Today's Orders</h6>
                            <h3 class="mb-0">{{ $todayOrders }}</h3>
                            <small class="text-success">
                                ${{ number_format($todayRevenue, 2) }} revenue
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Pending Orders</h6>
                            <h3 class="mb-0">{{ $pendingOrders }}</h3>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-circle"></i> Needs attention
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> Recent Orders
                        </h5>
                        <a href="{{ route('admin.bookstore.orders') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $order->order_number }}</strong>
                                        </td>
                                        <td>
                                            <div>{{ $order->customer_name }}</div>
                                            <small class="text-muted">{{ $order->customer_email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $order->items->count() }} items
                                            </span>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($order->total, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $order->status_badge_color }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $order->payment_status_badge_color }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $order->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.bookstore.orders.show', $order) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="col-lg-4 mb-4">
            <!-- Best Sellers -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-star-fill text-warning"></i> Best Sellers
                    </h6>
                </div>
                <div class="card-body">
                    @if($bestSellers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($bestSellers as $book)
                            <div class="list-group-item px-0 border-0 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        @if($book->cover_image_path)
                                            <img src="{{ Storage::disk('public')->url($book->cover_image_path) }}" 
                                                 alt="{{ $book->title }}" 
                                                 class="rounded"
                                                 style="width: 40px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 60px;">
                                                <i class="bi bi-book text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-bold small">{{ Str::limit($book->title, 30) }}</div>
                                        <small class="text-muted">{{ $book->author }}</small>
                                        <div class="mt-1">
                                            <span class="badge bg-success small">{{ $book->sold_count }} sold</span>
                                            <span class="text-muted small ms-2">${{ number_format($book->sale_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <small>No sales yet</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning"></i> Stock Alerts
                    </h6>
                </div>
                <div class="card-body">
                    @if($lowStockBooks->count() > 0)
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-circle"></i>
                            <strong>{{ $lowStockBooks->count() }}</strong> books have low stock (≤5 units)
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($lowStockBooks as $book)
                            <div class="list-group-item px-0 border-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small">{{ Str::limit($book->title, 30) }}</div>
                                        <small class="text-muted">{{ $book->author }}</small>
                                    </div>
                                    <span class="badge bg-warning">{{ $book->stock_quantity }} left</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.bookstore.inventory') }}?stock_status=low" 
                               class="btn btn-sm btn-outline-warning w-100">
                                Manage Low Stock
                            </a>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-check-circle text-success fs-2"></i>
                            <p class="mb-0 mt-2 small">All books in stock</p>
                        </div>
                    @endif

                    @if($outOfStockBooks > 0)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-x-circle"></i>
                            <strong>{{ $outOfStockBooks }}</strong> books are out of stock
                            <a href="{{ route('admin.bookstore.inventory') }}?stock_status=out" 
                               class="alert-link ms-2">View</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning-fill text-primary"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.modules.library.books.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New Book
                        </a>
                        <a href="{{ route('admin.bookstore.inventory') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-seam"></i> Manage Inventory
                        </a>
                        <a href="{{ route('admin.bookstore.orders') }}?status=pending" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-clock"></i> View Pending Orders
                        </a>
                        <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-outline-info btn-sm" target="_blank">
                            <i class="bi bi-shop"></i> View Storefront
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection


