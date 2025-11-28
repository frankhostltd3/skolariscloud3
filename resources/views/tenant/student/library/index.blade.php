@extends('layouts.tenant.student')

@section('title', 'Library')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="bi bi-book me-2"></i>{{ __('School Library') }}
            </h4>
            <a href="{{ route('tenant.student.library.my-borrows') }}" class="btn btn-primary">
                <i class="bi bi-list-check me-2"></i>{{ __('My Borrows') }}
                @if ($statistics['my_borrows'] > 0)
                    <span class="badge bg-white text-primary ms-1">{{ $statistics['my_borrows'] }}</span>
                @endif
            </a>
        </div>

        <!-- Success/Error Messages -->
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
            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('Total Books') }}</h6>
                                <h4 class="mb-0">{{ $statistics['total'] }}</h4>
                            </div>
                            <div class="text-primary" style="font-size: 1.5rem;">
                                <i class="bi bi-book"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('Available') }}</h6>
                                <h4 class="mb-0 text-success">{{ $statistics['available'] }}</h4>
                            </div>
                            <div class="text-success" style="font-size: 1.5rem;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('My Borrows') }}</h6>
                                <h4 class="mb-0 text-info">{{ $statistics['my_borrows'] }}</h4>
                            </div>
                            <div class="text-info" style="font-size: 1.5rem;">
                                <i class="bi bi-bookmark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 small">{{ __('Overdue') }}</h6>
                                <h4 class="mb-0 {{ $statistics['overdue'] > 0 ? 'text-danger' : '' }}">
                                    {{ $statistics['overdue'] }}
                                </h4>
                            </div>
                            <div class="{{ $statistics['overdue'] > 0 ? 'text-danger' : 'text-secondary' }}"
                                style="font-size: 1.5rem;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Borrows Alert -->
        @if ($myBorrows->count() > 0)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle me-2"></i>{{ __('You currently have') }} {{ $myBorrows->count() }}
                    {{ __('book(s) borrowed') }}
                </h6>
                <ul class="mb-0 small">
                    @foreach ($myBorrows->take(3) as $borrow)
                        <li>
                            <strong>{{ $borrow->book->title }}</strong> -
                            {{ __('Due:') }} {{ $borrow->due_date->format('M d, Y') }}
                            @if ($borrow->isOverdue())
                                <span class="badge bg-danger ms-1">{{ __('Overdue') }}</span>
                            @endif
                        </li>
                    @endforeach
                    @if ($myBorrows->count() > 3)
                        <li class="mt-1">
                            <a href="{{ route('tenant.student.library.my-borrows') }}">
                                {{ __('View all borrows') }} &raquo;
                            </a>
                        </li>
                    @endif
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('tenant.student.library.index') }}" class="row g-2">
                    <div class="col-12 col-md-5">
                        <label class="form-label small mb-1">{{ __('Search') }}</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="{{ __('Search by title, author, ISBN...') }}" value="{{ $search }}">
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-1">{{ __('Category') }}</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">{{ __('Availability') }}</label>
                        <select name="availability" class="form-select form-select-sm">
                            <option value="">{{ __('All') }}</option>
                            <option value="available" {{ $availability == 'available' ? 'selected' : '' }}>
                                {{ __('Available') }}
                            </option>
                            <option value="borrowed" {{ $availability == 'borrowed' ? 'selected' : '' }}>
                                {{ __('Borrowed') }}
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>{{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Books Grid -->
        @if ($books->count() > 0)
            <div class="row">
                @foreach ($books as $book)
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <!-- Book Cover -->
                            <div class="position-relative">
                                @if ($book->cover_image_url)
                                    <img src="{{ $book->cover_image_url }}" class="card-img-top"
                                        alt="{{ $book->title }}" style="height: 180px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="height: 180px;">
                                        <i class="bi bi-book text-muted" style="font-size: 2.5rem;"></i>
                                    </div>
                                @endif

                                <!-- Availability Badge -->
                                <div class="position-absolute top-0 end-0 m-1">
                                    @if ($book->isAvailable())
                                        <span class="badge bg-success badge-sm">{{ __('Available') }}</span>
                                    @else
                                        <span class="badge bg-danger badge-sm">{{ __('Borrowed') }}</span>
                                    @endif
                                </div>

                                <!-- Category Badge -->
                                @if ($book->category)
                                    <div class="position-absolute top-0 start-0 m-1">
                                        <span class="badge bg-primary badge-sm">{{ $book->category }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body p-2 d-flex flex-column">
                                <!-- Book Title -->
                                <h6 class="card-title mb-1 small">
                                    <a href="{{ route('tenant.student.library.show', $book->id) }}"
                                        class="text-decoration-none text-dark">
                                        {{ Str::limit($book->title, 35) }}
                                    </a>
                                </h6>

                                <!-- Author -->
                                <p class="text-muted mb-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-person me-1"></i>{{ Str::limit($book->author ?? 'Unknown', 25) }}
                                </p>

                                <!-- Book Details -->
                                <div class="mt-auto">
                                    <div class="row g-1 mb-2" style="font-size: 0.7rem;">
                                        <div class="col-6 text-muted">
                                            <i class="bi bi-stack me-1"></i>{{ $book->quantity }}
                                        </div>
                                        <div class="col-6 text-success">
                                            <i class="bi bi-check-circle me-1"></i>{{ $book->available_quantity }}
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-1">
                                        <a href="{{ route('tenant.student.library.show', $book->id) }}"
                                            class="btn btn-outline-primary btn-sm"
                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <i class="bi bi-eye me-1"></i>{{ __('View') }}
                                        </a>

                                        @if ($book->isAvailable())
                                            <form method="POST"
                                                action="{{ route('tenant.student.library.borrow', $book->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100"
                                                    style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                    <i class="bi bi-bookmark-plus me-1"></i>{{ __('Borrow') }}
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled
                                                style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                <i class="bi bi-x-circle me-1"></i>{{ __('N/A') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if (method_exists($books, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $books->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('No Books Found') }}</h5>
                    <p class="text-muted">
                        @if ($search || $category || $availability)
                            {{ __('Try adjusting your search criteria.') }}
                        @else
                            {{ __('The library is currently empty.') }}
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- Library Guidelines -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-info bg-opacity-10 border-0 py-2">
                <h6 class="mb-0 small">
                    <i class="bi bi-info-circle me-2"></i>{{ __('Library Guidelines') }}
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-1 small">{{ __('Borrowing Rules:') }}</h6>
                        <ul class="mb-2" style="font-size: 0.8rem;">
                            <li>{{ __('Borrowing period is 14 days') }}</li>
                            <li>{{ __('Maximum of 2 renewals per book') }}</li>
                            <li>{{ __('Each renewal extends the period by 7 days') }}</li>
                            <li>{{ __('Cannot borrow if you have 3 or more overdue books') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-1 small">{{ __('Important Notes:') }}</h6>
                        <ul class="mb-2" style="font-size: 0.8rem;">
                            <li>{{ __('Return books on time to avoid fines') }}</li>
                            <li>{{ __('Take good care of borrowed books') }}</li>
                            <li>{{ __('Report any damage immediately') }}</li>
                            <li>{{ __('Check "My Borrows" to track due dates') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection
