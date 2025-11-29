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
                        <div class="col-12 mb-3">
                            <label class="form-label">Fee Structures <span class="text-danger">*</span></label>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="selectAllFees"
                                    onclick="toggleAllFees(this)">
                                <label class="form-check-label fw-bold" for="selectAllFees">
                                    Select All Fees
                                </label>
                            </div>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach ($feeStructures as $fee)
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="fee_structure_ids[]" value="{{ $fee->id }}"
                                            class="form-check-input fee-checkbox" id="fee_{{ $fee->id }}"
                                            data-amount="{{ $fee->amount }}"
                                            {{ old('fee_structure_ids') && in_array($fee->id, old('fee_structure_ids')) ? 'checked' : '' }}
                                            onchange="updateTotal()">
                                        <label class="form-check-label d-flex justify-content-between w-100"
                                            for="fee_{{ $fee->id }}">
                                            <span>{{ $fee->fee_name }}</span>
                                            <span class="text-muted">{{ formatMoney($fee->amount) }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="alert alert-info mt-2 mb-0">
                                <strong>Total Amount:</strong> <span id="totalAmount">{{ formatMoney(0) }}</span>
                            </div>
                            @error('fee_structure_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('fee_structure_ids.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date"
                                class="form-control @error('issue_date') is-invalid @enderror"
                                value="{{ old('issue_date', isset($invoice) ? $invoice->issue_date->format('Y-m-d') : date('Y-m-d')) }}"
                                required>
                            @error('issue_date')
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year"
                                class="form-control @error('academic_year') is-invalid @enderror"
                                value="{{ old('academic_year', isset($invoice) ? $invoice->academic_year : setting('current_academic_year', date('Y'))) }}"
                                placeholder="e.g., 2024/2025" required>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Term</label>
                            <select name="term" class="form-select @error('term') is-invalid @enderror">
                                <option value="">Select Term (Optional)</option>
                                <option value="Term 1"
                                    {{ old('term', $invoice->term ?? '') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                <option value="Term 2"
                                    {{ old('term', $invoice->term ?? '') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                <option value="Term 3"
                                    {{ old('term', $invoice->term ?? '') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                            </select>
                            @error('term')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="5">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Only</button>
                        <div class="btn-group">
                            <button type="submit" name="send_invoice" value="1" class="btn btn-success">
                                <i class="bi bi-send me-1"></i> Save & Send
                            </button>
                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown">
                                <span class="visually-hidden">Send options</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button type="submit" name="send_invoice" value="1" class="dropdown-item"
                                        onclick="document.querySelector('input[name=send_to]').value='student'">
                                        <i class="bi bi-person me-2"></i> Save & Send to Student
                                    </button>
                                </li>
                                <li>
                                    <button type="submit" name="send_invoice" value="1" class="dropdown-item"
                                        onclick="document.querySelector('input[name=send_to]').value='parent'">
                                        <i class="bi bi-people me-2"></i> Save & Send to Parent(s)
                                    </button>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <button type="submit" name="send_invoice" value="1" class="dropdown-item"
                                        onclick="document.querySelector('input[name=send_to]').value='both'">
                                        <i class="bi bi-send me-2"></i> Save & Send to Both
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <input type="hidden" name="send_to" value="both">
                        <a href="{{ route('tenant.finance.invoices.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>
    <script>
        function toggleAllFees(checkbox) {
            const feeCheckboxes = document.querySelectorAll('.fee-checkbox');
            feeCheckboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            const checkedBoxes = document.querySelectorAll('.fee-checkbox:checked');
            checkedBoxes.forEach(cb => {
                total += parseFloat(cb.dataset.amount);
            });

            // Format the total amount
            const formatted = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: '{{ currentCurrency()->code ?? 'UGX' }}'
            }).format(total);

            document.getElementById('totalAmount').textContent = formatted;
        }

        // Initialize Summernote WYSIWYG editor
        $(document).ready(function() {
            $('#notes').summernote({
                height: 150,
                placeholder: 'Enter additional notes or payment instructions...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['codeview', 'help']]
                ]
            });

            updateTotal();
        });
    </script>
@endpush
