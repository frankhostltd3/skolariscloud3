@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Edit Fee') }}</h1>
    <div class="small text-secondary">{{ __('Update fee information') }}</div>
  </div>
  <a href="{{ route('tenant.modules.fees.show', $fee) }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Fee Details') }}
  </a>
</div>

<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('tenant.modules.fees.update', $fee) }}">
          @csrf
          @method('PUT')

          {{-- Fee Name --}}
          <div class="mb-3">
            <label for="name" class="form-label fw-medium">{{ __('Fee Name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name', $fee->name) }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Description --}}
          <div class="mb-3">
            <label for="description" class="form-label fw-medium">{{ __('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3">{{ old('description', $fee->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Amount and Category --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label fw-medium">{{ __('Amount') }} <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">{{ currency_symbol() }}</span>
                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                       id="amount" name="amount" value="{{ old('amount', $fee->amount) }}" required>
                @error('amount')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="category" class="form-label fw-medium">{{ __('Category') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                <option value="">{{ __('Select Category') }}</option>
                <option value="tuition" {{ old('category', $fee->category) == 'tuition' ? 'selected' : '' }}>{{ __('Tuition') }}</option>
                <option value="registration" {{ old('category', $fee->category) == 'registration' ? 'selected' : '' }}>{{ __('Registration') }}</option>
                <option value="exam" {{ old('category', $fee->category) == 'exam' ? 'selected' : '' }}>{{ __('Exam') }}</option>
                <option value="library" {{ old('category', $fee->category) == 'library' ? 'selected' : '' }}>{{ __('Library') }}</option>
                <option value="transport" {{ old('category', $fee->category) == 'transport' ? 'selected' : '' }}>{{ __('Transport') }}</option>
                <option value="hostel" {{ old('category', $fee->category) == 'hostel' ? 'selected' : '' }}>{{ __('Hostel') }}</option>
                <option value="uniform" {{ old('category', $fee->category) == 'uniform' ? 'selected' : '' }}>{{ __('Uniform') }}</option>
                <option value="activity" {{ old('category', $fee->category) == 'activity' ? 'selected' : '' }}>{{ __('Activity') }}</option>
                <option value="other" {{ old('category', $fee->category) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
              </select>
              @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Due Date and Recurring Type --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="due_date" class="form-label fw-medium">{{ __('Due Date') }} <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                     id="due_date" name="due_date" value="{{ old('due_date', $fee->due_date?->format('Y-m-d')) }}" required>
              @error('due_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="recurring_type" class="form-label fw-medium">{{ __('Recurring Type') }} <span class="text-danger">*</span></label>
              <select class="form-select @error('recurring_type') is-invalid @enderror" id="recurring_type" name="recurring_type" required>
                <option value="one-time" {{ old('recurring_type', $fee->recurring_type) == 'one-time' ? 'selected' : '' }}>{{ __('One-time') }}</option>
                <option value="monthly" {{ old('recurring_type', $fee->recurring_type) == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                <option value="quarterly" {{ old('recurring_type', $fee->recurring_type) == 'quarterly' ? 'selected' : '' }}>{{ __('Quarterly') }}</option>
                <option value="yearly" {{ old('recurring_type', $fee->recurring_type) == 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                <option value="term-based" {{ old('recurring_type', $fee->recurring_type) == 'term-based' ? 'selected' : '' }}>{{ __('Term-based') }}</option>
              </select>
              @error('recurring_type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Applicable To --}}
          <div class="mb-3">
            <label for="applicable_to" class="form-label fw-medium">{{ __('Applicable To') }} <span class="text-danger">*</span></label>
            <select class="form-select @error('applicable_to') is-invalid @enderror" 
                    id="applicable_to" name="applicable_to" required onchange="toggleApplicableFields()">
              <option value="all" {{ old('applicable_to', $fee->applicable_to) == 'all' ? 'selected' : '' }}>{{ __('All Students') }}</option>
              <option value="specific_class" {{ old('applicable_to', $fee->applicable_to) == 'specific_class' ? 'selected' : '' }}>{{ __('Specific Class') }}</option>
              <option value="specific_student" {{ old('applicable_to', $fee->applicable_to) == 'specific_student' ? 'selected' : '' }}>{{ __('Specific Student') }}</option>
            </select>
            @error('applicable_to')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Class Selection (Hidden by default) --}}
          <div class="mb-3" id="class_field" style="display: none;">
            <label for="class_id" class="form-label fw-medium">{{ __('Select Class') }}</label>
            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id">
              <option value="">{{ __('Select a class') }}</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ old('class_id', $fee->class_id) == $class->id ? 'selected' : '' }}>
                  {{ $class->name }}
                </option>
              @endforeach
            </select>
            @error('class_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Student Selection (Hidden by default) --}}
          <div class="mb-3" id="student_field" style="display: none;">
            <label for="student_id" class="form-label fw-medium">{{ __('Select Student') }}</label>
            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id">
              <option value="">{{ __('Select a student') }}</option>
              @foreach($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id', $fee->student_id) == $student->id ? 'selected' : '' }}>
                  {{ $student->full_name }} - {{ $student->admission_number }}
                </option>
              @endforeach
            </select>
            @error('student_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Active Status --}}
          <div class="mb-4">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                     {{ old('is_active', $fee->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                {{ __('Active (Fee can be assigned and collected)') }}
              </label>
            </div>
          </div>

          {{-- Submit Buttons --}}
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-1"></i>{{ __('Update Fee') }}
            </button>
            <a href="{{ route('tenant.modules.fees.show', $fee) }}" class="btn btn-outline-secondary">
              {{ __('Cancel') }}
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- Tips Card --}}
    <div class="card shadow-sm mt-3 border-info">
      <div class="card-header bg-info bg-opacity-10">
        <h6 class="mb-0 text-info"><i class="bi bi-lightbulb me-2"></i>{{ __('Tips') }}</h6>
      </div>
      <div class="card-body">
        <ul class="mb-0 small">
          <li>{{ __('Choose "All Students" to apply the fee to every student in the school') }}</li>
          <li>{{ __('Select "Specific Class" to target students in a particular class or grade') }}</li>
          <li>{{ __('Use "Specific Student" for individual fee assignments (e.g., special cases)') }}</li>
          <li>{{ __('Recurring fees will automatically generate new charges based on the selected frequency') }}</li>
          <li>{{ __('You can assign fees to additional classes/students later from the fee details page') }}</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
function toggleApplicableFields() {
  const applicableTo = document.getElementById('applicable_to').value;
  const classField = document.getElementById('class_field');
  const studentField = document.getElementById('student_field');
  
  classField.style.display = 'none';
  studentField.style.display = 'none';
  
  if (applicableTo === 'specific_class') {
    classField.style.display = 'block';
  } else if (applicableTo === 'specific_student') {
    studentField.style.display = 'block';
  }
}

// Run on page load to show correct fields if there are validation errors
document.addEventListener('DOMContentLoaded', function() {
  toggleApplicableFields();
});
</script>
@endsection
