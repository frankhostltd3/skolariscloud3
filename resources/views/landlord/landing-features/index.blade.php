@extends('landlord.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Landing Page Features</h1>
            <a href="{{ route('landlord.landing-features.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Feature
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Icon</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($features as $feature)
                                <tr>
                                    <td>{{ $feature->sort_order }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                                            style="width: 40px; height: 40px; background-color: {{ $feature->icon_bg_color }};">
                                            <i class="bi {{ $feature->icon }}"
                                                style="color: {{ str_starts_with($feature->icon_color, 'var') ? $feature->icon_color : '' }}"
                                                class="{{ !str_starts_with($feature->icon_color, 'var') ? $feature->icon_color : '' }}"></i>
                                        </div>
                                    </td>
                                    <td><strong>{{ $feature->title }}</strong></td>
                                    <td>{{ Str::limit($feature->description, 50) }}</td>
                                    <td>
                                        @if ($feature->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('landlord.landing-features.edit', $feature) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('landlord.landing-features.destroy', $feature) }}"
                                            method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No features found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
