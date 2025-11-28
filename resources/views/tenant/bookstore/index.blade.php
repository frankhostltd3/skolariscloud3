@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold">{{ setting('school_name') }} Bookstore</h1>
                <p class="lead text-muted">Browse our collection of books and resources</p>
            </div>
            <div>
                <a href="{{ route('tenant.bookstore.cart') }}" class="btn btn-outline-primary position-relative">
                    <i class="bi bi-cart3 me-2"></i>Cart
                    @if (session('bookstore_cart'))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ count(session('bookstore_cart')) }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <form action="{{ route('tenant.bookstore.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search books..."
                        value="{{ request('search') }}">
                    <select name="category" class="form-select w-auto">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Sort By
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort_by' => 'newest']) }}">Newest</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort_by' => 'price_low']) }}">Price: Low to High</a>
                        </li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort_by' => 'price_high']) }}">Price: High to
                                Low</a></li>
                        <li><a class="dropdown-item"
                                href="{{ request()->fullUrlWithQuery(['sort_by' => 'popular']) }}">Popular</a></li>
                    </ul>
                </div>
            </div>
        </div>

        @if ($featuredBooks->count() > 0 && !request('search') && !request('category'))
            <div class="mb-5">
                <h3 class="mb-3">Featured Books</h3>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach ($featuredBooks as $book)
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0">
                                @if ($book->cover_image_url)
                                    <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}"
                                        style="height: 250px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="height: 250px;">
                                        <i class="bi bi-book text-muted fs-1"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">{{ $book->title }}</h5>
                                    <p class="card-text text-muted small">{{ $book->author }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="h5 mb-0 text-primary">{{ format_money($book->final_price) }}</span>
                                        @if ($book->discount_percentage > 0)
                                            <span
                                                class="text-decoration-line-through text-muted small">{{ format_money($book->sale_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="{{ route('tenant.bookstore.show', $book) }}"
                                        class="btn btn-outline-primary w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <h3 class="mb-3">All Books</h3>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            @forelse($books as $book)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        @if ($book->cover_image_url)
                            <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}"
                                style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-book text-muted fs-1"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h6 class="card-title text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
                            <p class="card-text text-muted small mb-2">{{ $book->author }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">{{ format_money($book->final_price) }}</span>
                                @if ($book->stock_quantity > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success">In Stock</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('tenant.bookstore.show', $book) }}"
                                class="btn btn-sm btn-outline-primary w-100">View</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search fs-1 text-muted mb-3"></i>
                    <h4>No books found</h4>
                    <p class="text-muted">Try adjusting your search or filters</p>
                    <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-primary">Clear Filters</a>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $books->links() }}
        </div>
    </div>
@endsection
