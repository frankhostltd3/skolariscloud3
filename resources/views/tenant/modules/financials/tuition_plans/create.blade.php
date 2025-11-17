@extends('tenant.layouts.app')

@section('title', __('Create Tuition Plan'))

@section('sidebar')
<div class="card shadow-sm mb-4">
  <div class="card-header fw-semibold">{{ __('Financial Management') }}</div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.overview') }}">
      <span class="bi bi-speedometer2 me-2"></span>{{ __('Financial Overview') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.fees') }}">
      <span class="bi bi-cash-stack me-2"></span>{{ __('Fee Management') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.expenses') }}">
      <span class="bi bi-receipt me-2"></span>{{ __('Expenses') }}
    </a>
    <a class="list-group-item list-group-item-action active" href="{{ route('tenant.modules.financials.tuition_plans') }}">
      <span class="bi bi-file-earmark-text me-2"></span>{{ __('Tuition Plans') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.invoices') }}">
      <span class="bi bi-file-earmark-pdf me-2"></span>{{ __('Invoices') }}
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">{{ __('Plan Creation Tips') }}</div>
  <div class="card-body">
    <div class="small text-muted">
      <div class="mb-3">
        <strong>{{ __('Plan Structure') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('Add multiple fee items to bundle costs') }}</li>
          <li>{{ __('Set tax rates and apply discounts') }}</li>
          <li>{{ __('Create installment schedules for payments') }}</li>
          <li>{{ __('Set due dates for each installment') }}</li>
        </ul>
      </div>

      <div class="alert alert-info small p-2">
        <strong>{{ __('Tip:') }}</strong> {{ __('Use the "Add Item" and "Add Installment" buttons to build comprehensive plans.') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h5 fw-semibold mb-0">{{ __('Create Tuition Plan') }}</h1>
        <div class="small text-secondary">{{ __('Define structured fee plans and installment schedules.') }}</div>
    </div>
    <div class="card-body">
        <form action="{{ route('tenant.modules.financials.tuition_plans.store') }}" method="POST" id="tuitionPlanForm">
            @csrf

            <!-- Basic Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Plan Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="currency_id" class="form-label">{{ __('Currency') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('currency_id') is-invalid @enderror"
                                id="currency_id" name="currency_id" required>
                            <option value="">{{ __('Select Currency') }}</option>
                            @foreach(\App\Models\Currency::active()->orderBy('is_default', 'desc')->orderBy('code')->get() as $currency)
                                <option value="{{ $currency->id }}"
                                        data-symbol="{{ $currency->symbol }}"
                                        data-symbol-position="{{ $currency->symbol_position }}"
                                        data-decimal-places="{{ $currency->decimal_places }}"
                                        data-thousands-separator="{{ $currency->thousands_separator }}"
                                        data-decimal-separator="{{ $currency->decimal_separator }}"
                                        data-grade-levels="{{ $currency->grade_levels ? json_encode($currency->grade_levels) : 'null' }}"
                                        {{ old('currency_id', \App\Models\Currency::getDefault()?->id) == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->code }} - {{ $currency->name }}
                                    @if($currency->is_default) ({{ __('Default') }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('currency_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="3"
                          placeholder="{{ __('Describe this tuition plan...') }}">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">{{ __('Academic Year') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                               id="academic_year" name="academic_year"
                               value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}" required>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="grade_level" class="form-label">{{ __('Grade Level') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('grade_level') is-invalid @enderror"
                                id="grade_level" name="grade_level" required>
                            <option value="">{{ __('Select Grade Level') }}</option>
                            <!-- Grade levels will be populated dynamically based on selected currency -->
                        </select>
                        @error('grade_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Fee Items Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('Fee Items') }}</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Item') }}
                    </button>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <!-- Dynamic items will be added here -->
                        <div class="item-row border rounded p-3 mb-3" data-item-index="0">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Fee Item') }} <span class="text-danger">*</span></label>
                                    <select class="form-select fee-select @error('items.0.fee_id') is-invalid @enderror"
                                            name="items[0][fee_id]" required>
                                        <option value="">{{ __('Select Fee') }}</option>
                                        @foreach($fees as $fee)
                                            <option value="{{ $fee->id }}" data-amount="{{ $fee->amount }}">
                                                {{ $fee->name }} ({{ format_money($fee->amount) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('items.0.fee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('items.0.description') is-invalid @enderror"
                                           name="items[0][description]" placeholder="Fee description" required>
                                    @error('items.0.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control quantity-input @error('items.0.quantity') is-invalid @enderror"
                                           name="items[0][quantity]" value="1" min="1" required>
                                    @error('items.0.quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}</span>
                                        <input type="number" class="form-control unit-price-input @error('items.0.unit_price') is-invalid @enderror"
                                               name="items[0][unit_price]" step="0.01" min="0" required>
                                    </div>
                                    @error('items.0.unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Actions') }}</label>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Tax Rate (%)') }}</label>
                                    <input type="number" class="form-control tax-rate-input @error('items.0.tax_rate') is-invalid @enderror"
                                           name="items[0][tax_rate]" step="0.01" min="0" max="100" placeholder="0.00">
                                    @error('items.0.tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Discount Amount') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}</span>
                                        <input type="number" class="form-control discount-input @error('items.0.discount_amount') is-invalid @enderror"
                                               name="items[0][discount_amount]" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    @error('items.0.discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Net Amount') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}</span>
                                        <input type="text" class="form-control net-amount-input" readonly value="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Total -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Items Subtotal:') }}</span>
                                    <span id="itemsSubtotalDisplay">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Tax:') }}</span>
                                    <span id="itemsTaxDisplay">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Discount:') }}</span>
                                    <span id="itemsDiscountDisplay">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>{{ __('Items Total:') }}</span>
                                    <span id="itemsTotalDisplay">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Installments Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('Payment Installments') }}</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addInstallmentBtn">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Installment') }}
                    </button>
                </div>
                <div class="card-body">
                    <div id="installmentsContainer">
                        <!-- Dynamic installments will be added here -->
                        <div class="installment-row border rounded p-3 mb-3" data-installment-index="0">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Installment Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('installments.0.name') is-invalid @enderror"
                                           name="installments[0][name]" placeholder="e.g., First Term Payment" required>
                                    @error('installments.0.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Description') }}</label>
                                    <input type="text" class="form-control @error('installments.0.description') is-invalid @enderror"
                                           name="installments[0][description]" placeholder="Optional description">
                                    @error('installments.0.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}</span>
                                        <input type="number" class="form-control installment-amount-input @error('installments.0.amount') is-invalid @enderror"
                                               name="installments[0][amount]" step="0.01" min="0" required>
                                    </div>
                                    @error('installments.0.amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Due Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('installments.0.due_date') is-invalid @enderror"
                                           name="installments[0][due_date]" required>
                                    @error('installments.0.due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Actions') }}</label>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-installment-btn" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Installments Total -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>{{ __('Total Installments:') }}</span>
                                    <span id="installmentsTotalDisplay">{{ \App\Models\Currency::getDefault()?->symbol ?? '$' }}0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Create Tuition Plan') }}
                </button>
                <a href="{{ route('tenant.modules.financials.tuition_plans') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    let installmentIndex = 1;
    let currentCurrency = {
        symbol: '$',
        symbolPosition: 'before',
        decimalPlaces: 2,
        thousandsSeparator: ',',
        decimalSeparator: '.'
    };

    // Initialize currency on page load
    updateCurrencyFromSelection();

    // Currency change handler
    document.getElementById('currency_id').addEventListener('change', function() {
        updateCurrencyFromSelection();
        updateGradeLevelsFromCurrency();
        updateAllCurrencyDisplays();
        calculateItemsTotal();
        calculateInstallmentsTotal();
    });

    // Add item button functionality
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItemRow();
    });

    // Add installment button functionality
    document.getElementById('addInstallmentBtn').addEventListener('click', function() {
        addInstallmentRow();
    });

    // Function to update currency from selection
    function updateCurrencyFromSelection() {
        const currencySelect = document.getElementById('currency_id');
        const selectedOption = currencySelect.options[currencySelect.selectedIndex];

        if (selectedOption && selectedOption.value) {
            currentCurrency = {
                symbol: selectedOption.getAttribute('data-symbol') || selectedOption.value,
                symbolPosition: selectedOption.getAttribute('data-symbol-position') || 'before',
                decimalPlaces: parseInt(selectedOption.getAttribute('data-decimal-places')) || 2,
                thousandsSeparator: selectedOption.getAttribute('data-thousands-separator') || ',',
                decimalSeparator: selectedOption.getAttribute('data-decimal-separator') || '.'
            };
        }
    }

    // Function to update grade levels from selected currency
    function updateGradeLevelsFromCurrency() {
        const currencySelect = document.getElementById('currency_id');
        const gradeLevelSelect = document.getElementById('grade_level');
        const selectedOption = currencySelect.options[currencySelect.selectedIndex];

        // Clear existing options except the first one
        while (gradeLevelSelect.options.length > 1) {
            gradeLevelSelect.remove(1);
        }

        if (selectedOption && selectedOption.value) {
            const gradeLevelsData = selectedOption.getAttribute('data-grade-levels');
            if (gradeLevelsData) {
                const gradeLevels = JSON.parse(gradeLevelsData);
                gradeLevels.forEach(level => {
                    const option = document.createElement('option');
                    option.value = level;
                    option.textContent = level;
                    gradeLevelSelect.appendChild(option);
                });
            } else {
                // Default grade levels if no custom ones are set
                const defaultLevels = ['Pre-School', 'Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
                defaultLevels.forEach(level => {
                    const option = document.createElement('option');
                    option.value = level;
                    option.textContent = level;
                    gradeLevelSelect.appendChild(option);
                });
            }

            // Add "All Grades" option
            const allGradesOption = document.createElement('option');
            allGradesOption.value = 'All Grades';
            allGradesOption.textContent = 'All Grades';
            gradeLevelSelect.appendChild(allGradesOption);
        }
    }

    // Function to update all currency displays
    function updateAllCurrencyDisplays() {
        // Update all input group texts
        document.querySelectorAll('.input-group-text').forEach(element => {
            if (element.textContent.includes('$') || element.textContent.includes(currentCurrency.symbol)) {
                element.textContent = currentCurrency.symbol;
            }
        });

        // Update all display elements with currency
        ['itemsSubtotalDisplay', 'itemsTaxDisplay', 'itemsDiscountDisplay', 'itemsTotalDisplay', 'installmentsTotalDisplay'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                const value = element.textContent.replace(/^[^\d.-]+/, '').replace(/[^\d.-]+$/, '');
                element.textContent = formatCurrency(parseFloat(value) || 0);
            }
        });
    }

    // Function to format currency
    function formatCurrency(amount) {
        const formatted = amount.toFixed(currentCurrency.decimalPlaces);
        const parts = formatted.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, currentCurrency.thousandsSeparator);

        const numberString = parts.join(currentCurrency.decimalSeparator);

        if (currentCurrency.symbolPosition === 'before') {
            return currentCurrency.symbol + numberString;
        } else {
            return numberString + ' ' + currentCurrency.symbol;
        }
    }

    // Function to add a new item row
    function addItemRow() {
        const container = document.getElementById('itemsContainer');
        const itemRow = createItemRow(itemIndex);
        container.appendChild(itemRow);
        itemIndex++;

        // Enable remove buttons if more than one item
        updateRemoveButtons();
        attachItemEventListeners();
    }

    // Function to add a new installment row
    function addInstallmentRow() {
        const container = document.getElementById('installmentsContainer');
        const installmentRow = createInstallmentRow(installmentIndex);
        container.appendChild(installmentRow);
        installmentIndex++;

        // Enable remove buttons if more than one installment
        updateRemoveButtons();
        attachInstallmentEventListeners();
    }

    // Function to create item row HTML
    function createItemRow(index) {
        const div = document.createElement('div');
        div.className = 'item-row border rounded p-3 mb-3';
        div.setAttribute('data-item-index', index);

        div.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">${__('Fee Item')} <span class="text-danger">*</span></label>
                    <select class="form-select fee-select" name="items[${index}][fee_id]" required>
                        <option value="">${__('Select Fee')}</option>
                        @foreach($fees as $fee)
                            <option value="{{ $fee->id }}" data-amount="{{ $fee->amount }}">
                                {{ $fee->name }} (${{ number_format($fee->amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">${__('Description')} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${index}][description]" placeholder="Fee description" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">${__('Quantity')} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control quantity-input" name="items[${index}][quantity]" value="1" min="1" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">${__('Unit Price')} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">${currentCurrency.symbol}</span>
                        <input type="number" class="form-control unit-price-input" name="items[${index}][unit_price]" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">${__('Actions')}</label>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3">
                    <label class="form-label">${__('Tax Rate (%)')}</label>
                    <input type="number" class="form-control tax-rate-input" name="items[${index}][tax_rate]" step="0.01" min="0" max="100" placeholder="0.00">
                </div>

                <div class="col-md-3">
                    <label class="form-label">${__('Discount Amount')}</label>
                    <div class="input-group">
                        <span class="input-group-text">${currentCurrency.symbol}</span>
                        <input type="number" class="form-control discount-input" name="items[${index}][discount_amount]" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">${__('Net Amount')}</label>
                    <div class="input-group">
                        <span class="input-group-text">${currentCurrency.symbol}</span>
                        <input type="text" class="form-control net-amount-input" readonly value="0.00">
                    </div>
                </div>
            </div>
        `;

        return div;
    }

    // Function to create installment row HTML
    function createInstallmentRow(index) {
        const div = document.createElement('div');
        div.className = 'installment-row border rounded p-3 mb-3';
        div.setAttribute('data-installment-index', index);

        div.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">${__('Installment Name')} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="installments[${index}][name]" placeholder="e.g., Second Term Payment" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">${__('Description')}</label>
                    <input type="text" class="form-control" name="installments[${index}][description]" placeholder="Optional description">
                </div>

                <div class="col-md-2">
                    <label class="form-label">${__('Amount')} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">${currentCurrency.symbol}</span>
                        <input type="number" class="form-control installment-amount-input" name="installments[${index}][amount]" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">${__('Due Date')} <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="installments[${index}][due_date]" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">${__('Actions')}</label>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-installment-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;

        return div;
    }

    // Function to update remove buttons state
    function updateRemoveButtons() {
        const itemRemoveButtons = document.querySelectorAll('.remove-item-btn');
        const installmentRemoveButtons = document.querySelectorAll('.remove-installment-btn');
        const itemRows = document.querySelectorAll('.item-row');
        const installmentRows = document.querySelectorAll('.installment-row');

        itemRemoveButtons.forEach(button => {
            button.disabled = itemRows.length <= 1;
        });

        installmentRemoveButtons.forEach(button => {
            button.disabled = installmentRows.length <= 1;
        });
    }

    // Function to attach item event listeners
    function attachItemEventListeners() {
        // Fee selection change
        document.querySelectorAll('.fee-select').forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const amount = selectedOption.getAttribute('data-amount');
                const unitPriceInput = this.closest('.item-row').querySelector('.unit-price-input');

                if (amount && unitPriceInput) {
                    unitPriceInput.value = amount;
                    calculateItemNetAmount(this.closest('.item-row'));
                    calculateItemsTotal();
                }
            });
        });

        // Quantity, unit price, tax rate, discount change
        document.querySelectorAll('.quantity-input, .unit-price-input, .tax-rate-input, .discount-input').forEach(input => {
            input.addEventListener('input', function() {
                calculateItemNetAmount(this.closest('.item-row'));
                calculateItemsTotal();
            });
        });

        // Remove item button
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.disabled) {
                    this.closest('.item-row').remove();
                    updateRemoveButtons();
                    calculateItemsTotal();
                }
            });
        });
    }

    // Function to attach installment event listeners
    function attachInstallmentEventListeners() {
        // Amount change
        document.querySelectorAll('.installment-amount-input').forEach(input => {
            input.addEventListener('input', function() {
                calculateInstallmentsTotal();
            });
        });

        // Remove installment button
        document.querySelectorAll('.remove-installment-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.disabled) {
                    this.closest('.installment-row').remove();
                    updateRemoveButtons();
                    calculateInstallmentsTotal();
                }
            });
        });
    }

    // Function to calculate item net amount
    function calculateItemNetAmount(itemRow) {
        const quantity = parseFloat(itemRow.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(itemRow.querySelector('.unit-price-input').value) || 0;
        const taxRate = parseFloat(itemRow.querySelector('.tax-rate-input').value) || 0;
        const discount = parseFloat(itemRow.querySelector('.discount-input').value) || 0;

        const subtotal = quantity * unitPrice;
        const taxAmount = subtotal * (taxRate / 100);
        const netAmount = subtotal + taxAmount - discount;

        itemRow.querySelector('.net-amount-input').value = netAmount.toFixed(currentCurrency.decimalPlaces);
    }

    // Function to calculate items total
    function calculateItemsTotal() {
        let subtotal = 0;
        let totalTax = 0;
        let totalDiscount = 0;
        let totalNet = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

            const itemSubtotal = quantity * unitPrice;
            const itemTax = itemSubtotal * (taxRate / 100);
            const itemNet = itemSubtotal + itemTax - discount;

            subtotal += itemSubtotal;
            totalTax += itemTax;
            totalDiscount += discount;
            totalNet += itemNet;
        });

        document.getElementById('itemsSubtotalDisplay').textContent = formatCurrency(subtotal);
        document.getElementById('itemsTaxDisplay').textContent = formatCurrency(totalTax);
        document.getElementById('itemsDiscountDisplay').textContent = formatCurrency(totalDiscount);
        document.getElementById('itemsTotalDisplay').textContent = formatCurrency(totalNet);
    }

    // Function to calculate installments total
    function calculateInstallmentsTotal() {
        let total = 0;

        document.querySelectorAll('.installment-amount-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        document.getElementById('installmentsTotalDisplay').textContent = formatCurrency(total);
    }

    // Initialize event listeners for existing elements
    attachItemEventListeners();
    attachInstallmentEventListeners();
    updateRemoveButtons();
    updateAllCurrencyDisplays();
    updateGradeLevelsFromCurrency();
});
</script>
@endsection