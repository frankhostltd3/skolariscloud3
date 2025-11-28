@extends('layouts.tenant.student')

@section('title', 'My Borrows')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-bookmark me-2"></i>{{ __('My Borrowed Books') }}
            </h4>
            <a href="{{ route('tenant.student.library.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Library') }}
                </h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Active Borrows') }}</h6>
                                <h3 class="mb-0 text-info">{{ $statistics['active'] }}</h3>
                            </div>
                            <div class="text-info" style="font-size: 2rem;">
                                <i class="bi bi-bookmark-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Overdue') }}</h6>
                                <h3 class="mb-0 {{ $statistics['overdue'] > 0 ? 'text-danger' : '' }}">
                                    {{ $statistics['overdue'] }}</h3>
                            </div>
                            <div class="{{ $statistics['overdue'] > 0 ? 'text-danger' : 'text-secondary' }}"
                                style="font-size: 2rem;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Returned') }}</h6>
                                <h3 class="mb-0 text-success">{{ $statistics['returned'] }}</h3>
                            </div>
                            <div class="text-success" style="font-size: 2rem;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Total Fines') }}</h6>
                                <h3 class="mb-0 {{ $statistics['total_fines'] > 0 ? 'text-warning' : '' }}">
                                    {{ format_money($statistics['total_fines']) }}
                                </h3>
                            </div>
                            <div class="{{ $statistics['total_fines'] > 0 ? 'text-warning' : 'text-secondary' }}"
                                style="font-size: 2rem;">
                                <i class="bi bi-cash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('tenant.student.library.my-borrows') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small">{{ __('Status') }}</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ __('Active Borrows') }}
                            </option>
                            <option value="overdue" {{ $status == 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}
                            </option>
                            <option value="returned" {{ $status == 'returned' ? 'selected' : '' }}>{{ __('Returned') }}
                            </option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions List -->
        @if ($transactions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Book') }}</th>
                                    <th>{{ __('Borrowed Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Renewals') }}</th>
                                    <th>{{ __('Fine') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($transaction->book->cover_image_url)
                                                    <img src="{{ $transaction->book->cover_image_url }}"
                                                        class="me-2 rounded" width="40" height="60"
                                                        style="object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $transaction->book->title }}</strong><br>
                                                    <small class="text-muted">{{ $transaction->book->author }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $transaction->borrowed_at->format('M d, Y') }}</td>
                                        <td>
                                            {{ $transaction->due_date->format('M d, Y') }}
                                            @if ($transaction->status === 'borrowed' && !$transaction->isOverdue())
                                                <br><small
                                                    class="text-muted">{{ $transaction->due_date->diffInDays(now()) }} days
                                                    left</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->status === 'returned')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>{{ __('Returned') }}
                                                </span>
                                                @if ($transaction->returned_at)
                                                    <br><small
                                                        class="text-muted">{{ $transaction->returned_at->format('M d, Y') }}</small>
                                                @endif
                                            @elseif($transaction->isOverdue())
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ __('Overdue') }}
                                                </span>
                                                <br><small class="text-danger">{{ $transaction->days_overdue }} days
                                                    late</small>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="bi bi-bookmark me-1"></i>{{ __('Borrowed') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $transaction->renewal_count }}/2
                                        </td>
                                        <td>
                                            @if ($transaction->fine_amount > 0)
                                                <span class="text-warning">
                                                    {{ format_money($transaction->fine_amount) }}
                                                </span>
                                                @if (!$transaction->fine_paid)
                                                    <br><span class="badge bg-warning text-dark">{{ __('Unpaid') }}</span>
                                                @else
                                                    <br><span class="badge bg-success">{{ __('Paid') }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->status === 'borrowed')
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('tenant.student.library.show', $transaction->book->id) }}"
                                                        class="btn btn-outline-primary" title="{{ __('View Book') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if ($transaction->renewal_count < 2 && !$transaction->isOverdue())
                                                        <form method="POST"
                                                            action="{{ route('tenant.student.library.extend', $transaction->id) }}"
                                                            class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning"
                                                                title="{{ __('Request Extension') }}">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <a href="{{ route('tenant.student.library.show', $transaction->book->id) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>{{ __('View') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if (method_exists($transactions, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('No Transactions Found') }}</h5>
                    <p class="text-muted">
                        @if ($status === 'active')
                            {{ __('You have no active borrows.') }}
                        @elseif($status === 'overdue')
                            {{ __('You have no overdue books.') }}
                        @else
                            {{ __('You have not returned any books yet.') }}
                        @endif
                    </p>
                    <a href="{{ route('tenant.student.library.index') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-book me-2"></i>{{ __('Browse Library') }}
                    </a>
                </div>
            </div>
        @endif

        <!-- Important Notes -->
        <div class="alert alert-info mt-4">
            <h6 class="alert-heading">
                <i class="bi bi-info-circle me-2"></i>{{ __('Important Information') }}
            </h6>
            <ul class="mb-0 small">
                <li>{{ __('Books must be returned by the due date to avoid fines') }}</li>
                <li>{{ __('You can renew a book up to 2 times (7 days each renewal)') }}</li>
                <li>{{ __('Overdue books cannot be renewed') }}</li>
                <li>{{ __('If you have 3 or more overdue books, you cannot borrow new books') }}</li>
                <li>{{ __('Fines must be paid before you can borrow again') }}</li>
            </ul>
        </div>
    </div>
@endsection
