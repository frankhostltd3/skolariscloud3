<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $examinationBody->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('Code') }}</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $examinationBody->code ?? '') }}">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="country_id" class="form-label">{{ __('Country') }}</label>
        <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id">
            <option value="">{{ __('Select Country') }}</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ old('country_id', $examinationBody->country_id ?? '') == $country->id ? 'selected' : '' }}>
                    {{ $country->full_name }}
                </option>
            @endforeach
        </select>
        @error('country_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="website" class="form-label">{{ __('Website') }}</label>
        <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website"
            value="{{ old('website', $examinationBody->website ?? '') }}" placeholder="https://">
        @error('website')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" id="is_international" name="is_international" value="1"
                {{ old('is_international', $examinationBody->is_international ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_international">{{ __('International Body') }}</label>
        </div>
    </div>

    <div class="col-md-6">
        <label for="is_active" class="form-label">{{ __('Status') }}</label>
        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
            <option value="1" {{ old('is_active', $examinationBody->is_active ?? true) ? 'selected' : '' }}>
                {{ __('Active') }}</option>
            <option value="0" {{ old('is_active', $examinationBody->is_active ?? true) ? '' : 'selected' }}>
                {{ __('Inactive') }}</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3">{{ old('description', $examinationBody->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>{{ $buttonText }}</button>
    <a href="{{ route('tenant.academics.examination-bodies.index') }}"
        class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
</div>
