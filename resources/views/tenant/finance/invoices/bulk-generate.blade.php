@extends('tenant.layouts.app')
@section('title', 'Bulk Generate Invoices')
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Bulk Generate Invoices</h1>

        <!-- Generation Tabs -->
        <ul class="nav nav-tabs mb-4" id="generationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="class-tab" data-bs-toggle="tab" data-bs-target="#class-generation"
                    type="button" role="tab">
                    <i class="bi bi-mortarboard me-2"></i> By Class
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stream-tab" data-bs-toggle="tab" data-bs-target="#stream-generation"
                    type="button" role="tab">
                    <i class="bi bi-diagram-3 me-2"></i> By Stream
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="school-tab" data-bs-toggle="tab" data-bs-target="#school-generation"
                    type="button" role="tab">
                    <i class="bi bi-building me-2"></i> Entire School
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students-generation"
                    type="button" role="tab">
                    <i class="bi bi-people me-2"></i> Select Students
                </button>
            </li>
        </ul>

        <div class="tab-content" id="generationTabContent">
            <!-- Class Generation -->
            <div class="tab-pane fade show active" id="class-generation" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Generate Invoices for Entire Class</h5>
                        <form action="{{ route('tenant.finance.invoices.generate-class') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Select Class <span class="text-danger">*</span></label>
                                    <select name="class_id" class="form-select" required>
                                        <option value="">Choose a class...</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}
                                                ({{ $class->students->count() }} students)</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" class="form-control"
                                        value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control"
                                        value="{{ setting('current_academic_year', date('Y')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Term</label>
                                    <select name="term" class="form-select">
                                        <option value="">Select Term (Optional)</option>
                                        <option value="Term 1">Term 1</option>
                                        <option value="Term 2">Term 2</option>
                                        <option value="Term 3">Term 3</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Fee Structures <span class="text-danger">*</span></label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllFeesClass"
                                                onclick="toggleAllCheckboxes(this, 'class')">
                                            <label class="form-check-label fw-bold" for="selectAllFeesClass">Select All
                                                Fees</label>
                                        </div>
                                        <hr>
                                        @foreach ($feeStructures as $fee)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="fee_structure_ids[]"
                                                    value="{{ $fee->id }}" class="form-check-input class-fee-checkbox"
                                                    id="class_fee_{{ $fee->id }}">
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="class_fee_{{ $fee->id }}">
                                                    <span>{{ $fee->fee_name }}</span>
                                                    <span class="text-muted">{{ formatMoney($fee->amount) }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes for all invoices..."></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-lightning me-1"></i> Generate Class Invoices
                                </button>
                                <a href="{{ route('tenant.finance.invoices.index') }}"
                                    class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stream Generation -->
            <div class="tab-pane fade" id="stream-generation" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Generate Invoices for Entire Stream</h5>
                        <form action="{{ route('tenant.finance.invoices.generate-stream') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Select Stream <span class="text-danger">*</span></label>
                                    <select name="stream_id" class="form-select" required>
                                        <option value="">Choose a stream...</option>
                                        @foreach ($streams as $stream)
                                            <option value="{{ $stream->id }}">{{ $stream->full_name }}
                                                ({{ $stream->students->count() }} students)</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" class="form-control"
                                        value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control"
                                        value="{{ setting('current_academic_year', date('Y')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Term</label>
                                    <select name="term" class="form-select">
                                        <option value="">Select Term (Optional)</option>
                                        <option value="Term 1">Term 1</option>
                                        <option value="Term 2">Term 2</option>
                                        <option value="Term 3">Term 3</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Fee Structures <span class="text-danger">*</span></label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllFeesStream"
                                                onclick="toggleAllCheckboxes(this, 'stream')">
                                            <label class="form-check-label fw-bold" for="selectAllFeesStream">Select All
                                                Fees</label>
                                        </div>
                                        <hr>
                                        @foreach ($feeStructures as $fee)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="fee_structure_ids[]"
                                                    value="{{ $fee->id }}"
                                                    class="form-check-input stream-fee-checkbox"
                                                    id="stream_fee_{{ $fee->id }}">
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="stream_fee_{{ $fee->id }}">
                                                    <span>{{ $fee->fee_name }}</span>
                                                    <span class="text-muted">{{ formatMoney($fee->amount) }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes for all invoices..."></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-lightning me-1"></i> Generate Stream Invoices
                                </button>
                                <a href="{{ route('tenant.finance.invoices.index') }}"
                                    class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- School-wide Generation -->
            <div class="tab-pane fade" id="school-generation" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Generate Invoices for Entire School</h5>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This will generate invoices for ALL active students in the school.
                            This operation may take a while.
                        </div>
                        <form action="{{ route('tenant.finance.invoices.generate-school') }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to generate invoices for ALL students in the school?');">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" class="form-control"
                                        value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control"
                                        value="{{ setting('current_academic_year', date('Y')) }}" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Term</label>
                                    <select name="term" class="form-select">
                                        <option value="">Select Term (Optional)</option>
                                        <option value="Term 1">Term 1</option>
                                        <option value="Term 2">Term 2</option>
                                        <option value="Term 3">Term 3</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Fee Structures <span class="text-danger">*</span></label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllFeesSchool"
                                                onclick="toggleAllCheckboxes(this, 'school')">
                                            <label class="form-check-label fw-bold" for="selectAllFeesSchool">Select All
                                                Fees</label>
                                        </div>
                                        <hr>
                                        @foreach ($feeStructures as $fee)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="fee_structure_ids[]"
                                                    value="{{ $fee->id }}"
                                                    class="form-check-input school-fee-checkbox"
                                                    id="school_fee_{{ $fee->id }}">
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="school_fee_{{ $fee->id }}">
                                                    <span>{{ $fee->fee_name }}</span>
                                                    <span class="text-muted">{{ formatMoney($fee->amount) }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes for all invoices..."></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="alert alert-info">
                                        <strong>Total Active Students:</strong> {{ $totalStudents }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-lightning me-1"></i> Generate School-wide Invoices
                                </button>
                                <a href="{{ route('tenant.finance.invoices.index') }}"
                                    class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Select Students Generation -->
            <div class="tab-pane fade" id="students-generation" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Generate Invoices for Selected Students</h5>
                        <form action="{{ route('tenant.finance.invoices.generate-students') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" class="form-control"
                                        value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" class="form-control"
                                        value="{{ setting('current_academic_year', date('Y')) }}" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Term</label>
                                    <select name="term" class="form-select">
                                        <option value="">Select Term (Optional)</option>
                                        <option value="Term 1">Term 1</option>
                                        <option value="Term 2">Term 2</option>
                                        <option value="Term 3">Term 3</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Select Students <span class="text-danger">*</span></label>
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" id="studentSearch" class="form-control"
                                                placeholder="Search students by name or ID...">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="clearSearch()">
                                                <i class="bi bi-x"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllStudents"
                                                onclick="toggleAllStudents(this)">
                                            <label class="form-check-label fw-bold" for="selectAllStudents">
                                                Select All Students (<span id="studentCount">{{ $totalStudents }}</span>)
                                            </label>
                                        </div>
                                        <hr>
                                        <div id="studentList">
                                            @foreach ($allStudents as $student)
                                                <div class="form-check mb-2 student-item"
                                                    data-student-name="{{ strtolower($student->name) }}"
                                                    data-student-id="{{ $student->student_id ?? '' }}">
                                                    <input type="checkbox" name="student_ids[]"
                                                        value="{{ $student->id }}"
                                                        class="form-check-input student-checkbox"
                                                        id="student_{{ $student->id }}">
                                                    <label class="form-check-label d-flex justify-content-between w-100"
                                                        for="student_{{ $student->id }}">
                                                        <span>
                                                            <strong>{{ $student->name }}</strong>
                                                            @if ($student->student_id)
                                                                <small class="text-muted">(ID:
                                                                    {{ $student->student_id }})</small>
                                                            @endif
                                                        </span>
                                                        <span class="text-muted small">
                                                            @if ($student->enrollments->isNotEmpty())
                                                                {{ $student->enrollments->first()->class->name ?? 'N/A' }}
                                                            @else
                                                                No Class
                                                            @endif
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <span id="selectedCount">0</span> student(s) selected
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Fee Structures <span class="text-danger">*</span></label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllFeesStudents"
                                                onclick="toggleAllCheckboxes(this, 'students')">
                                            <label class="form-check-label fw-bold" for="selectAllFeesStudents">Select All
                                                Fees</label>
                                        </div>
                                        <hr>
                                        @foreach ($feeStructures as $fee)
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="fee_structure_ids[]"
                                                    value="{{ $fee->id }}"
                                                    class="form-check-input students-fee-checkbox"
                                                    id="students_fee_{{ $fee->id }}">
                                                <label class="form-check-label d-flex justify-content-between w-100"
                                                    for="students_fee_{{ $fee->id }}">
                                                    <span>{{ $fee->fee_name }}</span>
                                                    <span class="text-muted">{{ formatMoney($fee->amount) }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes for all invoices..."></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-lightning me-1"></i> Generate Invoices for Selected Students
                                </button>
                                <a href="{{ route('tenant.finance.invoices.index') }}"
                                    class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleAllCheckboxes(checkbox, type) {
            const checkboxes = document.querySelectorAll(`.${type}-fee-checkbox`);
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        function toggleAllStudents(checkbox) {
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            studentCheckboxes.forEach(cb => {
                if (cb.closest('.student-item').style.display !== 'none') {
                    cb.checked = checkbox.checked;
                }
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.student-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = checked;
        }

        function clearSearch() {
            document.getElementById('studentSearch').value = '';
            filterStudents('');
        }

        function filterStudents(searchTerm) {
            const items = document.querySelectorAll('.student-item');
            const term = searchTerm.toLowerCase();
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.dataset.studentName || '';
                const id = item.dataset.studentId || '';

                if (name.includes(term) || id.includes(term)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('studentCount').textContent = visibleCount;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Student search
            const searchInput = document.getElementById('studentSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    filterStudents(this.value);
                });
            }

            // Update selected count on checkbox change
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            studentCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });
        });
    </script>
@endpush
