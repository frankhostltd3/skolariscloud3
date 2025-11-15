@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 fw-semibold mb-2">Payment Settings</h1>
                    <p class="text-muted mb-4">
                        Manage credentials for the payment providers used across your workspace. Enable the
                        gateways that apply to your school and keep credentials up to date to maintain a smooth
                        checkout experience.
                    </p>

                    @if ($envWritable)
                        <div class="alert alert-info d-flex align-items-start gap-2">
                            <span class="bi bi-info-circle-fill"></span>
                            <span>
                                Environment file updates are enabled on this host. Select “Sync to .env” for any
                                gateway to mirror the saved credentials into <code>.env</code>.
                            </span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.payments.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="accordion" id="paymentGatewayAccordion">
                            @foreach ($definitions as $key => $definition)
                                @php
                                    /** @var \App\Models\PaymentGatewaySetting|null $stored */
                                    $stored = $settings->get($key);
                                    $config = $stored?->config ?? [];
                                    $fields = $definition['fields'] ?? [];
                                    $isEnabledOld = old("gateways.$key.is_enabled");
                                    $isEnabled =
                                        $isEnabledOld !== null
                                            ? filter_var($isEnabledOld, FILTER_VALIDATE_BOOLEAN)
                                            : $stored?->is_enabled ?? false;
                                @endphp

                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="heading-{{ $key }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $key }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse-{{ $key }}">
                                            <div
                                                class="d-flex flex-column flex-lg-row w-100 align-items-start align-items-lg-center">
                                                <div class="me-lg-3">
                                                    <span class="fw-semibold">{{ $definition['label'] }}</span>
                                                </div>
                                                <div class="small text-muted">{{ $definition['description'] ?? '' }}
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $key }}"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $key }}"
                                        data-bs-parent="#paymentGatewayAccordion">
                                        <div class="accordion-body">
                                            <div class="form-check form-switch mb-4">
                                                <input type="hidden" name="gateways[{{ $key }}][is_enabled]"
                                                    value="0">
                                                <input class="form-check-input" type="checkbox"
                                                    id="is_enabled_{{ $key }}"
                                                    name="gateways[{{ $key }}][is_enabled]" value="1"
                                                    {{ $isEnabled ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold"
                                                    for="is_enabled_{{ $key }}">
                                                    Enable {{ $definition['label'] }}
                                                </label>
                                            </div>

                                            <div class="row g-4">
                                                @foreach ($fields as $fieldKey => $field)
                                                    @php
                                                        $inputName = "gateways[$key][config][$fieldKey]";
                                                        $oldValue = old("gateways.$key.config.$fieldKey");
                                                        $defaultValue = $field['default'] ?? null;
                                                        $currentValue = $oldValue;

                                                        if ($currentValue === null && empty($field['conceal'])) {
                                                            $currentValue = $config[$fieldKey] ?? $defaultValue;
                                                        }

                                                        if ($currentValue === null) {
                                                            $currentValue = '';
                                                        }

                                                        $inputType = $field['type'] ?? 'text';
                                                    @endphp

                                                    <div class="col-md-6">
                                                        <label class="form-label"
                                                            for="{{ $fieldKey }}_{{ $key }}">
                                                            {{ $field['label'] }}
                                                        </label>

                                                        @if ($inputType === 'textarea')
                                                            <textarea class="form-control @error("gateways.$key.config.$fieldKey") is-invalid @enderror"
                                                                id="{{ $fieldKey }}_{{ $key }}" name="{{ $inputName }}" rows="3">{{ $currentValue }}</textarea>
                                                        @elseif ($inputType === 'select')
                                                            <select
                                                                class="form-select @error("gateways.$key.config.$fieldKey") is-invalid @enderror"
                                                                id="{{ $fieldKey }}_{{ $key }}"
                                                                name="{{ $inputName }}">
                                                                @foreach ($field['options'] ?? [] as $optionValue => $label)
                                                                    <option value="{{ $optionValue }}"
                                                                        {{ (string) $currentValue === (string) $optionValue ? 'selected' : '' }}>
                                                                        {{ $label }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            @if ($inputType === 'password')
                                                                <input type="password"
                                                                    class="form-control @error("gateways.$key.config.$fieldKey") is-invalid @enderror"
                                                                    id="{{ $fieldKey }}_{{ $key }}"
                                                                    name="{{ $inputName }}"
                                                                    @if ($currentValue !== '') value="{{ $currentValue }}" @endif
                                                                    placeholder="Leave blank to keep existing value">
                                                            @else
                                                                <input type="{{ $inputType === 'url' ? 'url' : 'text' }}"
                                                                    class="form-control @error("gateways.$key.config.$fieldKey") is-invalid @enderror"
                                                                    id="{{ $fieldKey }}_{{ $key }}"
                                                                    name="{{ $inputName }}"
                                                                    value="{{ $currentValue }}">
                                                            @endif
                                                        @endif

                                                        @error("gateways.$key.config.$fieldKey")
                                                            <div class="invalid-feedback d-block">{{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            </div>

                                            @if ($envWritable && !empty(array_filter(array_column($fields, 'env'))))
                                                <div class="form-check mt-4">
                                                    <input type="hidden" name="sync_env[{{ $key }}]"
                                                        value="0">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="sync_env_{{ $key }}"
                                                        name="sync_env[{{ $key }}]" value="1"
                                                        {{ old("sync_env.$key") ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sync_env_{{ $key }}">
                                                        Sync {{ $definition['label'] }} credentials to
                                                        <code>.env</code>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
