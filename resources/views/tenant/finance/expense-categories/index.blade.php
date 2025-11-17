@extends('tenant.layouts.app')

@section('title', 'Expense Categories')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Expense Categories</h1>
            <a href="{{ route('tenant.finance.expense-categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add Category
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search categories..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>
                            Search</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('tenant.finance.expense-categories.index') }}" class="btn btn-secondary w-100"><i
                                class="bi bi-x-circle me-1"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card">
            <div class="card-body">
                @if ($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Parent</th>
                                    <th>Budget Limit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>
                                            <i class="{{ $category->icon }} me-2" style="color: {{ $category->color }}"></i>
                                            {{ $category->name }}
                                        </td>
                                        <td>{{ $category->code ?? '-' }}</td>
                                        <td>{{ $category->parent?->name ?? '-' }}</td>
                                        <td>{{ $category->budget_limit ? formatMoney($category->budget_limit) : '-' }}</td>
                                        <td><span
                                                class="badge {{ $category->status_badge_class }}">{{ $category->status_text }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tenant.finance.expense-categories.show', $category) }}"
                                                class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('tenant.finance.expense-categories.edit', $category) }}"
                                                class="btn btn-sm btn-warning" title="Edit"><i
                                                    class="bi bi-pencil"></i></a>
                                            <form
                                                action="{{ route('tenant.finance.expense-categories.destroy', $category) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i
                                                        class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $categories->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i> No expense categories found. <a
                            href="{{ route('tenant.finance.expense-categories.create') }}">Add one now</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
