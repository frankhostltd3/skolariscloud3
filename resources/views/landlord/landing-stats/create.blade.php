@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Stat</h1>
        <a href="{{ route('landlord.landing-stats.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('landlord.landing-stats.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('value') is-invalid @enderror" id="value"
                        name="value" value="{{ old('value') }}" required placeholder="e.g., 500+">
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('label') is-invalid @enderror" id="label"
                        name="label" value="{{ old('label') }}" required placeholder="e.g., Schools Trust Us">
                    @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Icon (Bootstrap Icons Class)</label>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon"
                        name="icon" value="{{ old('icon') }}" placeholder="e.g., bi-building">
                    <div class="form-text">Optional. Use Bootstrap Icons classes.</div>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order"
                            name="sort_order" value="{{ old('sort_order', 0) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Create Stat</button>
            </form>
        </div>
    </div>
@endsection
