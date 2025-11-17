@extends('tenant.layouts.app')

@section('title', 'Classes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Classes</h1>
        <p class="text-muted mb-0">Manage all classes for this school.</p>
    </div>
    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> New Class
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Filters</h6>
    </div>
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('admin.classes.index') }}">
            <div class="col-md-4">
                <label class="form-label">Education Level</label>
                <select name="level_id" class="form-select">
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" @selected(($filters['level_id'] ?? null) == $level->id)>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Stream</label>
                <select name="stream_id" class="form-select">
                    <option value="">All Streams</option>
                    @foreach($streams as $stream)
                        <option value="{{ $stream->id }}" @selected(($filters['stream_id'] ?? null) == $stream->id)>
                            {{ $stream->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @php $status = $filters['status'] ?? 'active'; @endphp
                    <option value="all" @selected($status === 'all')>All</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-primary me-2" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Classes ({{ $classes->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Education Level</th>
                    <th>Stream</th>
                    <th>Capacity</th>
                    <th>Room</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($classes as $class)
                <tr>
                    <td>{{ $class->name }}</td>
                    <td>
                        <span class="badge bg-light text-dark">
                            {{ optional($class->educationLevel)->name ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @if($class->stream)
                            <span class="badge bg-info-subtle text-info">
                                {{ $class->stream->name }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $class->capacity ?? '—' }}</td>
                    <td>{{ $class->room_number ?? '—' }}</td>
                    <td>
                        @if($class->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this class?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No classes found. <a href="{{ route('admin.classes.create') }}">Create the first class</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($classes->hasPages())
        <div class="card-footer">
            {{ $classes->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
