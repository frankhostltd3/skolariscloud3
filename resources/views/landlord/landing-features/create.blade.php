@extends('landlord.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Add New Feature</h1>
            <a href="{{ route('landlord.landing-features.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('landlord.landing-features.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icon Class (Bootstrap Icons) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon"
                                name="icon" value="{{ old('icon') }}" placeholder="bi-star-fill" required>
                            <div class="form-text">Example: bi-people-fill, bi-calendar-check</div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon_color" class="form-label">Icon Color Class or Value</label>
                            <input type="text" class="form-control @error('icon_color') is-invalid @enderror"
                                id="icon_color" name="icon_color" value="{{ old('icon_color') }}"
                                placeholder="text-primary or #FF0000">
                            <div class="form-text">Can be a Bootstrap class (text-primary) or CSS variable
                                (var(--accent-color)).</div>
                            @error('icon_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="icon_bg_color" class="form-label">Icon Background Color</label>
                            <input type="text" class="form-control @error('icon_bg_color') is-invalid @enderror"
                                id="icon_bg_color" name="icon_bg_color" value="{{ old('icon_bg_color') }}"
                                placeholder="rgba(79, 70, 229, 0.1)">
                            <div class="form-text">CSS color value (hex, rgb, rgba).</div>
                            @error('icon_bg_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Feature</button>
                </form>
            </div>
        </div>
    </div>
@endsection
