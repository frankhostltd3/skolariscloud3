@extends('tenant.layouts.app')

@section('title', __('Create Fee Item'))

@section('sidebar')
<div class="card shadow-sm mb-4">
  <div class="card-header fw-semibold">{{ __('Fee Management') }}</div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.overview') }}">
      <span class="bi bi-speedometer2 me-2"></span>{{ __('Financial Overview') }}
    </a>
    <a class="list-group-item list-group-item-action active" href="{{ route('tenant.modules.financials.fees') }}">
      <span class="bi bi-cash-stack me-2"></span>{{ __('Fee Management') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.expenses') }}">
      <span class="bi bi-receipt me-2"></span>{{ __('Expenses') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.tuition_plans') }}">
      <span class="bi bi-file-earmark-text me-2"></span>{{ __('Tuition Plans') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.invoices') }}">
      <span class="bi bi-file-earmark-pdf me-2"></span>{{ __('Invoices') }}
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">{{ __('Quick Tips') }}</div>
  <div class="card-body">
    <div class="small text-muted">
      <div class="mb-3">
        <strong>{{ __('Fee Categories') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('Tuition: Regular school fees') }}</li>
          <li>{{ __('Exam: Examination fees') }}</li>
          <li>{{ __('Transport: Bus/transport fees') }}</li>
          <li>{{ __('Library: Library access fees') }}</li>
        </ul>
      </div>

      <div class="mb-3">
        <strong>{{ __('Recurring Types') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('One-time: Single payment') }}</li>
          <li>{{ __('Monthly: Monthly installments') }}</li>
          <li>{{ __('Yearly: Annual payments') }}</li>
          <li>{{ __('Term-based: Per academic term') }}</li>
        </ul>
      </div>

      <div class="alert alert-info small p-2">
        <strong>{{ __('Tip:') }}</strong> {{ __('Set appropriate due dates to ensure timely payments and avoid late fees.') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h5 fw-semibold mb-0">{{ __('Create Fee Item') }}</h1>
        <div class="small text-secondary">{{ __('Add a new fee item to the system') }}</div>
    </div>
    <div class="card-body">
        <form action="{{ route('tenant.modules.financials.fees.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Fee Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category" class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('category') is-invalid @enderror"
                                id="category" name="category" required>
                            <option value="">{{ __('Select Category') }}</option>
                            <option value="tuition" {{ old('category') == 'tuition' ? 'selected' : '' }}>{{ __('Tuition') }}</option>
                            <option value="exam" {{ old('category') == 'exam' ? 'selected' : '' }}>{{ __('Exam Fees') }}</option>
                            <option value="registration" {{ old('category') == 'registration' ? 'selected' : '' }}>{{ __('Registration') }}</option>
                            <option value="transport" {{ old('category') == 'transport' ? 'selected' : '' }}>{{ __('Transport') }}</option>
                            <option value="library" {{ old('category') == 'library' ? 'selected' : '' }}>{{ __('Library') }}</option>
                            <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>{{ __('Sports') }}</option>
                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>{{ __('General') }}</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="amount" class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">{{ __('$') }}</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                   id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="due_date" class="form-label">{{ __('Due Date') }}</label>
                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                               id="due_date" name="due_date" value="{{ old('due_date') }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="recurring_type" class="form-label">{{ __('Recurring Type') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('recurring_type') is-invalid @enderror"
                                id="recurring_type" name="recurring_type" required>
                            <option value="one-time" {{ old('recurring_type', 'one-time') == 'one-time' ? 'selected' : '' }}>{{ __('One-time') }}</option>
                            <option value="monthly" {{ old('recurring_type') == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                            <option value="yearly" {{ old('recurring_type') == 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                            <option value="term-based" {{ old('recurring_type') == 'term-based' ? 'selected' : '' }}>{{ __('Term-based') }}</option>
                        </select>
                        @error('recurring_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="applicable_to" class="form-label">{{ __('Applicable To') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('applicable_to') is-invalid @enderror"
                                id="applicable_to" name="applicable_to" required>
                            <option value="all" {{ old('applicable_to', 'all') == 'all' ? 'selected' : '' }}>{{ __('All Students') }}</option>
                            <option value="specific_class" {{ old('applicable_to') == 'specific_class' ? 'selected' : '' }}>{{ __('Specific Class') }}</option>
                            <option value="specific_student" {{ old('applicable_to') == 'specific_student' ? 'selected' : '' }}>{{ __('Specific Student') }}</option>
                        </select>
                        @error('applicable_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Conditional fields based on applicable_to selection -->
            <div class="mb-3" id="class_selection" style="display: none;">
                <label for="class_id" class="form-label">{{ __('Select Class') }}</label>
                <select class="form-select @error('class_id') is-invalid @enderror"
                        id="class_id" name="class_id">
                    <option value="">{{ __('Select Class') }}</option>
                    <!-- Classes would be populated here -->
                </select>
                @error('class_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="student_selection" style="display: none;">
                <label for="student_id" class="form-label">{{ __('Select Student') }}</label>
                <select class="form-select @error('student_id') is-invalid @enderror"
                        id="student_id" name="student_id">
                    <option value="">{{ __('Select Student') }}</option>
                    <!-- Students would be populated here -->
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        {{ __('Active') }}
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Create Fee Item') }}
                </button>
                <a href="{{ route('tenant.modules.financials.fees') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicableToSelect = document.getElementById('applicable_to');
    const classSelection = document.getElementById('class_selection');
    const studentSelection = document.getElementById('student_selection');

    function toggleSelections() {
        const value = applicableToSelect.value;
        classSelection.style.display = value === 'specific_class' ? 'block' : 'none';
        studentSelection.style.display = value === 'specific_student' ? 'block' : 'none';
    }

    applicableToSelect.addEventListener('change', toggleSelections);
    toggleSelections(); // Initial check
});
</script>
@endsection