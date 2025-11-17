@extends('tenant.layouts.app')
@section('title', isset($feeStructure) ? 'Edit Fee Structure' : 'Create Fee Structure')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">{{ isset($feeStructure) ? 'Edit' : 'Create' }} Fee Structure</h1>
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($feeStructure) ? route('tenant.finance.fee-structures.update', $feeStructure) : route('tenant.finance.fee-structures.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($feeStructure))
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Name <span class="text-danger">*</span></label>
                            <input type="text" name="fee_name"
                                class="form-control @error('fee_name') is-invalid @enderror"
                                value="{{ old('fee_name', $feeStructure->fee_name ?? '') }}" required>
                            @error('fee_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach (['tuition' => 'Tuition', 'registration' => 'Registration', 'examination' => 'Examination', 'transport' => 'Transport', 'accommodation' => 'Accommodation', 'meals' => 'Meals', 'uniform' => 'Uniform', 'books' => 'Books', 'activity' => 'Activity', 'other' => 'Other'] as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('fee_type', $feeStructure->fee_type ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @error('fee_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount"
                                class="form-control @error('amount') is-invalid @enderror"
                                value="{{ old('amount', $feeStructure->amount ?? '') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year"
                                class="form-control @error('academic_year') is-invalid @enderror"
                                value="{{ old('academic_year', $feeStructure->academic_year ?? date('Y')) }}"
                                placeholder="2024" required>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Term</label>
                            <input type="text" name="term" class="form-control @error('term') is-invalid @enderror"
                                value="{{ old('term', $feeStructure->term ?? '') }}" placeholder="1, 2, 3">
                            @error('term')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date', isset($feeStructure) ? $feeStructure->due_date?->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $feeStructure->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                    value="1"
                                    {{ old('is_active', $feeStructure->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
                        <a href="{{ route('tenant.finance.fee-structures.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
