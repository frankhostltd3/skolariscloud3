<div class="row g-3">
    <div class="col-md-6"><label for="name" class="form-label">{{ __('Country Name') }} <span
                class="text-danger">*</span></label><input type="text"
            class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $country->name ?? '') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="iso_code_2" class="form-label">{{ __('ISO 2') }} <span
                class="text-danger">*</span></label><input type="text"
            class="form-control @error('iso_code_2') is-invalid @enderror" id="iso_code_2" name="iso_code_2"
            value="{{ old('iso_code_2', $country->iso_code_2 ?? '') }}" maxlength="2" placeholder="UG" required>
        @error('iso_code_2')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="iso_code_3" class="form-label">{{ __('ISO 3') }} <span
                class="text-danger">*</span></label><input type="text"
            class="form-control @error('iso_code_3') is-invalid @enderror" id="iso_code_3" name="iso_code_3"
            value="{{ old('iso_code_3', $country->iso_code_3 ?? '') }}" maxlength="3" placeholder="UGA" required>
        @error('iso_code_3')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="phone_code" class="form-label">{{ __('Phone Code') }}</label><input type="text"
            class="form-control @error('phone_code') is-invalid @enderror" id="phone_code" name="phone_code"
            value="{{ old('phone_code', $country->phone_code ?? '') }}" placeholder="+256">
        @error('phone_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="currency_code" class="form-label">{{ __('Currency Code') }}</label><input
            type="text" class="form-control @error('currency_code') is-invalid @enderror" id="currency_code"
            name="currency_code" value="{{ old('currency_code', $country->currency_code ?? '') }}" maxlength="3"
            placeholder="UGX">
        @error('currency_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4"><label for="currency_symbol" class="form-label">{{ __('Currency Symbol') }}</label><input
            type="text" class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol"
            name="currency_symbol" value="{{ old('currency_symbol', $country->currency_symbol ?? '') }}"
            placeholder="UGX">
        @error('currency_symbol')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6"><label for="timezone" class="form-label">{{ __('Timezone') }}</label><input type="text"
            class="form-control @error('timezone') is-invalid @enderror" id="timezone" name="timezone"
            value="{{ old('timezone', $country->timezone ?? '') }}" placeholder="Africa/Kampala">
        @error('timezone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="flag_emoji" class="form-label">{{ __('Flag Emoji') }}</label><input
            type="text" class="form-control @error('flag_emoji') is-invalid @enderror" id="flag_emoji"
            name="flag_emoji" value="{{ old('flag_emoji', $country->flag_emoji ?? '') }}" placeholder="🇺🇬">
        @error('flag_emoji')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3"><label for="is_active" class="form-label">{{ __('Status') }}</label><select
            class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
            <option value="1" {{ old('is_active', $country->is_active ?? true) ? 'selected' : '' }}>
                {{ __('Active') }}</option>
            <option value="0" {{ old('is_active', $country->is_active ?? true) ? '' : 'selected' }}>
                {{ __('Inactive') }}</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>{{ $buttonText }}</button>
    <a href="{{ route('tenant.academics.countries.index') }}"
        class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
</div>
