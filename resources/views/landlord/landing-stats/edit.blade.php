@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Stat</h1>
        <a href="{{ route('landlord.landing-stats.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('landlord.landing-stats.update', $landingStat->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('value') is-invalid @enderror" id="value"
                        name="value" value="{{ old('value', $landingStat->value) }}" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('label') is-invalid @enderror" id="label"
                        name="label" value="{{ old('label', $landingStat->label) }}" required>
                    @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Icon (Bootstrap Icons Class)</label>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon"
                        name="icon" value="{{ old('icon', $landingStat->icon) }}">
                    <div class="form-text">Optional. Use Bootstrap Icons classes.</div>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order"
                            name="sort_order" value="{{ old('sort_order', $landingStat->sort_order) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                        {{ old('is_active', $landingStat->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Stat</button>
            </form>
        </div>
    </div>
@endsection
