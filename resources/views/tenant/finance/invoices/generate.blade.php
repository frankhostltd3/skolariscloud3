@extends('tenant.layouts.admin')

@section('title', 'Generate Term Invoices')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Generate Term Invoices</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('tenant.finance.invoices.store-term') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="academic_year" class="form-label">Academic Year</label>
                                        <select name="academic_year" id="academic_year" class="form-select" required>
                                            <option value="">Select Academic Year</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year->year }}"
                                                    {{ old('academic_year', $currentYear) == $year->year ? 'selected' : '' }}>
                                                    {{ $year->year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="term" class="form-label">Term</label>
                                        <select name="term" id="term" class="form-select" required>
                                            <option value="">Select Term</option>
                                            <option value="Term 1"
                                                {{ old('term', $currentTerm) == 'Term 1' ? 'selected' : '' }}>Term 1
                                            </option>
                                            <option value="Term 2"
                                                {{ old('term', $currentTerm) == 'Term 2' ? 'selected' : '' }}>Term 2
                                            </option>
                                            <option value="Term 3"
                                                {{ old('term', $currentTerm) == 'Term 3' ? 'selected' : '' }}>Term 3
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" required
                                    value="{{ old('due_date') }}">
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> This will generate invoices for all active students based
                                on the active fee structures for the selected academic year and term.
                                Existing invoices for the same student and fee structure will be skipped.
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-gear"></i> Generate Invoices
                            </button>
                            <a href="{{ route('tenant.finance.invoices.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
