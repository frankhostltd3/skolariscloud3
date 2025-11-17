@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1">
                    <i class="bi bi-{{ isset($book) ? 'pencil' : 'plus-circle' }} me-2"></i>
                    {{ isset($book) ? 'Edit Book' : 'Add New Book' }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.index') }}">Library</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.books.index') }}">Books</a></li>
                        <li class="breadcrumb-item active">{{ isset($book) ? 'Edit' : 'Add' }}</li>
                    </ol>
                </nav>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ isset($book) ? route('tenant.modules.library.books.update', $book) : route('tenant.modules.library.books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($book))
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <!-- Title -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Book Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $book->title ?? '') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Author -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Author <span class="text-danger">*</span></label>
                                <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" 
                                       value="{{ old('author', $book->author ?? '') }}" required>
                                @error('author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ISBN -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ISBN</label>
                                <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" 
                                       value="{{ old('isbn', $book->isbn ?? '') }}">
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" 
                                       value="{{ old('category', $book->category ?? 'General') }}" required
                                       list="categoryList">
                                <datalist id="categoryList">
                                    <option value="Fiction">
                                    <option value="Non-Fiction">
                                    <option value="Science">
                                    <option value="History">
                                    <option value="Mathematics">
                                    <option value="Literature">
                                    <option value="Biography">
                                    <option value="Reference">
                                </datalist>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', $book->quantity ?? 1) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Publisher -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Publisher</label>
                                <input type="text" name="publisher" class="form-control @error('publisher') is-invalid @enderror" 
                                       value="{{ old('publisher', $book->publisher ?? '') }}">
                                @error('publisher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Publication Year -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Publication Year</label>
                                <input type="number" name="publication_year" class="form-control @error('publication_year') is-invalid @enderror" 
                                       value="{{ old('publication_year', $book->publication_year ?? '') }}" 
                                       min="1800" max="{{ date('Y') }}">
                                @error('publication_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Language -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Language</label>
                                <input type="text" name="language" class="form-control @error('language') is-invalid @enderror" 
                                       value="{{ old('language', $book->language ?? 'English') }}">
                                @error('language')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pages -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pages</label>
                                <input type="number" name="pages" class="form-control @error('pages') is-invalid @enderror" 
                                       value="{{ old('pages', $book->pages ?? '') }}" min="1">
                                @error('pages')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Purchase Price -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Purchase Price</label>
                                <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" 
                                       value="{{ old('purchase_price', $book->purchase_price ?? '') }}" 
                                       step="0.01" min="0">
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Shelf Location</label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                       value="{{ old('location', $book->location ?? '') }}"
                                       placeholder="e.g., Shelf A-3, Section B">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status (only for edit) -->
                            @if(isset($book))
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available" {{ old('status', $book->status) === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="maintenance" {{ old('status', $book->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="lost" {{ old('status', $book->status) === 'lost' ? 'selected' : '' }}>Lost</option>
                                    <option value="damaged" {{ old('status', $book->status) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <!-- Bookstore Section -->
                            <div class="col-12">
                                <hr class="my-4">
                                <h5 class="mb-3"><i class="bi bi-shop me-2"></i>Bookstore Settings</h5>
                            </div>

                            <!-- Available for Sale -->
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_for_sale" class="form-check-input" id="is_for_sale"
                                           value="1" {{ old('is_for_sale', $book->is_for_sale ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_for_sale">
                                        <strong>Available for Sale in Bookstore</strong>
                                        <small class="d-block text-muted">Check this to make the book available for purchase</small>
                                    </label>
                                </div>
                            </div>

                            <div id="bookstore-fields" style="display: {{ old('is_for_sale', $book->is_for_sale ?? false) ? 'contents' : 'none' }}">
                                <!-- Cover Image Upload -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Cover Image</label>
                                    <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" 
                                           accept="image/*" id="cover_image_input">
                                    @if(isset($book) && $book->cover_image_url)
                                        <div class="mt-2">
                                            <img src="{{ $book->cover_image_url }}" alt="Current Cover" class="img-thumbnail" style="max-width: 150px;">
                                        </div>
                                    @endif
                                    <div class="mt-1" id="image_preview"></div>
                                    @error('cover_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Recommended size: 400x600px</small>
                                </div>

                                <!-- Sale Price -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Sale Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" 
                                               value="{{ old('sale_price', $book->sale_price ?? '') }}" 
                                               step="0.01" min="0">
                                    </div>
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Discount Percentage -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Discount %</label>
                                    <div class="input-group">
                                        <input type="number" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                               value="{{ old('discount_percentage', $book->discount_percentage ?? 0) }}" 
                                               step="0.01" min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Stock Quantity -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           value="{{ old('stock_quantity', $book->stock_quantity ?? 0) }}" 
                                           min="0">
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Featured Book -->
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured"
                                               value="1" {{ old('is_featured', $book->is_featured ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            <strong>Featured Book</strong>
                                            <small class="d-block text-muted">Display this book prominently in the bookstore</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Short Description -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Short Description (Bookstore Preview)</label>
                                    <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" 
                                              rows="2" maxlength="200" placeholder="Brief description for bookstore listing">{{ old('short_description', $book->short_description ?? '') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maximum 200 characters</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="my-4">
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4">{{ old('description', $book->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('tenant.modules.library.books.index') }}" class="btn btn-light">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ isset($book) ? 'Update Book' : 'Add Book' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle bookstore fields
document.getElementById('is_for_sale').addEventListener('change', function() {
    const bookstoreFields = document.getElementById('bookstore-fields');
    bookstoreFields.style.display = this.checked ? 'contents' : 'none';
});

// Preview cover image
document.getElementById('cover_image_input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image_preview').innerHTML = 
                '<img src="' + e.target.result + '" class="img-thumbnail mt-2" style="max-width: 150px;">';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush

@endsection
