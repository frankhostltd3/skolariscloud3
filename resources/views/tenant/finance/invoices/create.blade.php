@extends('tenant.layouts.app')
@section('title', 'Create Invoice')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">{{ isset($invoice) ? 'Edit' : 'Create' }} Invoice</h1>
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($invoice) ? route('tenant.finance.invoices.update', $invoice) : route('tenant.finance.invoices.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($invoice))
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror"
                                required>
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('student_id', $invoice->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Structure <span class="text-danger">*</span></label>
                            <select name="fee_structure_id"
                                class="form-select @error('fee_structure_id') is-invalid @enderror" required>
                                <option value="">Select Fee</option>
                                @foreach ($feeStructures as $fee)
                                    <option value="{{ $fee->id }}"
                                        {{ old('fee_structure_id', $invoice->fee_structure_id ?? '') == $fee->id ? 'selected' : '' }}>
                                        {{ $fee->fee_name }} - {{ formatMoney($fee->amount) }}</option>
                                @endforeach
                            </select>
                            @error('fee_structure_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date"
                                class="form-control @error('invoice_date') is-invalid @enderror"
                                value="{{ old('invoice_date', isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')) }}"
                                required>
                            @error('invoice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date', isset($invoice) ? $invoice->due_date->format('Y-m-d') : '') }}"
                                required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
                        <a href="{{ route('tenant.finance.invoices.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
