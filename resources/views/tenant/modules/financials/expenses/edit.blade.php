@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Edit Expense') }}</h1>
    <div class="small text-secondary">{{ __('Update expense information') }}</div>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('tenant.modules.financials.expenses.show', $expense) }}" class="btn btn-outline-info">
      <i class="bi bi-eye me-1"></i>{{ __('View') }}
    </a>
    <a href="{{ route('tenant.modules.financials.expenses') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Expenses') }}
    </a>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('tenant.modules.financials.expenses.update', $expense) }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label for="title" class="form-label">{{ __('Expense Title') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $expense->title) }}" required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="expense_date" class="form-label">{{ __('Expense Date') }} <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                @error('expense_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $expense->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="expense_category_id" class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id" required>
                  <option value="">{{ __('Select Category') }}</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
                @error('expense_category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="mb-3">
                <label for="currency_id" class="form-label">{{ __('Currency') }} <span class="text-danger">*</span></label>
                <select class="form-select @error('currency_id') is-invalid @enderror" id="currency_id" name="currency_id" required>
                  @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}" {{ old('currency_id', $expense->currency_id) == $currency->id ? 'selected' : '' }}>
                      {{ $currency->code }} - {{ $currency->name }}
                    </option>
                  @endforeach
                </select>
                @error('currency_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="mb-3">
                <label for="amount" class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" required>
                  @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="payment_method" class="form-label">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                  <option value="">{{ __('Select Payment Method') }}</option>
                  <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                  <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                  <option value="credit_card" {{ old('payment_method', $expense->payment_method) == 'credit_card' ? 'selected' : '' }}>{{ __('Credit Card') }}</option>
                  <option value="debit_card" {{ old('payment_method', $expense->payment_method) == 'debit_card' ? 'selected' : '' }}>{{ __('Debit Card') }}</option>
                  <option value="check" {{ old('payment_method', $expense->payment_method) == 'check' ? 'selected' : '' }}>{{ __('Check') }}</option>
                  <option value="online_payment" {{ old('payment_method', $expense->payment_method) == 'online_payment' ? 'selected' : '' }}>{{ __('Online Payment') }}</option>
                  <option value="other" {{ old('payment_method', $expense->payment_method) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                </select>
                @error('payment_method')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="reference_number" class="form-label">{{ __('Reference Number') }}</label>
                <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number', $expense->reference_number) }}">
                @error('reference_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="vendor_name" class="form-label">{{ __('Vendor/Supplier Name') }}</label>
                <input type="text" class="form-control @error('vendor_name') is-invalid @enderror" id="vendor_name" name="vendor_name" value="{{ old('vendor_name', $expense->vendor_name) }}">
                @error('vendor_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="vendor_contact" class="form-label">{{ __('Vendor Contact') }}</label>
                <input type="text" class="form-control @error('vendor_contact') is-invalid @enderror" id="vendor_contact" name="vendor_contact" value="{{ old('vendor_contact', $expense->vendor_contact) }}">
                @error('vendor_contact')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="receipt" class="form-label">{{ __('Receipt/Invoice') }}</label>
            <input type="file" class="form-control @error('receipt') is-invalid @enderror" id="receipt" name="receipt" accept="image/*,.pdf">
            <div class="form-text">
              {{ __('Upload receipt image or PDF (max 2MB)') }}
              @if($expense->receipt_path)
                <br>{{ __('Current:') }} <a href="{{ \Storage::disk('public')->url($expense->receipt_path) }}" target="_blank">{{ __('View current receipt') }}</a>
              @endif
            </div>
            @error('receipt')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label">{{ __('Additional Notes') }}</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $expense->notes) }}</textarea>
            @error('notes')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('tenant.modules.financials.expenses.show', $expense) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Update Expense') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Expense Status') }}</h6>
      </div>
      <div class="card-body">
        @if($expense->status === 'approved')
          <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>{{ __('This expense has been approved') }}
          </div>
        @elseif($expense->status === 'pending')
          <div class="alert alert-warning">
            <i class="bi bi-clock me-2"></i>{{ __('This expense is pending approval') }}
          </div>
        @elseif($expense->status === 'rejected')
          <div class="alert alert-danger">
            <i class="bi bi-x-circle me-2"></i>{{ __('This expense was rejected') }}
          </div>
        @endif

        @if($expense->status === 'rejected' && $expense->rejected_reason)
          <div class="mt-3">
            <label class="form-label fw-semibold small">{{ __('Rejection Reason') }}</label>
            <p class="small text-muted">{{ $expense->rejected_reason }}</p>
          </div>
        @endif
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Quick Tips') }}</h6>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mb-0 small">
          <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Always attach receipts for reimbursement') }}</li>
          <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Use descriptive titles for easy tracking') }}</li>
          <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Select appropriate categories for accurate reporting') }}</li>
          <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>{{ __('Changes may require re-approval') }}</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection