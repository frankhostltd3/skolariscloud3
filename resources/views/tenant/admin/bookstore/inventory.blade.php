@extends('tenant.layouts.app')

@section('title', 'Inventory Management')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-box-seam"></i> Inventory Management
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.bookstore.index') }}">Bookstore</a>
                    </li>
                    <li class="breadcrumb-item active">Inventory</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('tenant.modules.library.books.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Book
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookstore.inventory') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search by title, author, ISBN..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" 
                                    {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock Status Filter -->
                <div class="col-md-3">
                    <select name="stock_status" class="form-select">
                        <option value="">All Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                            In Stock
                        </option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>
                            Low Stock (≤5)
                        </option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>
                            Out of Stock
                        </option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.bookstore.inventory') }}" 
                           class="btn btn-outline-secondary"
                           title="Clear Filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i> Books Inventory
                </h5>
                <span class="badge bg-secondary">{{ $books->total() }} books</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($books->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Book Details</th>
                                <th>Category</th>
                                <th style="width: 120px;">Price</th>
                                <th style="width: 100px;">Stock</th>
                                <th style="width: 80px;">Sold</th>
                                <th style="width: 100px;">Featured</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                            <tr>
                                <!-- Cover Image -->
                                <td>
                                    @if($book->cover_image_path)
                                        <img src="{{ Storage::disk('public')->url($book->cover_image_path) }}" 
                                             alt="{{ $book->title }}" 
                                             class="rounded"
                                             style="width: 50px; height: 70px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 70px;">
                                            <i class="bi bi-book text-muted"></i>
                                        </div>
                                    @endif
                                </td>

                                <!-- Book Details -->
                                <td>
                                    <div class="fw-bold">{{ $book->title }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> {{ $book->author }}
                                    </small>
                                    @if($book->isbn)
                                        <br>
                                        <small class="text-muted">ISBN: {{ $book->isbn }}</small>
                                    @endif
                                </td>

                                <!-- Category -->
                                <td>
                                    <span class="badge bg-light text-dark">{{ $book->category ?? 'N/A' }}</span>
                                </td>

                                <!-- Price -->
                                <td>
                                    <div class="fw-bold">${{ number_format($book->sale_price, 2) }}</div>
                                    @if($book->discount_percentage > 0)
                                        <small class="text-success">
                                            -{{ $book->discount_percentage }}% off
                                        </small>
                                    @endif
                                </td>

                                <!-- Stock -->
                                <td>
                                    <div class="stock-update-form">
                                        <form method="POST" 
                                              action="{{ route('admin.bookstore.books.stock', $book) }}"
                                              class="d-flex align-items-center gap-1">
                                            @csrf
                                            <input type="number" 
                                                   name="stock_quantity" 
                                                   value="{{ $book->stock_quantity }}" 
                                                   class="form-control form-control-sm text-center stock-input"
                                                   style="width: 60px;"
                                                   min="0">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-primary stock-update-btn"
                                                    title="Update Stock">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        @if($book->stock_quantity == 0)
                                            <span class="badge bg-danger mt-1 w-100">Out</span>
                                        @elseif($book->stock_quantity <= 5)
                                            <span class="badge bg-warning mt-1 w-100">Low</span>
                                        @else
                                            <span class="badge bg-success mt-1 w-100">In Stock</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Sold Count -->
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $book->sold_count }}</span>
                                </td>

                                <!-- Featured Toggle -->
                                <td>
                                    <form method="POST" 
                                          action="{{ route('admin.bookstore.books.featured', $book) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $book->is_featured ? 'btn-warning' : 'btn-outline-secondary' }} w-100">
                                            <i class="bi bi-star{{ $book->is_featured ? '-fill' : '' }}"></i>
                                            {{ $book->is_featured ? 'Featured' : 'Feature' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('tenant.bookstore.show', $book) }}" 
                                           class="btn btn-outline-info"
                                           title="View in Store"
                                           target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('tenant.modules.library.books.edit', $book) }}" 
                                           class="btn btn-outline-primary"
                                           title="Edit Book">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white">
                    {{ $books->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">No Books Found</h5>
                    <p class="text-muted">No books match your search criteria.</p>
                    <a href="{{ route('admin.bookstore.inventory') }}" class="btn btn-outline-secondary mt-2">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stock-input:focus {
    box-shadow: none;
    border-color: #0d6efd;
}

.stock-update-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.stock-update-form {
    min-width: 100px;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
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

