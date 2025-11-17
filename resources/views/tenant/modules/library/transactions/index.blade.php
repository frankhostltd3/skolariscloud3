@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-arrow-left-right me-2"></i>Library Transactions</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.index') }}">Library</a></li>
                    <li class="breadcrumb-item active">Transactions</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('tenant.modules.library.transactions.borrow') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Issue Book
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.modules.library.transactions.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by user or book..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Book</th>
                            <th>Borrowed Date</th>
                            <th>Due Date</th>
                            <th>Returned Date</th>
                            <th>Status</th>
                            <th>Fine</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $transaction->user->name }}</div>
                                <small class="text-muted">{{ $transaction->user->email }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($transaction->book->title, 30) }}</div>
                                <small class="text-muted">{{ $transaction->book->author }}</small>
                            </td>
                            <td><small>{{ $transaction->borrowed_at->format('M d, Y') }}</small></td>
                            <td>
                                <small class="{{ $transaction->isOverdue() ? 'text-danger fw-semibold' : '' }}">
                                    {{ $transaction->due_date->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                @if($transaction->returned_at)
                                <small>{{ $transaction->returned_at->format('M d, Y') }}</small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->status === 'borrowed')
                                    @if($transaction->isOverdue())
                                        <span class="badge bg-danger">Overdue ({{ $transaction->days_overdue }}d)</span>
                                    @else
                                        <span class="badge bg-info">Borrowed</span>
                                    @endif
                                @elseif($transaction->status === 'returned')
                                    <span class="badge bg-success">Returned</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->fine_amount > 0)
                                <span class="text-danger fw-semibold">{{ format_money($transaction->fine_amount) }}</span>
                                @if($transaction->fine_paid)
                                    <i class="bi bi-check-circle text-success" title="Paid"></i>
                                @else
                                    <i class="bi bi-exclamation-circle text-danger" title="Unpaid"></i>
                                @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($transaction->status === 'borrowed')
                                <button type="button" class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#returnModal{{ $transaction->id }}">
                                    <i class="bi bi-arrow-return-left me-1"></i>Return
                                </button>

                                <!-- Return Modal -->
                                <div class="modal fade" id="returnModal{{ $transaction->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('tenant.modules.library.transactions.return', $transaction) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Return Book</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Book:</strong> {{ $transaction->book->title }}</p>
                                                    <p><strong>Borrower:</strong> {{ $transaction->user->name }}</p>
                                                    @if($transaction->isOverdue())
                                                    <div class="alert alert-warning">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        This book is overdue by {{ $transaction->days_overdue }} days.
                                                        Suggested fine: {{ format_money($transaction->calculateFine()) }}
                                                    </div>
                                                    @endif
                                                    <div class="mb-3">
                                                        <label class="form-label">Fine Amount (if any)</label>
                                                        <input type="number" name="fine_amount" class="form-control" 
                                                               value="{{ $transaction->calculateFine() }}" 
                                                               step="0.01" min="0">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Condition Notes</label>
                                                        <textarea name="condition_notes" class="form-control" rows="3" 
                                                                  placeholder="Note any damage or issues with the returned book..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Process Return</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted mb-3">No transactions found</p>
                                <a href="{{ route('tenant.modules.library.transactions.borrow') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Issue First Book
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
