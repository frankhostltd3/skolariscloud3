@extends('layouts.tenant.parent')

@section('title', __('Announcements'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Announcements') }}</h4>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($announcements as $announcement)
                            <div class="list-group-item p-4">
                                <div class="d-flex w-100 justify-content-between mb-2">
                                    <h5 class="mb-1 fw-bold">{{ $announcement->title }}</h5>
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-muted">{{ Str::limit($announcement->content, 200) }}</p>
                                <div class="mt-2">
                                    <span
                                        class="badge bg-light text-dark border">{{ $announcement->type ?? 'General' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-megaphone text-muted fs-1 mb-3"></i>
                                <p class="text-muted mb-0">{{ __('No announcements at this time.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if ($announcements->hasPages())
                    <div class="card-footer bg-white py-3">
                        {{ $announcements->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
