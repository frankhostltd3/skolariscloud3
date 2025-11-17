@extends('tenant.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Add Book</h1>
    <form method="POST" action="{{ route('tenant.modules.bookstore.books.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" name="sku" class="form-control" required value="{{ old('sku') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" value="{{ old('author') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', '0.00') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="is_published" name="is_published" {{ old('is_published') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">Published</label>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">Featured</label>
        </div>
        <button class="btn btn-primary">Save</button>
    <a href="{{ route('tenant.modules.bookstore.books.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
