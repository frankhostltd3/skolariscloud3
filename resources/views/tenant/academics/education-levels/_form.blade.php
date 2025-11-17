<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">
            {{ __('Level Name') }} <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $educationLevel->name ?? '') }}" placeholder="Primary, Secondary, O-Level, etc."
            required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('Level Code') }}</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $educationLevel->code ?? '') }}" placeholder="P, S, O, A">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="min_grade" class="form-label">{{ __('Minimum Grade/Year') }}</label>
        <input type="number" class="form-control @error('min_grade') is-invalid @enderror" id="min_grade"
            name="min_grade" value="{{ old('min_grade', $educationLevel->min_grade ?? '') }}" placeholder="1"
            min="0" max="20">
        @error('min_grade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="max_grade" class="form-label">{{ __('Maximum Grade/Year') }}</label>
        <input type="number" class="form-control @error('max_grade') is-invalid @enderror" id="max_grade"
            name="max_grade" value="{{ old('max_grade', $educationLevel->max_grade ?? '') }}" placeholder="7"
            min="0" max="20">
        @error('max_grade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="sort_order" class="form-label">{{ __('Sort Order') }}</label>
        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order"
            name="sort_order" value="{{ old('sort_order', $educationLevel->sort_order ?? 0) }}" min="0">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">{{ __('Lower numbers appear first') }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="is_active" class="form-label">{{ __('Status') }}</label>
        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
            <option value="1" {{ old('is_active', $educationLevel->is_active ?? true) ? 'selected' : '' }}>
                {{ __('Active') }}</option>
            <option value="0" {{ old('is_active', $educationLevel->is_active ?? true) ? '' : 'selected' }}>
                {{ __('Inactive') }}</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3">{{ old('description', $educationLevel->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>{{ $buttonText }}
    </button>
    <a href="{{ url('/tenant/academics/education-levels') }}" class="btn btn-outline-secondary">
        {{ __('Cancel') }}
    </a>
</div>

