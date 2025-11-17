@extends('tenant.layouts.app')

@section('title', __('Fee Payments'))

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0"><i class="bi bi-wallet2 me-2"></i>{{ __('Fee Payments') }}</h4>
      <p class="text-muted mb-0">{{ __('Review and verify bank deposit proofs submitted by students.') }}</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form class="row g-3" method="GET">
        <div class="col-md-3">
          <label class="form-label">{{ __('Status') }}</label>
          <select name="status" class="form-select" onchange="this.form.submit()">
            @php $currentStatus = request('status','pending'); @endphp
            <option value="">{{ __('All') }}</option>
            <option value="pending" {{ $currentStatus==='pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
            <option value="confirmed" {{ $currentStatus==='confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
            <option value="failed" {{ $currentStatus==='failed' ? 'selected' : '' }}>{{ __('Rejected/Failed') }}</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">{{ __('Method') }}</label>
          <select name="method" class="form-select" onchange="this.form.submit()">
            @php $currentMethod = request('method'); @endphp
            <option value="">{{ __('All') }}</option>
            <option value="bank" {{ $currentMethod==='bank' ? 'selected' : '' }}>{{ __('Bank') }}</option>
            <option value="mtn" {{ $currentMethod==='mtn' ? 'selected' : '' }}>{{ __('Mobile Money (MTN)') }}</option>
            <option value="airtel" {{ $currentMethod==='airtel' ? 'selected' : '' }}>{{ __('Mobile Money (Airtel)') }}</option>
            <option value="card" {{ $currentMethod==='card' ? 'selected' : '' }}>{{ __('Card') }}</option>
            <option value="cash" {{ $currentMethod==='cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end">
          <a href="{{ route('tenant.modules.financials.payments', ['status' => 'pending', 'method' => 'bank']) }}" class="btn btn-outline-primary">
            <i class="bi bi-hourglass-split me-2"></i>{{ __('Show Pending Bank Proofs') }}
          </a>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="bg-light">
            <tr>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Student') }}</th>
              <th>{{ __('Amount') }}</th>
              <th>{{ __('Method') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('Proof') }}</th>
              <th>{{ __('Reference') }}</th>
              <th class="text-end">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $payment)
              @php $proof = $payment->meta['proof_path'] ?? null; @endphp
              <tr>
                <td>{{ optional($payment->paid_at)->format('Y-m-d H:i') ?? $payment->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $payment->student->name ?? '—' }}</td>
                <td class="fw-semibold">{{ format_money($payment->amount) }}</td>
                <td>{{ ucfirst($payment->method) }}</td>
                <td>
                  @if($payment->status === 'pending')
                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                  @elseif($payment->status === 'confirmed')
                    <span class="badge bg-success">{{ __('Confirmed') }}</span>
                  @else
                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                  @endif
                </td>
                <td>
                  @if($proof)
                    <a href="{{ Storage::disk('public')->url($proof) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-file-earmark-arrow-down"></i> {{ __('View') }}
                    </a>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td><code>{{ $payment->reference }}</code></td>
                <td class="text-end">
                  @if($payment->status === 'pending' && $payment->method === 'bank')
                    <div class="d-inline-flex gap-2">
                      <form method="POST" action="{{ route('tenant.modules.financials.payments.confirm', $payment) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                          <i class="bi bi-check2-circle"></i> {{ __('Confirm') }}
                        </button>
                      </form>
                      <form method="POST" action="{{ route('tenant.modules.financials.payments.reject', $payment) }}">
                        @csrf
                        <input type="hidden" name="reason" value="{{ __('Bank slip rejected after review') }}">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-x-circle"></i> {{ __('Reject') }}
                        </button>
                      </form>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">{{ __('No payments found.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="p-3">
        {{ $payments->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
