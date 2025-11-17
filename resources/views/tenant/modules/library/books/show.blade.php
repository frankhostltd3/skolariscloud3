@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-book me-2"></i>{{ $book->title }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.index') }}">Library</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.books.index') }}">Books</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('tenant.modules.library.books.edit', $book) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('tenant.modules.library.books.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Book Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">TITLE</h6>
                            <p class="fw-semibold mb-0">{{ $book->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">AUTHOR</h6>
                            <p class="fw-semibold mb-0">{{ $book->author }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">ISBN</h6>
                            <p class="mb-0">{{ $book->isbn ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">CATEGORY</h6>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $book->category }}</span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">PUBLISHER</h6>
                            <p class="mb-0">{{ $book->publisher ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-1">PUBLICATION YEAR</h6>
                            <p class="mb-0">{{ $book->publication_year ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small mb-1">LANGUAGE</h6>
                            <p class="mb-0">{{ $book->language }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small mb-1">PAGES</h6>
                            <p class="mb-0">{{ $book->pages ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted small mb-1">LOCATION</h6>
                            <p class="mb-0">{{ $book->location ?? 'Not specified' }}</p>
                        </div>
                        @if($book->description)
                        <div class="col-md-12">
                            <h6 class="text-muted small mb-1">DESCRIPTION</h6>
                            <p class="mb-0">{{ $book->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Borrowing History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Borrowed Date</th>
                                    <th>Due Date</th>
                                    <th>Returned Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($book->transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->borrowed_at->format('M d, Y') }}</td>
                                    <td>{{ $transaction->due_date->format('M d, Y') }}</td>
                                    <td>{{ $transaction->returned_at ? $transaction->returned_at->format('M d, Y') : '-' }}</td>
                                    <td>
                                        @if($transaction->status === 'borrowed')
                                            @if($transaction->isOverdue())
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-info">Borrowed</span>
                                            @endif
                                        @elseif($transaction->status === 'returned')
                                            <span class="badge bg-success">Returned</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No borrowing history yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Book Status</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Status</span>
                            @if($book->status === 'available')
                            <span class="badge bg-success">Available</span>
                            @elseif($book->status === 'maintenance')
                            <span class="badge bg-warning">Maintenance</span>
                            @else
                            <span class="badge bg-danger">{{ ucfirst($book->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Copies</span>
                            <strong>{{ $book->quantity }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Available</span>
                            <strong class="text-{{ $book->available_quantity > 0 ? 'success' : 'danger' }}">
                                {{ $book->available_quantity }}
                            </strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Currently Borrowed</span>
                            <strong>{{ $book->quantity - $book->available_quantity }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted small mb-1">BORROWING RATE</h6>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $book->borrowing_rate }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($book->borrowing_rate, 1) }}%</small>
                    </div>
                    @if($book->purchase_price)
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Purchase Price</span>
                            <strong>{{ format_money($book->purchase_price) }}</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Statistics</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Borrows</span>
                            <strong>{{ $book->transactions->count() }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Active Borrows</span>
                            <strong>{{ $book->activeBorrows->count() }}</strong>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Added On</span>
                            <small>{{ $book->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
