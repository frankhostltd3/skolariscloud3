@extends('tenant.layouts.app')

@section('title', isset($expenseCategory) ? 'Edit Expense Category' : 'Create Expense Category')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ isset($expenseCategory) ? 'Edit' : 'Create' }} Expense Category</h1>
            <a href="{{ route('tenant.finance.expense-categories.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST"
                            action="{{ isset($expenseCategory) ? route('tenant.finance.expense-categories.update', $expenseCategory) : route('tenant.finance.expense-categories.store') }}">
                            @csrf
                            @if (isset($expenseCategory))
                                @method('PUT')
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name"
                                        value="{{ old('name', $expenseCategory->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="code" class="form-label">Code</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                        id="code" name="code"
                                        value="{{ old('code', $expenseCategory->code ?? '') }}">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional unique identifier</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color"
                                        class="form-control form-control-color @error('color') is-invalid @enderror"
                                        id="color" name="color"
                                        value="{{ old('color', $expenseCategory->color ?? '#6c757d') }}">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="icon" class="form-label">Icon (Bootstrap Icon)</label>
                                    <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                        id="icon" name="icon"
                                        value="{{ old('icon', $expenseCategory->icon ?? 'bi-receipt') }}"
                                        placeholder="bi-receipt">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">e.g., bi-receipt, bi-cash, bi-credit-card</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="parent_id" class="form-label">Parent Category</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id"
                                        name="parent_id">
                                        <option value="">None (Top Level)</option>
                                        @foreach ($categories ?? [] as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ old('parent_id', $expenseCategory->parent_id ?? '') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="budget_limit" class="form-label">Budget Limit</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('budget_limit') is-invalid @enderror" id="budget_limit"
                                        name="budget_limit"
                                        value="{{ old('budget_limit', $expenseCategory->budget_limit ?? '') }}">
                                    @error('budget_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional monthly budget limit</small>
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3">{{ old('description', $expenseCategory->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            {{ old('is_active', $expenseCategory->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> {{ isset($expenseCategory) ? 'Update' : 'Create' }}
                                        Category
                                    </button>
                                    <a href="{{ route('tenant.finance.expense-categories.index') }}"
                                        class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
