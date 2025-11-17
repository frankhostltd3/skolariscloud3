@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1"><i class="bi bi-plus-circle me-2"></i>Issue Book</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.index') }}">Library</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tenant.modules.library.transactions.index') }}">Transactions</a></li>
                        <li class="breadcrumb-item active">Issue Book</li>
                    </ol>
                </nav>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('tenant.modules.library.transactions.store') }}" method="POST">
                        @csrf

                        <!-- User Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select User <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Choose user...</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select the person borrowing the book</small>
                        </div>

                        <!-- Book Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Book <span class="text-danger">*</span></label>
                            <select name="library_book_id" class="form-select @error('library_book_id') is-invalid @enderror" required>
                                <option value="">Choose book...</option>
                                @foreach($availableBooks as $book)
                                <option value="{{ $book->id }}" {{ old('library_book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->title }} by {{ $book->author }} 
                                    (Available: {{ $book->available_quantity }})
                                </option>
                                @endforeach
                            </select>
                            @error('library_book_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Only available books are shown</small>
                        </div>

                        <!-- Due Days -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Borrowing Period (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="due_days" class="form-control @error('due_days') is-invalid @enderror" 
                                   value="{{ old('due_days', 14) }}" min="1" max="90" required>
                            @error('due_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Default: 14 days (Maximum: 90 days)</small>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Notes (Optional)</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="Any special conditions or notes about this borrowing...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tenant.modules.library.transactions.index') }}" class="btn btn-light">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Issue Book
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm mt-4 bg-info bg-opacity-10">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2"></i>Borrowing Policy</h6>
                    <ul class="mb-0 small">
                        <li>Standard borrowing period is 14 days</li>
                        <li>Books can be renewed if no one is waiting</li>
                        <li>Overdue fines: $1.00 per day</li>
                        <li>Maximum borrowing period: 90 days</li>
                        <li>Lost books must be reported immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
