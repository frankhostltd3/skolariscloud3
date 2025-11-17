{{-- Admin Bookstore Widget --}}
@php
    use App\Models\LibraryBook;
    use App\Models\BookstoreOrder;
    
    // Get featured books
    $featuredBooks = LibraryBook::forSale()
        ->featured()
        ->inStock()
        ->take(4)
        ->get();
    
    // Get bookstore stats
    $totalRevenue = BookstoreOrder::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
        ->sum('total');
    
    $todayOrders = BookstoreOrder::whereDate('created_at', today())->count();
    $pendingOrders = BookstoreOrder::where('status', 'pending')->count();
@endphp

<div class="card border-0 shadow-sm dashboard-card bookstore mb-4">
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-white bg-opacity-25 me-3">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h5 class="mb-0">Bookstore</h5>
                    <small class="opacity-75">Online Book Sales</small>
                </div>
            </div>
            <a href="{{ route('admin.bookstore.index') }}" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Stats Row -->
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="text-center p-2 bg-light rounded">
                    <div class="h4 mb-0 text-success fw-bold">${{ number_format($totalRevenue, 0) }}</div>
                    <small class="text-muted">Revenue</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center p-2 bg-light rounded">
                    <div class="h4 mb-0 text-primary fw-bold">{{ $todayOrders }}</div>
                    <small class="text-muted">Today</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center p-2 bg-light rounded">
                    <div class="h4 mb-0 text-warning fw-bold">{{ $pendingOrders }}</div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        
        <!-- Featured Books -->
        @if($featuredBooks->count() > 0)
        <div class="mb-3">
            <h6 class="text-muted small mb-2">
                <i class="bi bi-star-fill text-warning"></i> Featured Books
            </h6>
            <div class="row g-2">
                @foreach($featuredBooks as $book)
                <div class="col-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body p-2">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    @if($book->cover_image_path)
                                        <img src="{{ Storage::disk('public')->url($book->cover_image_path) }}" 
                                             alt="{{ $book->title }}" 
                                             class="rounded"
                                             style="width: 40px; height: 55px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary bg-opacity-25 rounded d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 55px;">
                                            <i class="bi bi-book text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="fw-bold small text-truncate">{{ Str::limit($book->title, 20) }}</div>
                                    <small class="text-muted d-block text-truncate">{{ Str::limit($book->author, 20) }}</small>
                                    <div class="mt-1">
                                        <span class="badge bg-success small">${{ number_format($book->sale_price, 2) }}</span>
                                        @if($book->stock_quantity <= 5 && $book->stock_quantity > 0)
                                            <span class="badge bg-warning small">Low</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="d-grid gap-2">
            <a href="{{ route('admin.bookstore.inventory') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-box-seam"></i> Manage Inventory
            </a>
            <a href="{{ route('admin.bookstore.orders') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-cart-check"></i> View Orders
            </a>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.dashboard-card.bookstore {
    border-left-color: #667eea;
}
</style>
