@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Enter grades') }}</h1>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form action="{{ route('tenant.modules.grades.store') }}" method="post">
      @csrf
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('Student') }}</label>
          <select name="student_id" class="form-select @error('student_id') is-invalid @enderror">
            <option value="">-- {{ __('Select student') }} --</option>
            @foreach($students as $student)
              <option value="{{ $student->id }}" @selected(old('student_id')==$student->id)>{{ $student->name }}</option>
            @endforeach
          </select>
          @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('Subject') }}</label>
          <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror">
            <option value="">-- {{ __('Select subject') }} --</option>
            @foreach($subjects as $subject)
              <option value="{{ $subject->id }}" @selected(old('subject_id')==$subject->id)>{{ $subject->name }}</option>
            @endforeach
          </select>
          @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">{{ __('Teacher (optional)') }}</label>
          <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
            <option value="">-- {{ __('Select teacher') }} --</option>
            @foreach($teachers as $teacher)
              <option value="{{ $teacher->id }}" @selected(old('teacher_id')==$teacher->id)>{{ $teacher->name }}</option>
            @endforeach
          </select>
          @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">{{ __('Term (e.g., 2025 T1)') }}</label>
          <input type="text" name="term" value="{{ old('term') }}" class="form-control @error('term') is-invalid @enderror" placeholder="2025 T1" />
          @error('term')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">{{ __('Score') }}</label>
          <input type="text" name="score" value="{{ old('score') }}" class="form-control @error('score') is-invalid @enderror" placeholder="A or 85" />
          @error('score')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label">{{ __('Awarded on') }}</label>
          <input type="date" name="awarded_on" value="{{ old('awarded_on') }}" class="form-control @error('awarded_on') is-invalid @enderror" />
          @error('awarded_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      @php
        // Attempt to resolve active scheme for preview using the first subject selection if present
        $selectedSubject = null;
        if (old('subject_id')) { $selectedSubject = \App\Models\Subject::with('educationLevel')->find(old('subject_id')); }
        $currentTerm = \App\Models\Term::where('is_current', true)->first();
        $activeScheme = \App\Support\Grading::resolveScheme($selectedSubject, $currentTerm);
      @endphp
      @if($activeScheme)
        <div class="mt-4">
          <div class="d-flex align-items-center justify-content-between">
            <h2 class="h6 mb-2">{{ __('Active grading scheme') }}: <span class="fw-semibold">{{ $activeScheme->name }}</span></h2>
            <span class="text-muted small">{{ __('preview') }}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
              <thead>
                <tr>
                  <th class="text-nowrap">{{ __('Order') }}</th>
                  <th>{{ __('Code') }}</th>
                  <th>{{ __('Label') }}</th>
                  <th class="text-nowrap">{{ __('Min') }}</th>
                  <th class="text-nowrap">{{ __('Max') }}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($activeScheme->bands as $b)
                  <tr>
                    <td>{{ $b->order }}</td>
                    <td>{{ $b->code ?: '—' }}</td>
                    <td>{{ $b->label }}</td>
                    <td>{{ $b->min_score }}</td>
                    <td>{{ $b->max_score }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <p class="text-muted small mt-2">{{ __('Numeric scores will be mapped to a band on save.') }}</p>
        </div>
      @endif
      <div class="mt-3">
        <button class="btn btn-primary" type="submit">{{ __('Save grade') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
