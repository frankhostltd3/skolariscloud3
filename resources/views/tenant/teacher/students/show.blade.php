@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', __('Student Overview'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary me-2">
      <i class="bi bi-arrow-left"></i> {{ __('Back') }}
    </a>
    <h1 class="h4 fw-semibold d-inline">{{ $student->name ?? __('Student Profile') }}</h1>
    <div class="text-muted small mt-1">
      {{ $class->name }} @if($class->section) &middot; {{ $class->section }} @endif
    </div>
  </div>
  <div class="d-flex flex-column text-end">
    <span class="badge bg-primary-subtle text-primary border border-primary mb-2">
      <i class="bi bi-journal-bookmark me-1"></i>{{ __('Class') }}: {{ $class->name }}
    </span>
    <span class="badge bg-info-subtle text-info border border-info">
      <i class="bi bi-person-check me-1"></i>{{ __('Status') }}: {{ $student->isActive() ? __('Active') : __('Inactive') }}
    </span>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="me-3">
            @if($student->profile_photo)
              <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
            @else
              <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <span class="text-primary fw-bold" style="font-size: 1.5rem;">{{ strtoupper(substr($student->name ?? 'S', 0, 1)) }}</span>
              </div>
            @endif
          </div>
          <div>
            <h5 class="mb-1 fw-semibold">{{ $student->name }}</h5>
            <div class="text-muted small">{{ $student->email }}</div>
            @if($student->phone)
              <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $student->phone }}</div>
            @endif
          </div>
        </div>

        <dl class="row small mb-0">
          <dt class="col-5 text-muted">{{ __('Gender') }}</dt>
          <dd class="col-7">{{ ucfirst($student->gender ?? __('N/A')) }}</dd>
          <dt class="col-5 text-muted">{{ __('Date of Birth') }}</dt>
          <dd class="col-7">{{ optional($student->date_of_birth)->format('M d, Y') ?? __('N/A') }}</dd>
          <dt class="col-5 text-muted">{{ __('Parent/Guardian') }}</dt>
          <dd class="col-7">{{ $student->parentProfile->name ?? __('Not captured') }}</dd>
          <dt class="col-5 text-muted">{{ __('Enrollment Date') }}</dt>
          <dd class="col-7">
            {{ optional($student->currentEnrollment()->first())->enrollment_date?->format('M d, Y') ?? __('N/A') }}
          </dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="fw-semibold mb-3">{{ __('Attendance Summary') }}</h5>
        <div class="row text-center g-3">
          <div class="col-6">
            <div class="border rounded p-3">
              <div class="text-success fw-bold" style="font-size: 1.4rem;">{{ $attendanceSummary['present'] ?? 0 }}</div>
              <div class="text-muted small">{{ __('Present') }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded p-3">
              <div class="text-danger fw-bold" style="font-size: 1.4rem;">{{ $attendanceSummary['absent'] ?? 0 }}</div>
              <div class="text-muted small">{{ __('Absent') }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded p-3">
              <div class="text-warning fw-bold" style="font-size: 1.4rem;">{{ $attendanceSummary['late'] ?? 0 }}</div>
              <div class="text-muted small">{{ __('Late') }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded p-3">
              <div class="text-info fw-bold" style="font-size: 1.4rem;">{{ $attendanceSummary['excused'] ?? 0 }}</div>
              <div class="text-muted small">{{ __('Excused') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="fw-semibold mb-3">{{ __('Subjects') }}</h5>
        @if($student->studentSubjects->isEmpty())
          <div class="text-muted small text-center py-3">
            <i class="bi bi-inbox me-1"></i>{{ __('No subjects assigned') }}
          </div>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($student->studentSubjects->take(8) as $subject)
              <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span>
                  <i class="bi bi-book me-2 text-primary"></i>{{ $subject->display_name ?? $subject->name }}
                </span>
                <span class="badge bg-light text-muted">{{ $subject->pivot->status ?? __('Current') }}</span>
              </li>
            @endforeach
          </ul>
          @if($student->studentSubjects->count() > 8)
            <div class="text-center mt-3">
              <span class="badge bg-secondary-subtle text-secondary">
                {{ __('+ :count more subjects', ['count' => $student->studentSubjects->count() - 8]) }}
              </span>
            </div>
          @endif
        @endif
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mt-4">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0">{{ __('Recent Grades Issued By You') }}</h5>
      <a href="{{ route('tenant.teacher.grades.index', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-list-check me-1"></i>{{ __('All Grades') }}
      </a>
    </div>

    @if($recentGrades->isEmpty())
      <div class="text-center py-4 text-muted">
        <i class="bi bi-clipboard-x me-2"></i>{{ __('No grades recorded yet for this student by you.') }}
      </div>
    @else
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>{{ __('Date') }}</th>
              <th>{{ __('Subject') }}</th>
              <th>{{ __('Assessment') }}</th>
              <th class="text-center">{{ __('Marks') }}</th>
              <th class="text-center">{{ __('Grade') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentGrades as $grade)
              <tr>
                <td>{{ optional($grade->assessment_date)->format('M d, Y') ?? __('N/A') }}</td>
                <td>{{ $grade->subject->display_name ?? $grade->subject->name ?? __('Subject') }}</td>
                <td>{{ $grade->assessment_name ?? ucfirst($grade->assessment_type) }}</td>
                <td class="text-center">
                  @if($grade->total_marks)
                    {{ $grade->marks_obtained }} / {{ $grade->total_marks }}
                  @else
                    {{ $grade->marks_obtained ?? __('N/A') }}
                  @endif
                </td>
                <td class="text-center">
                  <span class="badge bg-primary-subtle text-primary border border-primary">{{ $grade->grade_letter ?? __('N/A') }}</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection

