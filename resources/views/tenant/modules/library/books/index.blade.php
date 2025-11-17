@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-book me-2"></i>Library Books</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.index') }}">Library</a></li>
                    <li class="breadcrumb-item active">Books</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('tenant.modules.library.books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Add New Book
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.modules.library.books.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by title, author, ISBN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Books List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>ISBN</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Available</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $book->title }}</div>
                                @if($book->publisher)
                                <small class="text-muted">{{ $book->publisher }}</small>
                                @endif
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $book->category }}</span>
                            </td>
                            <td>
                                @if($book->isbn)
                                <code class="small">{{ $book->isbn }}</code>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $book->quantity }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $book->available_quantity > 0 ? 'success' : 'danger' }}">
                                    {{ $book->available_quantity }}
                                </span>
                            </td>
                            <td>
                                @if($book->status === 'available')
                                <span class="badge bg-success">Available</span>
                                @elseif($book->status === 'maintenance')
                                <span class="badge bg-warning">Maintenance</span>
                                @elseif($book->status === 'lost')
                                <span class="badge bg-danger">Lost</span>
                                @elseif($book->status === 'damaged')
                                <span class="badge bg-danger">Damaged</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tenant.modules.library.books.show', $book) }}" class="btn btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.modules.library.books.edit', $book) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" 
                                            onclick="deleteBook({{ $book->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                
                                <form id="delete-form-{{ $book->id }}" action="{{ route('tenant.modules.library.books.destroy', $book) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted mb-3">No books found</p>
                                <a href="{{ route('tenant.modules.library.books.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Add Your First Book
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($books->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $books->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function deleteBook(id) {
    if (confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
