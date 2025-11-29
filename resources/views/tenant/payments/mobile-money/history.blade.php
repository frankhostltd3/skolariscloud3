@extends('tenant.layouts.app')

@section('title', 'Payment History')

@section('content')
    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="bi bi-clock-history me-2"></i>Payment History
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payment History</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('tenant.payments.mobile-money.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>New Payment
            </a>
        </div>

        {{-- Statistics Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 opacity-75">Total Transactions</h6>
                                <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                            </div>
                            <i class="bi bi-receipt fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 opacity-75">Completed</h6>
                                <h3 class="mb-0">{{ number_format($stats['completed']) }}</h3>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-warning text-dark h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 opacity-75">Pending</h6>
                                <h3 class="mb-0">{{ number_format($stats['pending']) }}</h3>
                            </div>
                            <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 opacity-75">Total Received</h6>
                                <h3 class="mb-0">{{ formatMoney($stats['total_amount']) }}</h3>
                            </div>
                            <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('tenant.payments.mobile-money.history') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control"
                            value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control"
                            value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('tenant.payments.mobile-money.history') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Phone Number</th>
                                <th>Description</th>
                                <th>Gateway</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code class="small">{{ Str::limit($transaction->transaction_id, 20) }}</code>
                                    </td>
                                    <td>
                                        <span class="small">{{ $transaction->created_at->format('M d, Y') }}</span>
                                        <br>
                                        <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>{{ $transaction->phone_number }}</td>
                                    <td>
                                        <span class="small">{{ Str::limit($transaction->description, 30) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $transaction->mobileMoneyGateway->name ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $transaction->status_badge_class }}">
                                            <i class="{{ $transaction->status_icon }} me-1"></i>
                                            {{ $transaction->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tenant.payments.mobile-money.status', $transaction->transaction_id) }}"
                                                class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if ($transaction->canRetry())
                                                <a href="{{ route('tenant.payments.mobile-money.create') }}?amount={{ $transaction->amount }}&phone={{ $transaction->phone_number }}"
                                                    class="btn btn-outline-success" title="Retry">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">No transactions found</p>
                                        <a href="{{ route('tenant.payments.mobile-money.create') }}"
                                            class="btn btn-primary mt-3">
                                            <i class="bi bi-plus-lg me-1"></i>Make a Payment
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($transactions->hasPages())
                <div class="card-footer">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
