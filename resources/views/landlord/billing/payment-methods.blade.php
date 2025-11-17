@extends('landlord.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h4 fw-semibold mb-2">💳 {{ __('Payment Methods Configuration') }}</h1>
            <p class="text-secondary">{{ __('Configure how schools pay their subscription invoices') }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Configure payment gateways to allow schools to pay their landlord invoices online. Activate only the gateways you have accounts with.') }}
                    </div>

                    <!-- Gateway Cards -->
                    <div class="row g-4">
                        @foreach($availableGateways as $gatewayKey => $gatewayInfo)
                            @php
                                $existingConfig = $gateways->firstWhere('gateway', $gatewayKey);
                                $isConfigured = $existingConfig !== null;
                                $isActive = $existingConfig?->is_active ?? false;
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="card border {{ $isActive ? 'border-success' : ($isConfigured ? 'border-warning' : 'border-secondary') }} h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <span class="fs-3 me-2">{{ $gatewayInfo['logo'] }}</span>
                                                    {{ $gatewayInfo['name'] }}
                                                </h5>
                                                <p class="card-text small text-muted">{{ $gatewayInfo['description'] }}</p>
                                            </div>
                                            @if($isActive)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @elseif($isConfigured)
                                                <span class="badge bg-warning text-dark">{{ __('Inactive') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Not Configured') }}</span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">{{ __('Supported Currencies') }}:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($gatewayInfo['currencies'] as $currency)
                                                    <span class="badge bg-light text-dark">{{ $currency }}</span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button class="btn btn-{{ $isConfigured ? 'outline-primary' : 'primary' }} btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#configModal"
                                                    onclick="openConfigModal('{{ $gatewayKey }}')">
                                                <i class="bi bi-gear me-1"></i>
                                                {{ $isConfigured ? __('Edit Configuration') : __('Configure') }}
                                            </button>
                                            @if($isConfigured)
                                                <button class="btn btn-sm btn-{{ $isActive ? 'warning' : 'success' }}" 
                                                        onclick="toggleGateway('{{ $gatewayKey }}', {{ $isActive ? 'false' : 'true' }})">
                                                    <i class="bi bi-toggle-{{ $isActive ? 'on' : 'off' }} me-1"></i>
                                                    {{ $isActive ? __('Deactivate') : __('Activate') }}
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteGateway('{{ $gatewayKey }}')">
                                                    <i class="bi bi-trash me-1"></i>
                                                    {{ __('Delete') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configuration Modal -->
<div class="modal fade" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="configModalLabel">
                    <span id="modalGatewayIcon"></span>
                    <span id="modalGatewayName"></span> {{ __('Configuration') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="gatewayConfigForm">
                    <input type="hidden" id="configGateway" name="gateway">
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-lock me-2"></i>
                        {{ __('All credentials are encrypted before storage. Keep them secure.') }}
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1">
                                <label class="form-check-label" for="isActive">
                                    {{ __('Activate Gateway') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isTestMode" name="is_test_mode" value="1" checked>
                                <label class="form-check-label" for="isTestMode">
                                    {{ __('Test Mode (Sandbox)') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="credentialFields"></div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Supported Currencies') }}</label>
                        <div id="currencyCheckboxes" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="displayOrder" class="form-label">{{ __('Display Order') }}</label>
                        <input type="number" class="form-control" id="displayOrder" name="display_order" min="0" value="0">
                        <small class="text-muted">{{ __('Lower numbers appear first') }}</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveConfiguration()">
                    <i class="bi bi-save me-1"></i>
                    {{ __('Save Configuration') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const availableGateways = @json($availableGateways);
const configuredGateways = @json($gateways);
let currentGateway = null;

console.log('Available Gateways:', availableGateways);
console.log('Configured Gateways:', configuredGateways);

function openConfigModal(gatewayKey) {
    currentGateway = gatewayKey;
    const gatewayInfo = availableGateways[gatewayKey];
    const existingConfig = configuredGateways.find(g => g.gateway === gatewayKey);
    
    console.log('Opening modal for:', gatewayKey);
    console.log('Gateway Info:', gatewayInfo);
    console.log('Existing Config:', existingConfig);
    
    // Set modal title
    document.getElementById('modalGatewayIcon').textContent = gatewayInfo.logo;
    document.getElementById('modalGatewayName').textContent = gatewayInfo.name;
    document.getElementById('configGateway').value = gatewayKey;
    
    // Set checkboxes
    document.getElementById('isActive').checked = existingConfig?.is_active ?? false;
    document.getElementById('isTestMode').checked = existingConfig?.is_test_mode ?? true;
    document.getElementById('displayOrder').value = existingConfig?.display_order ?? 0;
    
    // Build credential fields
    const credentialFieldsContainer = document.getElementById('credentialFields');
    credentialFieldsContainer.innerHTML = '';
    
    Object.entries(gatewayInfo.fields).forEach(([key, field]) => {
        const existingValue = existingConfig?.credentials?.[key] ?? '';
        
        let inputHtml = '';
        if (field.type === 'select') {
            inputHtml = `<select class="form-select" id="cred_${key}" name="credentials[${key}]" ${field.required ? 'required' : ''}>
                <option value="">{{ __('Select...') }}</option>
                ${field.options.map(opt => `<option value="${opt}" ${existingValue === opt ? 'selected' : ''}>${opt}</option>`).join('')}
            </select>`;
        } else {
            const safeValue = field.type === 'password' ? '' : existingValue;
            inputHtml = `<input type="${field.type}" class="form-control" id="cred_${key}" name="credentials[${key}]" 
                placeholder="${field.label}" value="${safeValue ?? ''}" ${field.required ? 'required' : ''}>`;
        }
        
        credentialFieldsContainer.innerHTML += `
            <div class="mb-3">
                <label for="cred_${key}" class="form-label">
                    ${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}
                </label>
                ${inputHtml}
            </div>
        `;
    });
    
    // Build currency checkboxes
    const currencyContainer = document.getElementById('currencyCheckboxes');
    currencyContainer.innerHTML = '';
    
    gatewayInfo.currencies.forEach(currency => {
        const isChecked = existingConfig?.supported_currencies?.includes(currency) ?? true;
        currencyContainer.innerHTML += `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="curr_${currency}" name="currencies[]" value="${currency}" ${isChecked ? 'checked' : ''}>
                <label class="form-check-label" for="curr_${currency}">${currency}</label>
            </div>
        `;
    });
}

async function saveConfiguration() {
    const form = document.getElementById('gatewayConfigForm');
    const formData = new FormData(form);
    
    // Build credentials object
    const credentials = {};
    const gatewayInfo = availableGateways[currentGateway];
    Object.keys(gatewayInfo.fields).forEach(key => {
        const value = document.getElementById(`cred_${key}`)?.value;
        if (value) {
            credentials[key] = value;
        }
    });
    
    // Build currencies array
    const currencies = [];
    document.querySelectorAll('input[name="currencies[]"]:checked').forEach(cb => {
        currencies.push(cb.value);
    });
    
    const payload = {
        gateway: currentGateway,
        is_active: document.getElementById('isActive').checked,
        is_test_mode: document.getElementById('isTestMode').checked,
        credentials: credentials,
        supported_currencies: currencies,
        display_order: parseInt(document.getElementById('displayOrder').value) || 0,
        _token: '{{ csrf_token() }}'
    };
    
    try {
        const response = await fetch('{{ route("landlord.billing.payment-methods.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('{{ __("Configuration saved successfully!") }}');
            location.reload();
        } else {
            let msg = data.message || '{{ __("Unknown error") }}';
            if (data.errors) {
                const list = Object.entries(data.errors).map(([k, v]) => `- ${k}: ${v.join(', ')}`).join('\n');
                msg += `\n${list}`;
            }
            alert('{{ __("Error:") }} ' + msg);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('{{ __("Failed to save configuration") }}');
    }
}

async function toggleGateway(gatewayKey, activate) {
    if (!confirm(`{{ __("Are you sure you want to") }} ${activate ? '{{ __("activate") }}' : '{{ __("deactivate") }}'} {{ __("this gateway?") }}`)) {
        return;
    }
    
    try {
        const response = await fetch(`/landlord/billing/payment-methods/${gatewayKey}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('{{ __("Failed to toggle gateway") }}');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('{{ __("Failed to toggle gateway") }}');
    }
}

async function deleteGateway(gatewayKey) {
    if (!confirm('{{ __("Are you sure you want to delete this gateway configuration? This cannot be undone.") }}')) {
        return;
    }
    
    try {
        const response = await fetch(`/landlord/billing/payment-methods/${gatewayKey}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('{{ __("Failed to delete gateway") }}');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('{{ __("Failed to delete gateway") }}');
    }
}
</script>
@endpush
@endsection
