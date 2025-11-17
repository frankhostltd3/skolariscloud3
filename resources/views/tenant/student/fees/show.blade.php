@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Fee Details - ' . $fee->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-receipt me-2"></i>{{ $fee->name }}
            </h4>
            <p class="text-muted mb-0">{{ __('Fee Details & Payment History') }}</p>
        </div>
        <a href="{{ route('tenant.student.fees.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Fees') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Fee Summary -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold">{{ __('Fee Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('Fee Name') }}</h6>
                            <p class="mb-3">{{ $fee->name }}</p>

                            <h6 class="text-muted">{{ __('Description') }}</h6>
                            <p class="mb-3">{{ $fee->description ?: 'No description provided' }}</p>

                            <h6 class="text-muted">{{ __('Category') }}</h6>
                            <p class="mb-3">
                                <span class="badge bg-secondary">{{ $fee->category ?: 'General' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('Assigned Amount') }}</h6>
                            <p class="mb-3 fs-5 fw-semibold text-primary">{{ format_money($assignment->amount) }}</p>

                            <h6 class="text-muted">{{ __('Due Date') }}</h6>
                            <p class="mb-3">
                                @if($assignment->due_date)
                                    {{ $assignment->due_date->format('F d, Y') }}
                                    @if($assignment->due_date->isPast() && !$isPaid)
                                        <br><span class="badge bg-danger">{{ $assignment->due_date->diffInDays(now()) }} days overdue</span>
                                    @endif
                                @else
                                    <span class="text-muted">No due date set</span>
                                @endif
                            </p>

                            <h6 class="text-muted">{{ __('Status') }}</h6>
                            <p class="mb-3">
                                @if($isPaid)
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle me-1"></i>{{ __('Fully Paid') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="bi bi-clock me-1"></i>{{ __('Payment Pending') }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-semibold">{{ __('Payment Summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('Total Amount') }}</span>
                            <span class="fw-semibold">{{ format_money($assignment->amount) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('Total Paid') }}</span>
                            <span class="text-success fw-semibold">{{ format_money($totalPaid) }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ __('Outstanding') }}</span>
                            <span class="text-{{ $outstanding > 0 ? 'danger' : 'success' }} fw-semibold">
                                {{ format_money($outstanding) }}
                            </span>
                        </div>
                    </div>

                    @if(!$isPaid)
                        <form method="POST" action="{{ route('tenant.student.fees.pay', $fee) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('Payment Amount') }}</label>
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" min="0.01" step="0.01" max="{{ max($outstanding, 0) }}" value="{{ old('amount', $outstanding) }}" required>
                                <div class="form-text">{{ __('Enter the amount you wish to pay (partial payments allowed).') }}</div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Payment Method') }}</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="card">{{ __('Credit/Debit Card') }}</option>
                                    <option value="bank">{{ __('Bank Transfer') }}</option>
                                    <option value="mobile">{{ __('Mobile Money') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-credit-card me-2"></i>{{ __('Submit Payment') }}
                            </button>
                        </form>
                        <hr>
                        <a href="{{ route('tenant.student.fees.bank_slip', $fee) }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-download me-2"></i>{{ __('Download Bank Payment Slip (PDF)') }}
                        </a>
                        <div class="mt-3">
                            <form method="POST" action="{{ route('tenant.student.fees.upload_proof', $fee) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">{{ __('Bank Deposit Amount') }}</label>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" min="0.01" step="0.01" max="{{ max($outstanding, 0) }}" value="{{ old('amount', $outstanding) }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ __('Bank Reference (optional)') }}</label>
                                    <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}" maxlength="120" placeholder="e.g. Deposit Slip No or Narrative">
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Upload Bank Slip Proof (PDF/JPG/PNG)') }}</label>
                                    <input type="file" name="proof" class="form-control @error('proof') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                                    @error('proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">{{ __('Max size: 5MB') }}</div>
                                </div>
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-upload me-2"></i>{{ __('Upload Bank Slip Proof') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>{{ __('This fee has been fully paid.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-clock-history me-2"></i>{{ __('Payment History') }}
            </h5>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Method') }}</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->paid_at->format('M d, Y H:i') }}</td>
                                    <td class="fw-semibold">{{ format_money($payment->amount) }}</td>
                                    <td>{{ ucfirst($payment->method) }}</td>
                                    <td><code>{{ $payment->reference }}</code></td>
                                    <td>
                                        @if($payment->status === 'confirmed')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>{{ __('Confirmed') }}
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>{{ __('Pending') }}
                                            </span>
                                        @elseif($payment->status === 'failed')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>{{ __('Failed') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 text-muted">{{ __('No Payment History') }}</h6>
                    <p class="text-muted">{{ __('No payments have been made for this fee yet.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
