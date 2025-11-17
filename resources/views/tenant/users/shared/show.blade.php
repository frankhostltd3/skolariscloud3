@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.users.partials.sidebar')
@endsection

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 fw-semibold mb-0">{{ $user->name }}</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary" href="{{ route($routePrefix . '.edit', $user) }}">{{ __('Edit') }}</a>
      <form class="d-inline" method="POST" action="{{ route($routePrefix . '.destroy', $user) }}" onsubmit="return confirm('{{ __('Delete this user?') }}')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">{{ __('Name') }}</dt>
        <dd class="col-sm-9">{{ $user->name }}</dd>
        <dt class="col-sm-3">{{ __('Email') }}</dt>
        <dd class="col-sm-9">{{ $user->email }}</dd>
        <dt class="col-sm-3">{{ __('Created') }}</dt>
        <dd class="col-sm-9">{{ $user->created_at?->diffForHumans() }}</dd>
      </dl>
    </div>
  </div>

  @if(auth()->user()->hasRole('Admin'))
  <div class="row g-3">
    @php
      // Lightweight data fetch directly in the view for simple forms
      $classes = \App\Models\Academic\ClassRoom::query()->orderBy('name')->get(['id','name']);
      $subjects = \App\Models\Subject::query()->orderBy('name')->get(['id','name','code']);
      $streams = \App\Models\ClassStream::query()->orderBy('name')->get(['id','name']);
    @endphp

    @if($user->hasRole('Staff') || $user->hasRole('Teacher'))
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h2 class="h6 mb-0">{{ __('Teacher Management') }}</h2>
          @if(!$user->hasRole('Teacher'))
          <form method="POST" action="{{ route('admin.academics.teachers.register', $user) }}">
            @csrf
            <button class="btn btn-sm btn-primary">{{ __('Register as Teacher') }}</button>
          </form>
          @else
          <span class="badge bg-success">{{ __('Registered Teacher') }}</span>
          @endif
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <h3 class="h6">{{ __('Assign Class Teacher') }}</h3>
                <form method="POST" action="{{ route('admin.academics.teachers.assign_class', $user) }}">
                  @csrf
                  <div class="mb-2">
                    <label for="assign_class_id" class="form-label">{{ __('Class') }}</label>
                    <select id="assign_class_id" name="class_id" class="form-select" required>
                      <option value="">-- {{ __('Select class') }} --</option>
                      @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <button class="btn btn-sm btn-outline-primary">{{ __('Assign Class') }}</button>
                </form>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <h3 class="h6">{{ __('Assign Subjects to Class') }}</h3>
                <form method="POST" action="{{ route('admin.academics.teachers.assign_subjects', $user) }}">
                  @csrf
                  <div class="mb-2">
                    <label for="assign_subjects_class_id" class="form-label">{{ __('Class') }}</label>
                    <select id="assign_subjects_class_id" name="class_id" class="form-select" required>
                      <option value="">-- {{ __('Select class') }} --</option>
                      @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-2">
                    <label class="form-label">{{ __('Subjects') }}</label>
                    <div class="border rounded p-2" style="max-height: 220px; overflow:auto;">
                      @foreach($subjects as $s)
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="subject_ids[]" id="sub{{ $s->id }}" value="{{ $s->id }}">
                          <label class="form-check-label" for="sub{{ $s->id }}">{{ $s->name }} @if($s->code)<span class="text-muted">({{ $s->code }})</span>@endif</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <button class="btn btn-sm btn-outline-primary">{{ __('Assign Subjects') }}</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    @if($user->hasRole('Student'))
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <h2 class="h6 mb-0">{{ __('Enroll Student to Class') }}</h2>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.academics.students.enroll', $user) }}" class="row g-3">
            @csrf
            <div class="col-md-4">
              <label for="enroll_class_id" class="form-label">{{ __('Class') }}</label>
              <select id="enroll_class_id" name="class_id" class="form-select" required>
                <option value="">-- {{ __('Select class') }} --</option>
                @foreach($classes as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label for="enroll_stream_id" class="form-label">{{ __('Class Stream (optional)') }}</label>
              <select id="enroll_stream_id" name="class_stream_id" class="form-select">
                <option value="">-- {{ __('None') }} --</option>
                @foreach($streams as $st)
                  <option value="{{ $st->id }}">{{ $st->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label for="enroll_date" class="form-label">{{ __('Enrollment Date') }}</label>
              <input type="date" id="enroll_date" name="enrollment_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
              <label for="enroll_status" class="form-label">{{ __('Status') }}</label>
              <select id="enroll_status" name="status" class="form-select">
                <option value="active">{{ __('Active') }}</option>
                <option value="completed">{{ __('Completed') }}</option>
                <option value="transferred">{{ __('Transferred') }}</option>
                <option value="dropped">{{ __('Dropped') }}</option>
              </select>
            </div>
            <div class="col-12">
              <button class="btn btn-sm btn-primary">{{ __('Enroll Student') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endif
  </div>
  @endif
@endsection
