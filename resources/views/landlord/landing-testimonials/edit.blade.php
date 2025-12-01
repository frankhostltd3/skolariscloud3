@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Testimonial</h1>
        <a href="{{ route('landlord.landing-testimonials.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('landlord.landing-testimonials.update', $landingTestimonial->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $landingTestimonial->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <input type="text" class="form-control @error('role') is-invalid @enderror" id="role"
                        name="role" value="{{ old('role', $landingTestimonial->role) }}">
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="4"
                        required>{{ old('content', $landingTestimonial->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('rating') is-invalid @enderror" id="rating"
                        name="rating" value="{{ old('rating', $landingTestimonial->rating) }}" min="1"
                        max="5" required>
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="avatar_url" class="form-label">Avatar URL</label>
                    <input type="text" class="form-control @error('avatar_url') is-invalid @enderror" id="avatar_url"
                        name="avatar_url" value="{{ old('avatar_url', $landingTestimonial->avatar_url) }}">
                    <div class="form-text">Optional. URL to the user's avatar image.</div>
                    @error('avatar_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order"
                            name="sort_order" value="{{ old('sort_order', $landingTestimonial->sort_order) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                        {{ old('is_active', $landingTestimonial->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Testimonial</button>
            </form>
        </div>
    </div>
@endsection
