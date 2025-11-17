@extends('tenant.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Book: {{ $book->title ?? ('#'.$id) }}</h1>
        <div>
            <a href="{{ route('tenant.modules.bookstore.books.edit', $id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            <form action="{{ route('tenant.modules.bookstore.books.destroy', $id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this book?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">SKU</dt>
                <dd class="col-sm-9">{{ $book->sku }}</dd>
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $book->title }}</dd>
                <dt class="col-sm-3">Author</dt>
                <dd class="col-sm-9">{{ $book->author }}</dd>
                <dt class="col-sm-3">Price</dt>
                <dd class="col-sm-9">{{ number_format((float)$book->price, 2) }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $book->is_published ? 'Published' : 'Draft' }}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $book->description }}</dd>
            </dl>
        </div>
    </div>
    <div class="mt-3">
    <a href="{{ route('tenant.modules.bookstore.books.index') }}" class="link-secondary">Back to list</a>
    </div>
</div>
@endsection
