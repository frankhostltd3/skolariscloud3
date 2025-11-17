@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-book me-2"></i>Library Management</h1>
            <p class="text-muted mb-0">Manage library catalog, lending, and resources</p>
        </div>
        <div>
            <a href="{{ route('tenant.modules.library.transactions.borrow') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Issue Book
            </a>
            <a href="{{ route('tenant.modules.library.books.create') }}" class="btn btn-success">
                <i class="bi bi-book-half me-1"></i>Add Book
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-book-fill text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Books</h6>
                            <h3 class="mb-0">{{ number_format($totalBooks) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Available</h6>
                            <h3 class="mb-0">{{ number_format($availableBooks) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-arrow-right-circle-fill text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Currently Borrowed</h6>
                            <h3 class="mb-0">{{ number_format($borrowedBooks) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Overdue</h6>
                            <h3 class="mb-0">{{ number_format($overdueBooks) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Activities -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Borrows</h5>
                    <a href="{{ route('tenant.modules.library.transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Book</th>
                                    <th>Borrowed</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBorrows as $borrow)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $borrow->user->name }}</div>
                                                <small class="text-muted">{{ $borrow->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ Str::limit($borrow->book->title, 40) }}</div>
                                        <small class="text-muted">{{ $borrow->book->author }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $borrow->borrowed_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <small class="{{ $borrow->isOverdue() ? 'text-danger fw-semibold' : '' }}">
                                            {{ $borrow->due_date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($borrow->status === 'borrowed')
                                            @if($borrow->isOverdue())
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-info">Borrowed</span>
                                            @endif
                                        @elseif($borrow->status === 'returned')
                                            <span class="badge bg-success">Returned</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($borrow->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No recent borrowing activity
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Overdue Books -->
            @if($overdueTransactions->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger">
                    <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Overdue Books</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Book</th>
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                    <th>Fine</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueTransactions as $overdue)
                                <tr>
                                    <td>{{ $overdue->user->name }}</td>
                                    <td>{{ Str::limit($overdue->book->title, 40) }}</td>
                                    <td><span class="text-danger">{{ $overdue->due_date->format('M d, Y') }}</span></td>
                                    <td><span class="badge bg-danger">{{ $overdue->days_overdue }} days</span></td>
                                    <td>
                                        <span class="text-danger fw-semibold">
                                            {{ format_money($overdue->calculateFine()) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Popular Books -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i>Most Popular Books</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($popularBooks as $book)
                        <a href="{{ route('tenant.modules.library.books.show', $book) }}" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($book->title, 35) }}</h6>
                                    <small class="text-muted">{{ $book->author }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $book->transactions_count }}</span>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            <small>No popular books yet</small>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Category Distribution</h5>
                </div>
                <div class="card-body">
                    @forelse($categoryStats as $category)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $category->category }}</span>
                            <span class="text-muted">{{ $category->count }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ ($category->count / $totalBooks) * 100 }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        <small>No categories yet</small>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.modules.library.books.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul me-2"></i>View All Books
                        </a>
                        <a href="{{ route('tenant.modules.library.transactions.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-arrow-left-right me-2"></i>View Transactions
                        </a>
                        <a href="{{ route('tenant.modules.library.transactions.borrow') }}" class="btn btn-outline-success">
                            <i class="bi bi-plus-circle me-2"></i>Issue New Book
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
