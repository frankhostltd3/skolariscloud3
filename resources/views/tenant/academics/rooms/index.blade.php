@extends('tenant.layouts.app')

@section('title', 'Rooms')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Rooms</h1>
            <a href="{{ route('tenant.academics.rooms.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Room
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Rooms</h6>
                <div class="dropdown no-arrow">
                    <form action="{{ route('tenant.academics.rooms.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                            placeholder="Search rooms..." value="{{ request('search') }}">
                        <select name="type" class="form-select form-select-sm me-2" style="width: 150px;">
                            <option value="">All Types</option>
                            @foreach (['Classroom', 'Laboratory', 'Hall', 'Library', 'Computer Lab', 'Staff Room', 'Other'] as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if ($rooms->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rooms as $room)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tenant.academics.rooms.show', $room) }}"
                                                class="text-decoration-none fw-bold">
                                                {{ $room->name }}
                                            </a>
                                        </td>
                                        <td>{{ $room->code ?? '-' }}</td>
                                        <td>{{ $room->type ?? '-' }}</td>
                                        <td>{{ $room->capacity ?? '-' }}</td>
                                        <td>
                                            @if ($room->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('tenant.academics.rooms.edit', $room) }}"
                                                    class="btn btn-sm btn-info text-white" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('tenant.academics.rooms.destroy', $room) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this room?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $rooms->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-door-open display-1 text-gray-300"></i>
                        <p class="mt-3 mb-0 text-gray-500">No rooms found.</p>
                        <a href="{{ route('tenant.academics.rooms.create') }}" class="btn btn-link">Create your first
                            room</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
