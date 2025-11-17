@extends('tenant.layouts.app')
@section('title', 'Record Payment')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Record Payment</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tenant.finance.payments.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice <span class="text-danger">*</span></label>
                            <select name="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror"
                                id="invoiceSelect" required>
                                <option value="">Select Invoice</option>
                                @foreach ($invoices as $inv)
                                    <option value="{{ $inv->id }}" data-balance="{{ $inv->balance }}"
                                        {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>
                                        {{ $inv->invoice_number }} - {{ $inv->student->name }} (Balance:
                                        {{ formatMoney($inv->balance) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('invoice_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date"
                                class="form-control @error('payment_date') is-invalid @enderror"
                                value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount"
                                class="form-control @error('amount') is-invalid @enderror" id="amountInput"
                                value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror"
                                required>
                                <option value="">Select Method</option>
                                @foreach (['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money', 'cheque' => 'Cheque', 'card' => 'Card'] as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number"
                                class="form-control @error('reference_number') is-invalid @enderror"
                                value="{{ old('reference_number') }}">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Record
                            Payment</button>
                        <a href="{{ route('tenant.finance.payments.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.getElementById('invoiceSelect').addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const balance = selected.getAttribute('data-balance');
                if (balance) {
                    document.getElementById('amountInput').value = balance;
                }
            });
        </script>
    @endpush
@endsection
