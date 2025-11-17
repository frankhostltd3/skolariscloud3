@extends('tenant.layouts.app')

@section('title', __('Create Invoice'))

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
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.tuition_plans') }}">
      <span class="bi bi-file-earmark-text me-2"></span>{{ __('Tuition Plans') }}
    </a>
    <a class="list-group-item list-group-item-action active" href="{{ route('tenant.modules.financials.invoices') }}">
      <span class="bi bi-file-earmark-pdf me-2"></span>{{ __('Invoices') }}
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">{{ __('Invoice Tips') }}</div>
  <div class="card-body">
    <div class="small text-muted">
      <div class="mb-3">
        <strong>{{ __('Invoice Components') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('Student: Select the student to bill') }}</li>
          <li>{{ __('Parent: Optional parent/guardian') }}</li>
          <li>{{ __('Items: Add fee items with quantities') }}</li>
          <li>{{ __('Taxes: Apply tax rates per item') }}</li>
          <li>{{ __('Discounts: Apply discounts as needed') }}</li>
        </ul>
      </div>

      <div class="alert alert-info small p-2">
        <strong>{{ __('Tip:') }}</strong> {{ __('Use the "Add Item" button to include multiple fee items in one invoice.') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h5 fw-semibold mb-0">{{ __('Create Invoice') }}</h1>
        <div class="small text-secondary">{{ __('Generate a new invoice for student billing') }}</div>
    </div>
    <div class="card-body">
        <form action="{{ route('tenant.modules.financials.invoices.store') }}" method="POST" id="invoiceForm">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">{{ __('Student') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('student_id') is-invalid @enderror"
                                id="student_id" name="student_id" required>
                            <option value="">{{ __('Select Student') }}</option>
                            <!-- Students would be populated from database -->
                            <option value="1" {{ old('student_id') == '1' ? 'selected' : '' }}>John Doe (Grade 10)</option>
                            <option value="2" {{ old('student_id') == '2' ? 'selected' : '' }}>Jane Smith (Grade 9)</option>
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">{{ __('Parent/Guardian') }}</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror"
                                id="parent_id" name="parent_id">
                            <option value="">{{ __('Select Parent (Optional)') }}</option>
                            <!-- Parents would be populated from database -->
                            <option value="1" {{ old('parent_id') == '1' ? 'selected' : '' }}>Mr. John Doe Sr.</option>
                            <option value="2" {{ old('parent_id') == '2' ? 'selected' : '' }}>Mrs. Jane Smith</option>
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="issue_date" class="form-label">{{ __('Issue Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                               id="issue_date" name="issue_date"
                               value="{{ old('issue_date', date('Y-m-d')) }}" required>
                        @error('issue_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="due_date" class="form-label">{{ __('Due Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                               id="due_date" name="due_date"
                               value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Invoice Items Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('Invoice Items') }}</h6>
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
                                        <span class="input-group-text">{{ currency_symbol() }}</span>
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
                                        <span class="input-group-text">{{ currency_symbol() }}</span>
                                        <input type="number" class="form-control discount-input @error('items.0.discount_amount') is-invalid @enderror"
                                               name="items[0][discount_amount]" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    @error('items.0.discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Line Total') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ currency_symbol() }}</span>
                                        <input type="text" class="form-control line-total-input" readonly value="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Totals -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Subtotal:') }}</span>
                                    <span id="subtotalDisplay">{{ currency_symbol() }}0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Tax:') }}</span>
                                    <span id="taxDisplay">{{ currency_symbol() }}0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Discount:') }}</span>
                                    <span id="discountDisplay">{{ currency_symbol() }}0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>{{ __('Total:') }}</span>
                                    <span id="totalDisplay">{{ currency_symbol() }}0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                <textarea class="form-control @error('notes') is-invalid @enderror"
                          id="notes" name="notes" rows="3"
                          placeholder="{{ __('Additional notes for this invoice...') }}">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Create Invoice') }}
                </button>
                <a href="{{ route('tenant.modules.financials.invoices') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const CURRENCY_SYMBOL = @json(currency_symbol());
    let itemIndex = 1;

    // Add item button functionality
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItemRow();
    });

    // Function to add a new item row
    function addItemRow() {
        const container = document.getElementById('itemsContainer');
        const itemRow = createItemRow(itemIndex);
        container.appendChild(itemRow);
        itemIndex++;

        // Enable remove buttons if more than one item
        updateRemoveButtons();
        attachEventListeners();
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
                                {{ $fee->name }} ({{ format_money($fee->amount) }})
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
                        <span class="input-group-text">{{ currency_symbol() }}</span>
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
                        <span class="input-group-text">{{ currency_symbol() }}</span>
                        <input type="number" class="form-control discount-input" name="items[${index}][discount_amount]" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">${__('Line Total')}</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ currency_symbol() }}</span>
                        <input type="text" class="form-control line-total-input" readonly value="0.00">
                    </div>
                </div>
            </div>
        `;

        return div;
    }

    // Function to update remove buttons state
    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-item-btn');
        const itemRows = document.querySelectorAll('.item-row');

        removeButtons.forEach(button => {
            button.disabled = itemRows.length <= 1;
        });
    }

    // Function to attach event listeners to dynamic elements
    function attachEventListeners() {
        // Fee selection change
        document.querySelectorAll('.fee-select').forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const amount = selectedOption.getAttribute('data-amount');
                const unitPriceInput = this.closest('.item-row').querySelector('.unit-price-input');

                if (amount && unitPriceInput) {
                    unitPriceInput.value = amount;
                    calculateLineTotal(this.closest('.item-row'));
                    calculateInvoiceTotal();
                }
            });
        });

        // Quantity, unit price, tax rate, discount change
        document.querySelectorAll('.quantity-input, .unit-price-input, .tax-rate-input, .discount-input').forEach(input => {
            input.addEventListener('input', function() {
                calculateLineTotal(this.closest('.item-row'));
                calculateInvoiceTotal();
            });
        });

        // Remove item button
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.disabled) {
                    this.closest('.item-row').remove();
                    updateRemoveButtons();
                    calculateInvoiceTotal();
                }
            });
        });
    }

    // Function to calculate line total
    function calculateLineTotal(itemRow) {
        const quantity = parseFloat(itemRow.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(itemRow.querySelector('.unit-price-input').value) || 0;
        const taxRate = parseFloat(itemRow.querySelector('.tax-rate-input').value) || 0;
        const discount = parseFloat(itemRow.querySelector('.discount-input').value) || 0;

        const subtotal = quantity * unitPrice;
        const taxAmount = subtotal * (taxRate / 100);
        const total = subtotal + taxAmount - discount;

        itemRow.querySelector('.line-total-input').value = total.toFixed(2);
    }

    // Function to calculate invoice total
    function calculateInvoiceTotal() {
        let subtotal = 0;
        let totalTax = 0;
        let totalDiscount = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

            const itemSubtotal = quantity * unitPrice;
            const itemTax = itemSubtotal * (taxRate / 100);

            subtotal += itemSubtotal;
            totalTax += itemTax;
            totalDiscount += discount;
        });

        const total = subtotal + totalTax - totalDiscount;

        document.getElementById('subtotalDisplay').textContent = CURRENCY_SYMBOL + subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = CURRENCY_SYMBOL + totalTax.toFixed(2);
        document.getElementById('discountDisplay').textContent = CURRENCY_SYMBOL + totalDiscount.toFixed(2);
        document.getElementById('totalDisplay').textContent = CURRENCY_SYMBOL + total.toFixed(2);
    }

    // Initialize event listeners for existing elements
    attachEventListeners();
    updateRemoveButtons();
});
</script>
@endsection