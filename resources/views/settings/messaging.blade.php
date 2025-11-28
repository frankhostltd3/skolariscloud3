@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 fw-semibold mb-2">Messaging Channels</h1>
                    <p class="text-muted mb-4">
                        Configure SMS and WhatsApp gateways used for staff, student, guardian, and landlord
                        communications. Enable the providers available to your school and keep credentials in sync
                        with your infrastructure.
                    </p>

                    @if ($envWritable)
                        <div class="alert alert-info d-flex align-items-start gap-2">
                            <span class="bi bi-info-circle-fill"></span>
                            <span>
                                Environment file updates are enabled on this host. Select “Sync to .env” for any
                                provider to mirror the saved credentials into <code>.env</code>.
                            </span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tenant.settings.admin.messaging.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="accordion" id="messagingChannelsAccordion">
                            @foreach ($definitions as $channelKey => $channelDefinition)
                                @php
                                    $channelLabel = $channelDefinition['label'];
                                    $channelDescription = $channelDefinition['description'] ?? '';
                                    $providers = $channelDefinition['providers'] ?? [];
                                @endphp

                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="heading-channel-{{ $channelKey }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-channel-{{ $channelKey }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse-channel-{{ $channelKey }}">
                                            <div class="d-flex flex-column w-100">
                                                <span class="fw-semibold">{{ $channelLabel }}</span>
                                                <span class="small text-muted">{{ $channelDescription }}</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-channel-{{ $channelKey }}"
                                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                        aria-labelledby="heading-channel-{{ $channelKey }}"
                                        data-bs-parent="#messagingChannelsAccordion">
                                        <div class="accordion-body">
                                            <div class="accordion" id="providers-accordion-{{ $channelKey }}">
                                                @foreach ($providers as $providerKey => $providerDefinition)
                                                    @php
                                                        $stored = $settings->get($channelKey . '.' . $providerKey);
                                                        $config = $stored?->config ?? [];
                                                        $fields = $providerDefinition['fields'] ?? [];
                                                        $isEnabledOld = old(
                                                            "channels.$channelKey.providers.$providerKey.is_enabled",
                                                        );
                                                        $isEnabled =
                                                            $isEnabledOld !== null
                                                                ? filter_var($isEnabledOld, FILTER_VALIDATE_BOOLEAN)
                                                                : $stored?->is_enabled ?? false;
                                                    @endphp

                                                    <div class="accordion-item mb-2">
                                                        <h3 class="accordion-header"
                                                            id="heading-provider-{{ $channelKey }}-{{ $providerKey }}">
                                                            <button
                                                                class="accordion-button {{ $loop->parent->first && $loop->first ? '' : 'collapsed' }}"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse-provider-{{ $channelKey }}-{{ $providerKey }}"
                                                                aria-expanded="{{ $loop->parent->first && $loop->first ? 'true' : 'false' }}"
                                                                aria-controls="collapse-provider-{{ $channelKey }}-{{ $providerKey }}">
                                                                <div class="d-flex flex-column w-100">
                                                                    <span
                                                                        class="fw-semibold">{{ $providerDefinition['label'] }}</span>
                                                                    <span
                                                                        class="small text-muted">{{ $providerDefinition['description'] ?? '' }}</span>
                                                                </div>
                                                            </button>
                                                        </h3>
                                                        <div id="collapse-provider-{{ $channelKey }}-{{ $providerKey }}"
                                                            class="accordion-collapse collapse {{ $loop->parent->first && $loop->first ? 'show' : '' }}"
                                                            aria-labelledby="heading-provider-{{ $channelKey }}-{{ $providerKey }}"
                                                            data-bs-parent="#providers-accordion-{{ $channelKey }}">
                                                            <div class="accordion-body">
                                                                <div class="form-check form-switch mb-4">
                                                                    <input type="hidden"
                                                                        name="channels[{{ $channelKey }}][providers][{{ $providerKey }}][is_enabled]"
                                                                        value="0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        id="is_enabled_{{ $channelKey }}_{{ $providerKey }}"
                                                                        name="channels[{{ $channelKey }}][providers][{{ $providerKey }}][is_enabled]"
                                                                        value="1" {{ $isEnabled ? 'checked' : '' }}>
                                                                    <label class="form-check-label fw-semibold"
                                                                        for="is_enabled_{{ $channelKey }}_{{ $providerKey }}">
                                                                        Enable {{ $providerDefinition['label'] }}
                                                                    </label>
                                                                </div>

                                                                <div class="row g-4">
                                                                    @foreach ($fields as $fieldKey => $field)
                                                                        @php
                                                                            $inputName = "channels[$channelKey][providers][$providerKey][config][$fieldKey]";
                                                                            $oldValue = old(
                                                                                "channels.$channelKey.providers.$providerKey.config.$fieldKey",
                                                                            );
                                                                            $defaultValue = $field['default'] ?? null;
                                                                            $currentValue = $oldValue;

                                                                            if (
                                                                                $currentValue === null &&
                                                                                empty($field['conceal'])
                                                                            ) {
                                                                                $currentValue =
                                                                                    $config[$fieldKey] ?? $defaultValue;
                                                                            }

                                                                            if ($currentValue === null) {
                                                                                $currentValue = '';
                                                                            }

                                                                            $inputType = $field['type'] ?? 'text';
                                                                        @endphp

                                                                        <div class="col-md-6">
                                                                            <label class="form-label"
                                                                                for="{{ $fieldKey }}_{{ $channelKey }}_{{ $providerKey }}">
                                                                                {{ $field['label'] }}
                                                                            </label>

                                                                            @if ($inputType === 'textarea')
                                                                                <textarea
                                                                                    class="form-control @error("channels.$channelKey.providers.$providerKey.config.$fieldKey") is-invalid @enderror"
                                                                                    id="{{ $fieldKey }}_{{ $channelKey }}_{{ $providerKey }}" name="{{ $inputName }}" rows="3">{{ $currentValue }}</textarea>
                                                                            @elseif ($inputType === 'select')
                                                                                <select
                                                                                    class="form-select @error("channels.$channelKey.providers.$providerKey.config.$fieldKey") is-invalid @enderror"
                                                                                    id="{{ $fieldKey }}_{{ $channelKey }}_{{ $providerKey }}"
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
                                                                                        class="form-control @error("channels.$channelKey.providers.$providerKey.config.$fieldKey") is-invalid @enderror"
                                                                                        id="{{ $fieldKey }}_{{ $channelKey }}_{{ $providerKey }}"
                                                                                        name="{{ $inputName }}"
                                                                                        @if ($currentValue !== '') value="{{ $currentValue }}" @endif
                                                                                        placeholder="Leave blank to keep existing value">
                                                                                @else
                                                                                    <input
                                                                                        type="{{ $inputType === 'url' ? 'url' : 'text' }}"
                                                                                        class="form-control @error("channels.$channelKey.providers.$providerKey.config.$fieldKey") is-invalid @enderror"
                                                                                        id="{{ $fieldKey }}_{{ $channelKey }}_{{ $providerKey }}"
                                                                                        name="{{ $inputName }}"
                                                                                        value="{{ $currentValue }}">
                                                                                @endif
                                                                            @endif

                                                                            @error("channels.$channelKey.providers.$providerKey.config.$fieldKey")
                                                                                <div class="invalid-feedback d-block">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    @endforeach
                                                                </div>

                                                                @if ($envWritable && !empty(array_filter(array_column($fields, 'env'))))
                                                                    <div class="form-check mt-4">
                                                                        <input type="hidden"
                                                                            name="sync_env[{{ $channelKey }}][providers][{{ $providerKey }}]"
                                                                            value="0">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="sync_env_{{ $channelKey }}_{{ $providerKey }}"
                                                                            name="sync_env[{{ $channelKey }}][providers][{{ $providerKey }}]"
                                                                            value="1"
                                                                            {{ old("sync_env.$channelKey.providers.$providerKey") ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="sync_env_{{ $channelKey }}_{{ $providerKey }}">
                                                                            Sync {{ $providerDefinition['label'] }}
                                                                            credentials to <code>.env</code>
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
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
