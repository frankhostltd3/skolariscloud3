<div class="row g-3">
    <div class="col-md-6"><label for="name" class="form-label">{{ __('Grading System Name') }} <span
                class="text-danger">*</span></label><input type="text"
            class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $gradingScheme->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label for="country" class="form-label">{{ __('Country') }}</label><input type="text"
            class="form-control @error('country') is-invalid @enderror" id="country" name="country"
            value="{{ old('country', $gradingScheme->country ?? '') }}" placeholder="e.g., Uganda, Kenya, UK">
        @error('country')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label for="examination_body_id"
            class="form-label">{{ __('Examination Body') }}</label><select
            class="form-select @error('examination_body_id') is-invalid @enderror" id="examination_body_id"
            name="examination_body_id">
            <option value="">{{ __('-- Select Examination Body --') }}</option>
            @foreach ($examinationBodies as $body)
                <option value="{{ $body->id }}"
                    {{ old('examination_body_id', $gradingScheme->examination_body_id ?? '') == $body->id ? 'selected' : '' }}>
                    {{ $body->name }} ({{ $body->code }})</option>
            @endforeach
        </select>
        @error('examination_body_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="is_current" class="form-label">{{ __('Set as Current') }}</label><select
            class="form-select @error('is_current') is-invalid @enderror" id="is_current" name="is_current">
            <option value="0" {{ old('is_current', $gradingScheme->is_current ?? false) ? '' : 'selected' }}>
                {{ __('No') }}</option>
            <option value="1" {{ old('is_current', $gradingScheme->is_current ?? false) ? 'selected' : '' }}>
                {{ __('Yes') }}</option>
        </select><small class="text-muted">{{ __('Used for automatic grading') }}</small>
        @error('is_current')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="is_active" class="form-label">{{ __('Status') }}</label><select
            class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
            <option value="1" {{ old('is_active', $gradingScheme->is_active ?? true) ? 'selected' : '' }}>
                {{ __('Active') }}</option>
            <option value="0" {{ old('is_active', $gradingScheme->is_active ?? true) ? '' : 'selected' }}>
                {{ __('Inactive') }}</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12"><label for="description" class="form-label">{{ __('Description') }}</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
            rows="3">{{ old('description', $gradingScheme->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mt-4">
    <h5 class="mb-3">{{ __('Grading Bands') }}</h5>
    <div id="bands-container">
        @if (isset($gradingScheme) && $gradingScheme->bands->count() > 0)
            @foreach ($gradingScheme->bands as $index => $band)
                <div class="band-row card mb-2">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2"><label class="form-label small">{{ __('Grade') }} <span
                                        class="text-danger">*</span></label><input type="text"
                                    class="form-control form-control-sm" name="bands[{{ $index }}][grade]"
                                    value="{{ old("bands.{$index}.grade", $band->grade) }}" placeholder="A" required>
                            </div>
                            <div class="col-md-2"><label class="form-label small">{{ __('Label') }}</label><input
                                    type="text" class="form-control form-control-sm"
                                    name="bands[{{ $index }}][label]"
                                    value="{{ old("bands.{$index}.label", $band->label) }}" placeholder="Distinction">
                            </div>
                            <div class="col-md-2"><label class="form-label small">{{ __('Min Score') }} <span
                                        class="text-danger">*</span></label><input type="number" step="0.01"
                                    class="form-control form-control-sm" name="bands[{{ $index }}][min_score]"
                                    value="{{ old("bands.{$index}.min_score", $band->min_score) }}" placeholder="80"
                                    required></div>
                            <div class="col-md-2"><label class="form-label small">{{ __('Max Score') }} <span
                                        class="text-danger">*</span></label><input type="number" step="0.01"
                                    class="form-control form-control-sm" name="bands[{{ $index }}][max_score]"
                                    value="{{ old("bands.{$index}.max_score", $band->max_score) }}" placeholder="100"
                                    required></div>
                            <div class="col-md-2"><label class="form-label small">{{ __('Grade Point') }}</label><input
                                    type="number" step="0.01" class="form-control form-control-sm"
                                    name="bands[{{ $index }}][grade_point]"
                                    value="{{ old("bands.{$index}.grade_point", $band->grade_point) }}"
                                    placeholder="4.0"></div>
                            <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100"
                                    onclick="removeBand(this)"><i class="bi bi-trash"></i></button></div>
                            <div class="col-12"><input type="text" class="form-control form-control-sm"
                                    name="bands[{{ $index }}][remarks]"
                                    value="{{ old("bands.{$index}.remarks", $band->remarks) }}"
                                    placeholder="{{ __('Remarks (optional)') }}"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="band-row card mb-2">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2"><label class="form-label small">{{ __('Grade') }} <span
                                    class="text-danger">*</span></label><input type="text"
                                class="form-control form-control-sm" name="bands[0][grade]" placeholder="A" required>
                        </div>
                        <div class="col-md-2"><label class="form-label small">{{ __('Label') }}</label><input
                                type="text" class="form-control form-control-sm" name="bands[0][label]"
                                placeholder="Distinction"></div>
                        <div class="col-md-2"><label class="form-label small">{{ __('Min Score') }} <span
                                    class="text-danger">*</span></label><input type="number" step="0.01"
                                class="form-control form-control-sm" name="bands[0][min_score]" placeholder="80"
                                required></div>
                        <div class="col-md-2"><label class="form-label small">{{ __('Max Score') }} <span
                                    class="text-danger">*</span></label><input type="number" step="0.01"
                                class="form-control form-control-sm" name="bands[0][max_score]" placeholder="100"
                                required></div>
                        <div class="col-md-2"><label class="form-label small">{{ __('Grade Point') }}</label><input
                                type="number" step="0.01" class="form-control form-control-sm"
                                name="bands[0][grade_point]" placeholder="4.0"></div>
                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100"
                                onclick="removeBand(this)"><i class="bi bi-trash"></i></button></div>
                        <div class="col-12"><input type="text" class="form-control form-control-sm"
                                name="bands[0][remarks]" placeholder="{{ __('Remarks (optional)') }}"></div>
                    </div>
                </div>
            </div>
        @endif
    </div><button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addBand()"><i
            class="bi bi-plus-circle me-1"></i>{{ __('Add Band') }}</button>
</div>
<div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i
            class="bi bi-check-circle me-1"></i>{{ $buttonText }}</button><a
        href="{{ route('tenant.academics.grading_schemes.index') }}"
        class="btn btn-outline-secondary">{{ __('Cancel') }}</a></div>
@push('scripts')
    <script>
        let bandIndex = {{ isset($gradingScheme) ? $gradingScheme->bands->count() : 1 }};

        function addBand() {
            const container = document.getElementById('bands-container');
            const template =
                `<div class="band-row card mb-2"><div class="card-body"><div class="row g-2 align-items-end"><div class="col-md-2"><label class="form-label small">{{ __('Grade') }} <span class="text-danger">*</span></label><input type="text" class="form-control form-control-sm" name="bands[${bandIndex}][grade]" placeholder="A" required></div><div class="col-md-2"><label class="form-label small">{{ __('Label') }}</label><input type="text" class="form-control form-control-sm" name="bands[${bandIndex}][label]" placeholder="Distinction"></div><div class="col-md-2"><label class="form-label small">{{ __('Min Score') }} <span class="text-danger">*</span></label><input type="number" step="0.01" class="form-control form-control-sm" name="bands[${bandIndex}][min_score]" placeholder="80" required></div><div class="col-md-2"><label class="form-label small">{{ __('Max Score') }} <span class="text-danger">*</span></label><input type="number" step="0.01" class="form-control form-control-sm" name="bands[${bandIndex}][max_score]" placeholder="100" required></div><div class="col-md-2"><label class="form-label small">{{ __('Grade Point') }}</label><input type="number" step="0.01" class="form-control form-control-sm" name="bands[${bandIndex}][grade_point]" placeholder="4.0"></div><div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeBand(this)"><i class="bi bi-trash"></i></button></div><div class="col-12"><input type="text" class="form-control form-control-sm" name="bands[${bandIndex}][remarks]" placeholder="{{ __('Remarks (optional)') }}"></div></div></div></div>`;
            container.insertAdjacentHTML('beforeend', template);
            bandIndex++;
        }

        function removeBand(button) {
            if (document.querySelectorAll('.band-row').length > 1) {
                button.closest('.band-row').remove();
            } else {
                alert('{{ __('At least one grading band is required.') }}');
            }
        }
    </script>
@endpush
