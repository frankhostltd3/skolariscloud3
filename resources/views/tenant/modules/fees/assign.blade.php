@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Assign Fee') }}: {{ $fee->name }}</h1>
    <div class="small text-secondary">{{ __('Assign fee to classes or students') }}</div>
  </div>
  <a href="{{ route('tenant.modules.fees.show', $fee) }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
  </a>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('tenant.modules.fees.assign.store', $fee) }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Assignment Type') }} <span class="text-danger">*</span></label>
            <select class="form-select @error('assignment_type') is-invalid @enderror" 
                    name="assignment_type" id="assignment_type" required onchange="toggleFields()">
              <option value="">{{ __('Select Type') }}</option>
              <option value="class">{{ __('Assign to Class') }}</option>
              <option value="student">{{ __('Assign to Student') }}</option>
              <option value="bulk_students">{{ __('Bulk Assign to Students') }}</option>
            </select>
            @error('assignment_type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3" id="class_field" style="display:none;">
            <label class="form-label fw-medium">{{ __('Select Class') }}</label>
            <select class="form-select @error('class_id') is-invalid @enderror" name="class_id">
              <option value="">{{ __('Choose a class') }}</option>
              @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
              @endforeach
            </select>
            @error('class_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3" id="student_field" style="display:none;">
            <label class="form-label fw-medium">{{ __('Select Student') }}</label>
            <select class="form-select @error('student_id') is-invalid @enderror" name="student_id">
              <option value="">{{ __('Choose a student') }}</option>
              @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->full_name }} - {{ $student->admission_number }}</option>
              @endforeach
            </select>
            @error('student_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3" id="bulk_students_field" style="display:none;">
            <label class="form-label fw-medium">{{ __('Select Students') }}</label>
            <select class="form-select @error('student_ids') is-invalid @enderror" 
                    name="student_ids[]" multiple size="8">
              @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->full_name }} - {{ $student->admission_number }}</option>
              @endforeach
            </select>
            <small class="text-secondary">{{ __('Hold Ctrl (Windows) or Cmd (Mac) to select multiple students') }}</small>
            @error('student_ids')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium">{{ __('Effective Date') }} <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('effective_date') is-invalid @enderror" 
                   name="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
            @error('effective_date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium">{{ __('Notes') }}</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" 
                      name="notes" rows="2">{{ old('notes') }}</textarea>
            @error('notes')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-1"></i>{{ __('Assign Fee') }}
            </button>
            <a href="{{ route('tenant.modules.fees.show', $fee) }}" class="btn btn-outline-secondary">
              {{ __('Cancel') }}
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm border-info">
      <div class="card-header bg-info bg-opacity-10">
        <h6 class="mb-0 text-info"><i class="bi bi-info-circle me-2"></i>{{ __('Existing Assignments') }}</h6>
      </div>
      <div class="card-body">
        @if($existingAssignments->count() > 0)
          <ul class="list-unstyled mb-0 small">
            @foreach($existingAssignments as $assignment)
              <li class="mb-2">
                <i class="bi bi-check-circle text-success me-1"></i>
                @if($assignment->assignment_type === 'class' && $assignment->assignedClass)
                  <strong>Class:</strong> {{ $assignment->assignedClass->name }}
                @elseif($assignment->assignment_type === 'student' && $assignment->assignedStudent)
                  <strong>Student:</strong> {{ $assignment->assignedStudent->full_name }}
                @endif
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-secondary small mb-0">{{ __('No existing assignments') }}</p>
        @endif
      </div>
    </div>
  </div>
</div>

<script>
function toggleFields() {
  const type = document.getElementById('assignment_type').value;
  document.getElementById('class_field').style.display = 'none';
  document.getElementById('student_field').style.display = 'none';
  document.getElementById('bulk_students_field').style.display = 'none';
  
  if (type === 'class') {
    document.getElementById('class_field').style.display = 'block';
  } else if (type === 'student') {
    document.getElementById('student_field').style.display = 'block';
  } else if (type === 'bulk_students') {
    document.getElementById('bulk_students_field').style.display = 'block';
  }
}
</script>
@endsection
