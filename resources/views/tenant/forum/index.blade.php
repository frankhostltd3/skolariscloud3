@extends('tenant.layouts.app')

@section('title', 'Discussion Forum')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 mb-0 text-gray-800">Discussion Forum</h1>
                <p class="text-muted">Connect, share, and learn with your school community.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('tenant.forum.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Start Discussion
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filters</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('tenant.forum.index') }}"
                            class="list-group-item list-group-item-action {{ !request('filter') ? 'active' : '' }}">
                            All Discussions
                        </a>
                        <a href="{{ route('tenant.forum.index', ['filter' => 'my_classes']) }}"
                            class="list-group-item list-group-item-action {{ request('filter') == 'my_classes' ? 'active' : '' }}">
                            My Classes
                        </a>
                        <a href="{{ route('tenant.forum.index', ['filter' => 'my_subjects']) }}"
                            class="list-group-item list-group-item-action {{ request('filter') == 'my_subjects' ? 'active' : '' }}">
                            My Subjects
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                @if ($threads->count() > 0)
                    <div class="card">
                        <div class="list-group list-group-flush">
                            @foreach ($threads as $thread)
                                <div class="list-group-item p-3">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="mb-1">
                                                @if ($thread->is_pinned)
                                                    <span class="badge bg-warning text-dark me-2"><i
                                                            class="bi bi-pin-angle-fill"></i> Pinned</span>
                                                @endif
                                                @if ($thread->context_type)
                                                    <span
                                                        class="badge bg-info me-2">{{ ucfirst($thread->context_type) }}</span>
                                                @endif
                                                <a href="{{ route('tenant.forum.show', $thread->slug) }}"
                                                    class="text-decoration-none fw-bold text-dark fs-5">
                                                    {{ $thread->title }}
                                                </a>
                                            </div>
                                            <p class="mb-1 text-muted text-truncate" style="max-width: 600px;">
                                                {{ Str::limit(strip_tags($thread->content), 150) }}
                                            </p>
                                            <small class="text-muted">
                                                Posted by <strong>{{ $thread->author->name }}</strong>
                                                &bull; {{ $thread->created_at->diffForHumans() }}
                                                &bull; <i class="bi bi-eye"></i> {{ $thread->views_count }} views
                                                &bull; <i class="bi bi-chat-dots"></i> {{ $thread->posts->count() }}
                                                replies
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <a href="{{ route('tenant.forum.show', $thread->slug) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4">
                        {{ $threads->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        No discussions found. Be the first to start one!
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
