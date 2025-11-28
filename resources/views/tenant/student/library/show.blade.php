@extends('layouts.tenant.student')

@section('title', $book->title)

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('tenant.student.library.index') }}">{{ __('Library') }}</a></li>
                <li class="breadcrumb-item active">{{ $book->title }}</li>
            </ol>
        </nav>

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

        <div class="row">
            <!-- Left Column - Book Details -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row">
                            <!-- Book Cover -->
                            <div class="col-md-4 mb-3">
                                @if ($book->cover_image_url)
                                    <img src="{{ $book->cover_image_url }}" class="img-fluid rounded shadow-sm"
                                        alt="{{ $book->title }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                        style="height: 400px;">
                                        <i class="bi bi-book text-muted" style="font-size: 6rem;"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Book Information -->
                            <div class="col-md-8">
                                <h3 class="mb-3">{{ $book->title }}</h3>

                                <!-- Availability Status -->
                                <div class="mb-3">
                                    @if ($book->isAvailable())
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle me-1"></i>{{ __('Available') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-x-circle me-1"></i>{{ __('All Copies Borrowed') }}
                                        </span>
                                    @endif

                                    @if ($book->category)
                                        <span class="badge bg-primary fs-6 ms-2">{{ $book->category }}</span>
                                    @endif
                                </div>

                                <!-- Book Details Table -->
                                <table class="table table-sm">
                                    <tr>
                                        <th width="35%"><i class="bi bi-person me-2"></i>{{ __('Author') }}</th>
                                        <td>{{ $book->author ?? 'N/A' }}</td>
                                    </tr>
                                    @if ($book->isbn)
                                        <tr>
                                            <th><i class="bi bi-upc me-2"></i>{{ __('ISBN') }}</th>
                                            <td>{{ $book->isbn }}</td>
                                        </tr>
                                    @endif
                                    @if ($book->publisher)
                                        <tr>
                                            <th><i class="bi bi-building me-2"></i>{{ __('Publisher') }}</th>
                                            <td>{{ $book->publisher }}</td>
                                        </tr>
                                    @endif
                                    @if ($book->publication_year)
                                        <tr>
                                            <th><i class="bi bi-calendar me-2"></i>{{ __('Year') }}</th>
                                            <td>{{ $book->publication_year }}</td>
                                        </tr>
                                    @endif
                                    @if ($book->language)
                                        <tr>
                                            <th><i class="bi bi-translate me-2"></i>{{ __('Language') }}</th>
                                            <td>{{ $book->language }}</td>
                                        </tr>
                                    @endif
                                    @if ($book->pages)
                                        <tr>
                                            <th><i class="bi bi-file-text me-2"></i>{{ __('Pages') }}</th>
                                            <td>{{ $book->pages }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th><i class="bi bi-stack me-2"></i>{{ __('Total Copies') }}</th>
                                        <td>{{ $book->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="bi bi-check-circle me-2 text-success"></i>{{ __('Available') }}</th>
                                        <td><strong class="text-success">{{ $book->available_quantity }}</strong></td>
                                    </tr>
                                    @if ($book->location)
                                        <tr>
                                            <th><i class="bi bi-geo-alt me-2"></i>{{ __('Location') }}</th>
                                            <td>{{ $book->location }}</td>
                                        </tr>
                                    @endif
                                </table>

                                <!-- Borrow Button -->
                                <div class="mt-4">
                                    @if ($myActiveBorrow)
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            {{ __('You currently have this book borrowed.') }}<br>
                                            <strong>{{ __('Due Date:') }}</strong>
                                            {{ $myActiveBorrow->due_date->format('M d, Y') }}
                                            @if ($myActiveBorrow->isOverdue())
                                                <span class="badge bg-danger ms-2">{{ __('Overdue') }}</span>
                                            @endif
                                        </div>
                                        @if ($myActiveBorrow->renewal_count < 2 && !$myActiveBorrow->isOverdue())
                                            <form method="POST"
                                                action="{{ route('tenant.student.library.extend', $myActiveBorrow->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="bi bi-arrow-repeat me-2"></i>
                                                    {{ __('Request Extension') }}
                                                    <small>({{ 2 - $myActiveBorrow->renewal_count }}
                                                        {{ __('left') }})</small>
                                                </button>
                                            </form>
                                        @endif
                                    @elseif($book->isAvailable())
                                        <form method="POST"
                                            action="{{ route('tenant.student.library.borrow', $book->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="bi bi-bookmark-plus me-2"></i>{{ __('Borrow This Book') }}
                                            </button>
                                        </form>
                                        <p class="small text-muted mt-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            {{ __('Borrowing period: 14 days') }}
                                        </p>
                                    @else
                                        <button class="btn btn-secondary btn-lg" disabled>
                                            <i class="bi bi-x-circle me-2"></i>{{ __('Not Available') }}
                                        </button>
                                        <p class="small text-muted mt-2">
                                            {{ __('All copies are currently borrowed. Please check back later.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if ($book->description)
                            <hr class="my-4">
                            <h5 class="mb-3">{{ __('Description') }}</h5>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{!! nl2br(e($book->description)) !!}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Additional Info -->
            <div class="col-lg-4">
                <!-- Availability Stats -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">{{ __('Availability') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if ($book->isAvailable())
                                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-2 text-success">{{ __('Available') }}</h5>
                            @else
                                <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                                <h5 class="mt-2 text-danger">{{ __('All Borrowed') }}</h5>
                            @endif
                        </div>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($book->available_quantity / max($book->quantity, 1)) * 100 }}%">
                                {{ $book->available_quantity }} / {{ $book->quantity }}
                            </div>
                        </div>
                        <p class="small text-muted mb-0 text-center">
                            {{ __('Available Copies') }}
                        </p>
                    </div>
                </div>

                <!-- My Borrow Status -->
                @if ($myActiveBorrow)
                    <div class="card shadow-sm border-info mb-3">
                        <div class="card-header bg-info bg-opacity-10">
                            <h6 class="mb-0">{{ __('Your Borrow Status') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>{{ __('Borrowed:') }}</strong><br>
                                {{ $myActiveBorrow->borrowed_at->format('M d, Y') }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ __('Due Date:') }}</strong><br>
                                {{ $myActiveBorrow->due_date->format('M d, Y') }}
                                @if ($myActiveBorrow->isOverdue())
                                    <span class="badge bg-danger">{{ __('Overdue') }}</span>
                                @else
                                    <span class="badge bg-success">{{ $myActiveBorrow->due_date->diffInDays(now()) }}
                                        {{ __('days left') }}</span>
                                @endif
                            </p>
                            @if ($myActiveBorrow->renewal_count > 0)
                                <p class="mb-0">
                                    <strong>{{ __('Renewals:') }}</strong> {{ $myActiveBorrow->renewal_count }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Borrowing Guidelines -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>{{ __('Guidelines') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>{{ __('14-day borrowing period') }}</li>
                            <li>{{ __('Up to 2 renewals (7 days each)') }}</li>
                            <li>{{ __('Return on time to avoid fines') }}</li>
                            <li>{{ __('Take good care of the book') }}</li>
                            <li>{{ __('Report damage immediately') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
