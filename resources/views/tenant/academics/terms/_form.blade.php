<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">{{ __('Term Name') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $term->name ?? '') }}" required>
        <div class="form-text">{{ __('e.g., Term 1, Semester 1, Fall 2025') }}</div>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('Term Code') }}</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $term->code ?? '') }}">
        <div class="form-text">{{ __('e.g., T1, S1, FALL2025 (Optional)') }}</div>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="academic_year" class="form-label">{{ __('Academic Year') }} <span
                class="text-danger">*</span></label>
        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year"
            name="academic_year" value="{{ old('academic_year', $term->academic_year ?? '') }}" required>
        <div class="form-text">{{ __('Format: 2025 or 2024/2025') }}</div>
        @error('academic_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="start_date" class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date"
            name="start_date" value="{{ old('start_date', isset($term) ? $term->start_date->format('Y-m-d') : '') }}"
            required>
        @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="end_date" class="form-label">{{ __('End Date') }} <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date"
            name="end_date" value="{{ old('end_date', isset($term) ? $term->end_date->format('Y-m-d') : '') }}"
            required>
        @error('end_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3">{{ old('description', $term->description ?? '') }}</textarea>
        <div class="form-text">{{ __('Optional notes about this term') }}</div>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_current" name="is_current" value="1"
                {{ old('is_current', $term->is_current ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_current">
                <i class="bi bi-star-fill text-warning me-1"></i>{{ __('Set as Current Term') }}
            </label>
            <div class="form-text">{{ __('Only one term can be current at a time') }}</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                {{ old('is_active', $term->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('Active') }}
            </label>
            <div class="form-text">{{ __('Inactive terms are hidden from selection') }}</div>
        </div>
    </div>
</div>
