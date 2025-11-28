@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.bookstore.index') }}">Bookstore</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $book->title }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <div class="col-md-4">
                @if ($book->cover_image_url)
                    <img src="{{ $book->cover_image_url }}" class="img-fluid rounded shadow" alt="{{ $book->title }}">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm"
                        style="height: 400px;">
                        <i class="bi bi-book text-muted" style="font-size: 5rem;"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <h1 class="display-6 fw-bold mb-2">{{ $book->title }}</h1>
                <p class="lead text-muted mb-4">by {{ $book->author }}</p>

                <div class="d-flex align-items-center mb-4">
                    <h2 class="text-primary mb-0 me-3">{{ format_money($book->final_price) }}</h2>
                    @if ($book->discount_percentage > 0)
                        <div>
                            <span
                                class="text-decoration-line-through text-muted">{{ format_money($book->sale_price) }}</span>
                            <span class="badge bg-danger ms-2">{{ $book->discount_percentage }}% OFF</span>
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    @if ($book->stock_quantity > 0)
                        <span class="badge bg-success mb-3">In Stock ({{ $book->stock_quantity }} available)</span>
                        <form action="{{ route('tenant.bookstore.cart.add', $book) }}" method="POST"
                            class="d-flex gap-3 align-items-center" style="max-width: 300px;">
                            @csrf
                            <input type="number" name="quantity" class="form-control w-25" value="1" min="1"
                                max="{{ $book->stock_quantity }}">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>This book is currently out of stock.
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $book->description ?? 'No description available.' }}</p>
                </div>

                <div class="row g-3 text-muted small">
                    <div class="col-6 col-md-4">
                        <strong>ISBN:</strong><br>
                        {{ $book->isbn ?? 'N/A' }}
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Publisher:</strong><br>
                        {{ $book->publisher ?? 'N/A' }}
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Year:</strong><br>
                        {{ $book->publication_year ?? 'N/A' }}
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Pages:</strong><br>
                        {{ $book->pages ?? 'N/A' }}
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Language:</strong><br>
                        {{ $book->language ?? 'N/A' }}
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Category:</strong><br>
                        {{ $book->category }}
                    </div>
                </div>
            </div>
        </div>

        @if ($relatedBooks->count() > 0)
            <div class="mt-5 pt-5 border-top">
                <h3 class="mb-4">Related Books</h3>
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    @foreach ($relatedBooks as $related)
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0">
                                @if ($related->cover_image_url)
                                    <img src="{{ $related->cover_image_url }}" class="card-img-top"
                                        alt="{{ $related->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="height: 200px;">
                                        <i class="bi bi-book text-muted fs-1"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title text-truncate">{{ $related->title }}</h6>
                                    <p class="card-text text-muted small">{{ $related->author }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="fw-bold text-primary">{{ format_money($related->final_price) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('tenant.bookstore.show', $related) }}" class="stretched-link"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
