<div class="row g-3">
    <div class="col-md-6"><label for="name" class="form-label">{{ __('Subject Name') }} <span
                class="text-danger">*</span></label><input type="text"
            class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $subject->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="code" class="form-label">{{ __('Subject Code') }}</label><input type="text"
            class="form-control @error('code') is-invalid @enderror" id="code" name="code"
            value="{{ old('code', $subject->code ?? '') }}" placeholder="MATH101">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="type" class="form-label">{{ __('Type') }} <span
                class="text-danger">*</span></label><select class="form-select @error('type') is-invalid @enderror"
            id="type" name="type" required>
            <option value="core" {{ old('type', $subject->type ?? '') == 'core' ? 'selected' : '' }}>
                {{ __('Core') }}</option>
            <option value="elective" {{ old('type', $subject->type ?? '') == 'elective' ? 'selected' : '' }}>
                {{ __('Elective') }}</option>
            <option value="optional" {{ old('type', $subject->type ?? '') == 'optional' ? 'selected' : '' }}>
                {{ __('Optional') }}</option>
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label for="education_level_id" class="form-label">{{ __('Education Level') }}</label><select
            class="form-select @error('education_level_id') is-invalid @enderror" id="education_level_id"
            name="education_level_id">
            <option value="">{{ __('-- Select Level --') }}</option>
            @foreach ($educationLevels as $level)
                <option value="{{ $level->id }}"
                    {{ old('education_level_id', $subject->education_level_id ?? '') == $level->id ? 'selected' : '' }}>
                    {{ $level->name }}</option>
            @endforeach
        </select>
        @error('education_level_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="credit_hours" class="form-label">{{ __('Credit Hours') }}</label><input
            type="number" class="form-control @error('credit_hours') is-invalid @enderror" id="credit_hours"
            name="credit_hours" value="{{ old('credit_hours', $subject->credit_hours ?? '') }}" min="0"
            max="100">
        @error('credit_hours')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="sort_order" class="form-label">{{ __('Sort Order') }}</label><input
            type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order"
            name="sort_order" value="{{ old('sort_order', $subject->sort_order ?? 0) }}" min="0">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="pass_mark" class="form-label">{{ __('Pass Mark') }}</label><input type="number"
            class="form-control @error('pass_mark') is-invalid @enderror" id="pass_mark" name="pass_mark"
            value="{{ old('pass_mark', $subject->pass_mark ?? 40) }}" min="0" max="100">
        @error('pass_mark')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="max_marks" class="form-label">{{ __('Maximum Marks') }}</label><input
            type="number" class="form-control @error('max_marks') is-invalid @enderror" id="max_marks" name="max_marks"
            value="{{ old('max_marks', $subject->max_marks ?? 100) }}" min="1" max="1000">
        @error('max_marks')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="is_active" class="form-label">{{ __('Status') }}</label><select
            class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
            <option value="1" {{ old('is_active', $subject->is_active ?? true) ? 'selected' : '' }}>
                {{ __('Active') }}</option>
            <option value="0" {{ old('is_active', $subject->is_active ?? true) ? '' : 'selected' }}>
                {{ __('Inactive') }}</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12"><label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3">{{ old('description', $subject->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i
            class="bi bi-check-circle me-1"></i>{{ $buttonText }}</button><a
        href="{{ route('tenant.academics.subjects.index') }}"
        class="btn btn-outline-secondary">{{ __('Cancel') }}</a></div>
