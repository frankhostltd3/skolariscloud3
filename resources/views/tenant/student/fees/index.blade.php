@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Fees & Payments')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-cash me-2"></i>{{ __('Fees & Payments') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Manage your school fees and payments') }}</p>
        </div>
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

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('Total Paid') }}</h6>
                            <h3 class="mb-0 text-success">{{ format_money($totalPaid) }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('Outstanding') }}</h6>
                            <h3 class="mb-0 {{ $totalOutstanding > 0 ? 'text-danger' : 'text-success' }}">
                                {{ format_money($totalOutstanding) }}
                            </h3>
                        </div>
                        <div class="{{ $totalOutstanding > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 2rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('Active Fees') }}</h6>
                            <h3 class="mb-0 text-info">{{ $fees->count() }}</h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fees List -->
    @if($fees->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-list-ul me-2"></i>{{ __('Your Fees') }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Fee Name') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Outstanding') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees as $feeData)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $feeData['fee']->name }}</strong>
                                            <br><small class="text-muted">{{ $feeData['fee']->description }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ format_money($feeData['assignment']->amount ?? 0) }}</strong>
                                    </td>
                                    <td class="text-success">
                                        {{ format_money($feeData['total_paid']) }}
                                    </td>
                                    <td class="{{ $feeData['outstanding'] > 0 ? 'text-danger' : 'text-muted' }}">
                                        {{ format_money($feeData['outstanding']) }}
                                    </td>
                                    <td>
                                        @if($feeData['is_paid'])
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>{{ __('Paid') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>{{ __('Pending') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($feeData['assignment'] && $feeData['assignment']->due_date)
                                            {{ $feeData['assignment']->due_date->format('M d, Y') }}
                                            @if($feeData['assignment']->due_date->isPast() && !$feeData['is_paid'])
                                                <br><small class="text-danger">{{ $feeData['assignment']->due_date->diffInDays(now()) }} days overdue</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('tenant.student.fees.show', $feeData['fee']) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>{{ __('View') }}
                                        </a>
                                        @if(!$feeData['is_paid'])
                                            <form method="POST"
                                                  action="{{ route('tenant.student.fees.pay', $feeData['fee']) }}"
                                                  class="d-inline ms-1">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-credit-card me-1"></i>{{ __('Pay Now') }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">{{ __('No Fees Assigned') }}</h5>
                <p class="text-muted">
                    {{ __('You currently have no fees assigned to your account.') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Recent Payments -->
    @if($recentPayments->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2"></i>{{ __('Recent Payments') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($recentPayments as $payment)
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $payment->fee->name }}</h6>
                                            <p class="text-muted small mb-1">{{ $payment->reference }}</p>
                                            <p class="text-muted small mb-0">{{ $payment->paid_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-1 text-success">{{ format_money($payment->amount) }}</h5>
                                            <span class="badge bg-{{ $payment->status === 'confirmed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Information -->
    <div class="alert alert-info mt-4">
        <h6 class="alert-heading">
            <i class="bi bi-info-circle me-2"></i>{{ __('Payment Information') }}
        </h6>
        <ul class="mb-0 small">
            <li>{{ __('You can pay your fees online using various payment methods') }}</li>
            <li>{{ __('Payments are processed securely through our payment gateway') }}</li>
            <li>{{ __('Receipts are automatically generated for all successful payments') }}</li>
            <li>{{ __('Contact the school administration if you have any payment issues') }}</li>
        </ul>
    </div>
</div>
@endsection
