@extends('tenant.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Books</h1>
    <a href="{{ route('tenant.modules.bookstore.books.create') }}" class="btn btn-primary">Add Book</a>
    </div>

    <div class="card mb-3">
            <div class="card-body">
                    <form class="row g-2" method="get" action="{{ route('tenant.modules.bookstore.books.index') }}">
                            <div class="col-md-6">
                                    <input type="text" class="form-control" name="q" value="{{ $q ?? '' }}" placeholder="Search by title, author, or SKU">
                            </div>
                            <div class="col-md-3">
                                    <select class="form-select" name="published">
                                            <option value="">Any status</option>
                                            <option value="1" {{ (string)($published ?? '')==='1' ? 'selected' : '' }}>Published</option>
                                            <option value="0" {{ (string)($published ?? '')==='0' ? 'selected' : '' }}>Unpublished</option>
                                    </select>
                            </div>
                            <div class="col-md-3 d-grid">
                                    <button class="btn btn-outline-secondary">Filter</button>
                            </div>
                    </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th class="text-end">Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($books ?? [] as $b)
                        <tr>
                            <td><a href="{{ route('tenant.modules.bookstore.books.show', $b) }}">{{ $b->sku }}</a></td>
                            <td>{{ $b->title }}</td>
                            <td>{{ $b->author }}</td>
                            <td class="text-end">{{ number_format((float)$b->price, 2) }}</td>
                            <td>
                                @if($b->is_published)
                                    <span class="badge text-bg-success">Published</span>
                                @else
                                    <span class="badge text-bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('tenant.modules.bookstore.books.edit', $b) }}">Edit</a>
                                <form action="{{ route('tenant.modules.bookstore.books.destroy', $b) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this book?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No books found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if(($books ?? null) && $books->hasPages())
                <div class="card-footer">{{ $books->links() }}</div>
            @endif
        </div>
    <div class="mt-3">
    <a href="{{ route('tenant.modules.bookstore.index') }}" class="link-secondary">Back to Bookstore</a>
    </div>
</div>
@endsection
