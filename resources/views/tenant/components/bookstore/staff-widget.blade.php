{{-- Staff Bookstore Widget --}}
@php
    use App\Models\LibraryBook;
    use App\Models\BookstoreOrder;
    use Illuminate\Support\Facades\Schema;
    
    // Check if tables exist before querying
    $tableExists = Schema::hasTable('library_books');
    
    if ($tableExists) {
        try {
            // Get bookstore stats for staff
            $booksForSale = LibraryBook::forSale()->count();
            $inStockBooks = LibraryBook::forSale()->inStock()->count();
            $lowStockBooks = LibraryBook::forSale()
                ->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 5)
                ->count();
            
            $todayOrders = BookstoreOrder::whereDate('created_at', today())->count();
            $todayRevenue = BookstoreOrder::whereDate('created_at', today())
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->sum('total');
            
            $pendingOrders = BookstoreOrder::where('status', 'pending')->count();
            
            // Get featured books
            $featuredBooks = LibraryBook::forSale()
                ->featured()
                ->inStock()
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            $tableExists = false;
        }
    } else {
        $booksForSale = 0;
        $inStockBooks = 0;
        $lowStockBooks = 0;
        $todayOrders = 0;
        $todayRevenue = 0;
        $pendingOrders = 0;
        $featuredBooks = collect();
    }
@endphp

@if(!$tableExists)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-staff text-white py-3">
        <h5 class="mb-0">
            <i class="bi bi-shop me-2"></i> Bookstore
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i>
            <strong>Bookstore module is being set up.</strong>
            <p class="mb-0 small mt-2">Please run the database migrations to activate this feature.</p>
        </div>
    </div>
</div>
@else

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-staff text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bi bi-shop me-2"></i> Bookstore Overview
                </h5>
                <small class="opacity-75">Sales and Inventory Summary</small>
            </div>
            <a href="{{ route('tenant.bookstore.index') }}" 
               class="btn btn-sm btn-light"
               target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Key Metrics -->
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h5 mb-0 fw-bold text-primary">{{ $booksForSale }}</div>
                    <small class="text-muted">Books</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h5 mb-0 fw-bold text-success">{{ $inStockBooks }}</div>
                    <small class="text-muted">In Stock</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h5 mb-0 fw-bold text-info">{{ $todayOrders }}</div>
                    <small class="text-muted">Today</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h5 mb-0 fw-bold text-warning">{{ $pendingOrders }}</div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        
        <!-- Today's Revenue -->
        @if($todayRevenue > 0)
        <div class="alert alert-success mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-currency-dollar"></i>
                    <strong>Today's Revenue:</strong>
                </div>
                <div class="h5 mb-0">${{ number_format($todayRevenue, 2) }}</div>
            </div>
        </div>
        @endif
        
        <!-- Low Stock Alert -->
        @if($lowStockBooks > 0)
        <div class="alert alert-warning mb-3">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>{{ $lowStockBooks }}</strong> book{{ $lowStockBooks > 1 ? 's' : '' }} with low stock (≤5 units)
        </div>
        @endif
        
        <!-- Featured Books Preview -->
        @if($featuredBooks->count() > 0)
        <div class="mb-3">
            <h6 class="text-muted small mb-2">
                <i class="bi bi-star-fill text-warning"></i> Featured Books
            </h6>
            <div class="list-group list-group-flush">
                @foreach($featuredBooks as $book)
                <div class="list-group-item px-0 border-bottom">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            @if($book->cover_image_path)
                                <img src="{{ Storage::disk('public')->url($book->cover_image_path) }}" 
                                     alt="{{ $book->title }}" 
                                     class="rounded"
                                     style="width: 45px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 60px;">
                                    <i class="bi bi-book text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold small">{{ Str::limit($book->title, 35) }}</div>
                            <small class="text-muted d-block">{{ $book->author }}</small>
                            <div class="mt-1">
                                <span class="badge bg-success small">${{ number_format($book->sale_price, 2) }}</span>
                                @if($book->discount_percentage > 0)
                                    <span class="badge bg-danger small">-{{ $book->discount_percentage }}%</span>
                                @endif
                                <span class="badge bg-light text-dark small">
                                    <i class="bi bi-box-seam"></i> {{ $book->stock_quantity }} in stock
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Quick Links for Staff -->
        <div class="d-grid gap-2">
            <a href="{{ route('tenant.bookstore.index') }}" 
               class="btn btn-sm btn-outline-primary"
               target="_blank">
                <i class="bi bi-shop"></i> View Storefront
            </a>
            <a href="{{ route('tenant.bookstore.cart') }}" 
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-cart3"></i> My Cart
            </a>
        </div>
    </div>
</div>
@endif

<style>
.bg-gradient-staff {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
</style>
