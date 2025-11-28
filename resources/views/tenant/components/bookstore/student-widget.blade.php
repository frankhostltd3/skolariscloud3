{{-- Student Bookstore Widget --}}
@php
    use App\Models\LibraryBook;
    use Illuminate\Support\Facades\Schema;

    // Get featured books for students (only if bookstore tables exist)
    $featuredBooks = collect();
    if (Schema::hasTable('library_books')) {
        try {
            $featuredBooks = LibraryBook::forSale()->featured()->inStock()->take(6)->get();
        } catch (\Throwable $e) {
            $featuredBooks = collect();
        }
    }

    // Get cart item count from session
    $cartItems = session('cart', []);
    $cartCount = array_sum(array_column($cartItems, 'quantity'));
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-bookstore text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bi bi-shop me-2"></i> School Bookstore
                </h5>
                <small class="opacity-75">Shop for textbooks and materials</small>
            </div>
            @if ($cartCount > 0)
                <a href="{{ route('tenant.bookstore.cart') }}" class="btn btn-sm btn-light position-relative">
                    <i class="bi bi-cart3"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $cartCount }}
                    </span>
                </a>
            @endif
        </div>
    </div>

    <div class="card-body">
        @if ($featuredBooks->count() > 0)
            <h6 class="text-muted small mb-3">
                <i class="bi bi-star-fill text-warning"></i> Featured Books
            </h6>

            <div class="row g-3 mb-3">
                @foreach ($featuredBooks as $book)
                    <div class="col-md-4 col-sm-6">
                        <div class="card border-0 h-100 shadow-sm bookstore-book-card">
                            <div class="position-relative">
                                @if ($book->cover_image_path)
                                    <img src="{{ Storage::disk('public')->url($book->cover_image_path) }}"
                                        alt="{{ $book->title }}" class="card-img-top"
                                        style="height: 180px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="height: 180px;">
                                        <i class="bi bi-book fs-1 text-muted"></i>
                                    </div>
                                @endif

                                @if ($book->discount_percentage > 0)
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                        -{{ $book->discount_percentage }}%
                                    </span>
                                @endif

                                <span class="position-absolute top-0 start-0 m-2 badge bg-warning">
                                    <i class="bi bi-star-fill"></i> Featured
                                </span>
                            </div>

                            <div class="card-body p-3">
                                <h6 class="card-title mb-1 text-truncate" title="{{ $book->title }}">
                                    {{ Str::limit($book->title, 30) }}
                                </h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-person"></i> {{ Str::limit($book->author, 25) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if ($book->discount_percentage > 0)
                                            <div>
                                                <span class="text-decoration-line-through text-muted small">
                                                    ${{ number_format($book->sale_price, 2) }}
                                                </span>
                                                <div class="fw-bold text-success">
                                                    ${{ number_format($book->final_price, 2) }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="fw-bold text-primary">
                                                ${{ number_format($book->sale_price, 2) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        @if ($book->stock_quantity > 0)
                                            <form action="{{ route('tenant.bookstore.cart.add', $book) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm btn-primary"
                                                    title="Add to Cart">
                                                    <i class="bi bi-cart-plus"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($book->stock_quantity > 0 && $book->stock_quantity <= 5)
                                    <div class="mt-2">
                                        <small class="text-warning">
                                            <i class="bi bi-exclamation-circle"></i>
                                            Only {{ $book->stock_quantity }} left
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-white border-0 p-2">
                                <a href="{{ route('tenant.bookstore.show', $book) }}"
                                    class="btn btn-sm btn-outline-secondary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Browse All Books Button -->
            <div class="text-center">
                <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Browse All Books
                </a>
                @if ($cartCount > 0)
                    <a href="{{ route('tenant.bookstore.cart') }}" class="btn btn-outline-primary">
                        <i class="bi bi-cart3"></i> View Cart ({{ $cartCount }})
                    </a>
                @endif
            </div>
        @else
            <!-- No Books Message -->
            <div class="text-center py-4">
                <i class="bi bi-shop text-muted" style="font-size: 3rem;"></i>
                <h6 class="mt-3 text-muted">No Featured Books Available</h6>
                <p class="text-muted small">Check back soon for new books!</p>
                <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-search"></i> Browse All Books
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .bg-gradient-bookstore {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .bookstore-book-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .bookstore-book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
</style>
