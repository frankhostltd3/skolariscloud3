@extends('tenant.layouts.app')

@section('title', __('Assign Fee to Class/Level'))

@section('sidebar')
<div class="card shadow-sm mb-4">
  <div class="card-header fw-semibold">{{ __('Fee Management') }}</div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.overview') }}">
      <span class="bi bi-speedometer2 me-2"></span>{{ __('Financial Overview') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.fees') }}">
      <span class="bi bi-cash-stack me-2"></span>{{ __('Fee Management') }}
    </a>
    <a class="list-group-item list-group-item-action active" href="{{ route('tenant.modules.financials.fees.assign') }}">
      <span class="bi bi-person-plus me-2"></span>{{ __('Assign Fees') }}
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
  <div class="card-header fw-semibold">{{ __('Assignment Tips') }}</div>
  <div class="card-body">
    <div class="small text-muted">
      <div class="mb-3">
        <strong>{{ __('Assignment Types') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('Class Assignment: Apply fee to all students in a class') }}</li>
          <li>{{ __('Student Assignment: Apply fee to a specific student') }}</li>
        </ul>
      </div>

      <div class="alert alert-info small p-2">
        <strong>{{ __('Tip:') }}</strong> {{ __('Effective date determines when the fee assignment becomes active. Use this for future-dated assignments.') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h5 fw-semibold mb-0">{{ __('Assign Fee to Class/Level') }}</h1>
        <div class="small text-secondary">{{ __('Assign existing fee items to classes or individual students') }}</div>
    </div>
    <div class="card-body">
        <form action="{{ route('tenant.modules.financials.fees.assign.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fee_id" class="form-label">{{ __('Select Fee Item') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('fee_id') is-invalid @enderror"
                                id="fee_id" name="fee_id" required>
                            <option value="">{{ __('Select Fee Item') }}</option>
                            @foreach($fees as $fee)
                                <option value="{{ $fee->id }}" {{ old('fee_id') == $fee->id ? 'selected' : '' }}>
                                    {{ $fee->name }} - ${{ number_format($fee->amount, 2) }} ({{ ucfirst($fee->recurring_type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('fee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="assignment_type" class="form-label">{{ __('Assignment Type') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('assignment_type') is-invalid @enderror"
                                id="assignment_type" name="assignment_type" required>
                            <option value="">{{ __('Select Assignment Type') }}</option>
                            <option value="class" {{ old('assignment_type') == 'class' ? 'selected' : '' }}>{{ __('Assign to Class') }}</option>
                            <option value="student" {{ old('assignment_type') == 'student' ? 'selected' : '' }}>{{ __('Assign to Student') }}</option>
                        </select>
                        @error('assignment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Class Selection -->
            <div class="mb-3" id="class_selection" style="display: none;">
                <label for="class_id" class="form-label">{{ __('Select Class') }} <span class="text-danger">*</span></label>
                <select class="form-select @error('class_id') is-invalid @enderror"
                        id="class_id" name="class_id">
                    <option value="">{{ __('Select Class') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('This fee will be assigned to all students in the selected class.') }}</div>
            </div>

            <!-- Student Selection -->
            <div class="mb-3" id="student_selection" style="display: none;">
                <label for="student_id" class="form-label">{{ __('Select Student') }} <span class="text-danger">*</span></label>
                <select class="form-select @error('student_id') is-invalid @enderror"
                        id="student_id" name="student_id">
                    <option value="">{{ __('Select Student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }} ({{ $student->student_id }})
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('This fee will be assigned to the selected student only.') }}</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="effective_date" class="form-label">{{ __('Effective Date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('effective_date') is-invalid @enderror"
                               id="effective_date" name="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}" required>
                        @error('effective_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('Date when this fee assignment becomes active.') }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('Notes') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3" placeholder="{{ __('Optional notes about this assignment...') }}">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Assign Fee') }}
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
    const assignmentTypeSelect = document.getElementById('assignment_type');
    const classSelection = document.getElementById('class_selection');
    const studentSelection = document.getElementById('student_selection');

    function toggleAssignmentFields() {
        const value = assignmentTypeSelect.value;

        if (value === 'class') {
            classSelection.style.display = 'block';
            studentSelection.style.display = 'none';
            // Clear student selection
            document.getElementById('student_id').value = '';
        } else if (value === 'student') {
            classSelection.style.display = 'none';
            studentSelection.style.display = 'block';
            // Clear class selection
            document.getElementById('class_id').value = '';
        } else {
            classSelection.style.display = 'none';
            studentSelection.style.display = 'none';
        }
    }

    assignmentTypeSelect.addEventListener('change', toggleAssignmentFields);
    toggleAssignmentFields(); // Initial call
});
</script>
@endsection