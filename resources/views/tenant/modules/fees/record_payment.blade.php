@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Record Payment') }}: {{ $fee->name }}</h1>
    <div class="small text-secondary">{{ __('Record a manual fee payment') }}</div>
  </div>
  <a href="{{ route('tenant.modules.fees.show', $fee) }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
  </a>
</div>

<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('tenant.modules.fees.store-payment', $fee) }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Student') }} <span class="text-danger">*</span></label>
            <select class="form-select @error('student_id') is-invalid @enderror" name="student_id" required>
              <option value="">{{ __('Select student who made payment') }}</option>
              @foreach($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                  {{ $student->full_name }} - {{ $student->admission_number }}
                </option>
              @endforeach
            </select>
            @error('student_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-medium">{{ __('Amount') }} <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">{{ currency_symbol() }}</span>
                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                       name="amount" value="{{ old('amount', $fee->amount) }}" required>
                @error('amount')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <small class="text-secondary">{{ __('Fee amount') }}: {{ format_money($fee->amount) }}</small>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-medium">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                     name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
              @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-medium">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                <option value="">{{ __('Select method') }}</option>
                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>{{ __('Cheque') }}</option>
                <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>{{ __('Online Payment') }}</option>
                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>{{ __('Mobile Money') }}</option>
              </select>
              @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label fw-medium">{{ __('Reference Number') }}</label>
              <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                     name="reference" value="{{ old('reference') }}" 
                     placeholder="{{ __('Transaction ref, cheque no, etc.') }}">
              @error('reference')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium">{{ __('Notes') }}</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" 
                      name="notes" rows="3" placeholder="{{ __('Additional information about this payment') }}">{{ old('notes') }}</textarea>
            @error('notes')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle me-1"></i>{{ __('Record Payment') }}
            </button>
            <a href="{{ route('tenant.modules.fees.show', $fee) }}" class="btn btn-outline-secondary">
              {{ __('Cancel') }}
            </a>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm mt-3 border-info">
      <div class="card-header bg-info bg-opacity-10">
        <h6 class="mb-0 text-info"><i class="bi bi-lightbulb me-2"></i>{{ __('Information') }}</h6>
      </div>
      <div class="card-body">
        <ul class="mb-0 small">
          <li>{{ __('This form is for recording payments received through offline methods') }}</li>
          <li>{{ __('Online payments are recorded automatically through the payment gateway') }}</li>
          <li>{{ __('You can adjust the amount for partial payments or apply discounts') }}</li>
          <li>{{ __('Always include a reference number for bank transfers or cheques') }}</li>
          <li>{{ __('The payment will be marked as completed immediately') }}</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
