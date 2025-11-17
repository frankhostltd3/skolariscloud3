@extends('tenant.layouts.app')
@section('title', isset($expense) ? 'Edit Expense' : 'Record Expense')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">{{ isset($expense) ? 'Edit' : 'Record' }} Expense</h1>
        <div class="card">
            <div class="card-body">
                <form method="POST"
                    action="{{ isset($expense) ? route('tenant.finance.expenses.update', $expense) : route('tenant.finance.expenses.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if (isset($expense))
                        @method('PUT')
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Title <span class="text-danger">*</span></label><input
                                type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $expense->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Amount <span
                                    class="text-danger">*</span></label><input type="number" step="0.01" name="amount"
                                class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount', $expense->amount ?? '') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Category <span
                                    class="text-danger">*</span></label><select name="expense_category_id"
                                class="form-select @error('expense_category_id') is-invalid @enderror" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('expense_category_id', $expense->expense_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('expense_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Currency <span
                                    class="text-danger">*</span></label><select name="currency_id"
                                class="form-select @error('currency_id') is-invalid @enderror" required>
                                @foreach ($currencies as $curr)
                                    <option value="{{ $curr->id }}"
                                        {{ old('currency_id', $expense->currency_id ?? currentCurrency()->id) == $curr->id ? 'selected' : '' }}>
                                        {{ $curr->code }} - {{ $curr->name }}</option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Expense Date <span
                                    class="text-danger">*</span></label><input type="date" name="expense_date"
                                class="form-control @error('expense_date') is-invalid @enderror"
                                value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : date('Y-m-d')) }}"
                                required>
                            @error('expense_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Payment Method <span
                                    class="text-danger">*</span></label><select name="payment_method"
                                class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="cash"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'cash' ? 'selected' : '' }}>
                                    Cash</option>
                                <option value="bank_transfer"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>
                                    Bank Transfer</option>
                                <option value="credit_card"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'credit_card' ? 'selected' : '' }}>
                                    Credit Card</option>
                                <option value="debit_card"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'debit_card' ? 'selected' : '' }}>
                                    Debit Card</option>
                                <option value="check"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'check' ? 'selected' : '' }}>
                                    Check</option>
                                <option value="online_payment"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'online_payment' ? 'selected' : '' }}>
                                    Online Payment</option>
                                <option value="other"
                                    {{ old('payment_method', $expense->payment_method ?? '') == 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Reference Number</label><input type="text"
                                name="reference_number" class="form-control"
                                value="{{ old('reference_number', $expense->reference_number ?? '') }}"></div>
                        <div class="col-md-6"><label class="form-label">Vendor Name</label><input type="text"
                                name="vendor_name" class="form-control"
                                value="{{ old('vendor_name', $expense->vendor_name ?? '') }}"></div>
                        <div class="col-md-6"><label class="form-label">Vendor Contact</label><input type="text"
                                name="vendor_contact" class="form-control"
                                value="{{ old('vendor_contact', $expense->vendor_contact ?? '') }}"></div>
                        <div class="col-md-6"><label class="form-label">Receipt (PDF, JPG, PNG)</label><input type="file"
                                name="receipt" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                        <div class="col-12"><label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description ?? '') }}</textarea>
                        </div>
                        <div class="col-12"><label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $expense->notes ?? '') }}</textarea>
                        </div>
                        <div class="col-12"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>
                                {{ isset($expense) ? 'Update' : 'Record' }} Expense</button><a
                                href="{{ route('tenant.finance.expenses.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
