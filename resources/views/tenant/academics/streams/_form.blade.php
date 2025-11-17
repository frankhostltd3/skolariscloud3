<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">
            {{ __('Stream Name') }} <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $stream->name ?? '') }}" placeholder="A, B, East, Red, etc." required maxlength="100">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">{{ __('Enter a unique name for this stream (e.g., A, B, East, West).') }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('Stream Code') }}</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $stream->code ?? '') }}" placeholder="Optional" maxlength="50">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">{{ __('Short code for the stream (optional).') }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="capacity" class="form-label">{{ __('Capacity') }}</label>
        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
            name="capacity" value="{{ old('capacity', $stream->capacity ?? '') }}" placeholder="50" min="1"
            max="500">
        @error('capacity')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">{{ __('Maximum number of students in this stream.') }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="is_active" class="form-label">{{ __('Status') }}</label>
        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
            <option value="1" {{ old('is_active', $stream->is_active ?? true) ? 'selected' : '' }}>
                {{ __('Active') }}</option>
            <option value="0" {{ old('is_active', $stream->is_active ?? true) ? '' : 'selected' }}>
                {{ __('Inactive') }}</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">{{ __('Active streams can enroll students.') }}</small>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3" maxlength="500">{{ old('description', $stream->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">{{ __('Optional description or notes about this stream.') }}</small>
        @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>{{ $buttonText }}
    </button>
    <a href="{{ route('tenant.academics.streams.index', $class) }}" class="btn btn-outline-secondary">
        {{ __('Cancel') }}
    </a>
</div>

