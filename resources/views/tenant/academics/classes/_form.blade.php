<div class="row g-3">
    {{-- Class Name --}}
    <div class="col-md-6">
        <label for="name" class="form-label">{{ __('Class Name') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $class->name ?? '') }}" required
            placeholder="{{ __('e.g., Senior 1, Primary 5, Grade 10') }}" autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">{{ __('Enter the full class name as it appears in your school system.') }}</div>
    </div>

    {{-- Class Code --}}
    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('Class Code') }}</label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $class->code ?? '') }}" placeholder="{{ __('e.g., S1, P5, G10') }}">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">{{ __('Optional short code for reports and quick reference.') }}</div>
    </div>

    {{-- Education Level --}}
    <div class="col-md-6">
        <label for="education_level_id" class="form-label">{{ __('Education Level') }}</label>
        <select class="form-select @error('education_level_id') is-invalid @enderror" id="education_level_id"
            name="education_level_id">
            <option value="">{{ __('-- Select Education Level --') }}</option>
            @foreach ($educationLevels as $level)
                <option value="{{ $level->id }}"
                    {{ old('education_level_id', $class->education_level_id ?? '') == $level->id ? 'selected' : '' }}>
                    {{ $level->name }}
                    @if ($level->min_grade && $level->max_grade)
                        ({{ __('Years') }} {{ $level->min_grade }}-{{ $level->max_grade }})
                    @endif
                </option>
            @endforeach
        </select>
        @error('education_level_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            {{ __('Select the education level this class belongs to (e.g., Primary, O-Level, A-Level).') }}
            @if ($educationLevels->isEmpty())
                <span class="text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ __('No education levels found. You may need to create them first.') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Capacity --}}
    <div class="col-md-6">
        <label for="capacity" class="form-label">{{ __('Class Capacity') }}</label>
        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
            name="capacity" value="{{ old('capacity', $class->capacity ?? 50) }}" min="1" max="500"
            placeholder="{{ __('50') }}">
        @error('capacity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">{{ __('Maximum number of students that can be enrolled in this class.') }}</div>
    </div>

    {{-- Description --}}
    <div class="col-12">
        <label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3" placeholder="{{ __('Add any additional information about this class...') }}">{{ old('description', $class->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">{{ __('Optional description or notes about this class.') }}</div>
    </div>

    {{-- Active Status (only show on edit) --}}
    @if (isset($class) && $class->exists)
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active"
                    name="is_active" value="1" {{ old('is_active', $class->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    {{ __('Active') }}
                </label>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    {{ __('Inactive classes are hidden from student enrollment and class selection lists.') }}</div>
            </div>
        </div>
    @endif
</div>

{{-- Info Alert --}}
<div class="alert alert-info mt-4 mb-0" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <strong>{{ __('Note:') }}</strong>
    @if (isset($class) && $class->exists)
        {{ __('After updating the class, you can manage class streams, assign subjects, and enroll students from the class details page.') }}
    @else
        {{ __('After creating the class, you can add class streams (e.g., A, B, East, West), assign subjects, and enroll students.') }}
    @endif
</div>
