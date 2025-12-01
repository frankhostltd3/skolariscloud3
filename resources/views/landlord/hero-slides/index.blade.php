@extends('landlord.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Hero Slides</h1>
            <a href="{{ route('landlord.hero-slides.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Slide
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slides as $slide)
                                <tr>
                                    <td>{{ $slide->sort_order }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Slide Image"
                                            style="height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $slide->title ?? 'No Title' }}</strong><br>
                                        <small class="text-muted">{{ $slide->subtitle }}</small>
                                    </td>
                                    <td>
                                        @if ($slide->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('landlord.hero-slides.edit', $slide) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('landlord.hero-slides.destroy', $slide) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
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
                                    <td colspan="5" class="text-center">No slides found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
