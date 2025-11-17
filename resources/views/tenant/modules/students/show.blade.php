@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Student') }} #{{ $student->id }}</h1>
  <div>
    <a class="btn btn-outline-secondary" href="{{ route('tenant.modules.students.edit', $student) }}">{{ __('Edit') }}</a>
    <a class="btn btn-light" href="{{ route('tenant.modules.students.index') }}">{{ __('Back') }}</a>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <dl class="row">
      <dt class="col-sm-3">{{ __('Name') }}</dt><dd class="col-sm-9">{{ $student->name }}</dd>
      <dt class="col-sm-3">{{ __('Admission No') }}</dt><dd class="col-sm-9">{{ $student->admission_no }}</dd>
      <dt class="col-sm-3">{{ __('Email') }}</dt><dd class="col-sm-9">{{ $student->email }}</dd>
      <dt class="col-sm-3">{{ __('DOB') }}</dt><dd class="col-sm-9">{{ $student->dob }}</dd>
    </dl>
    <form class="mt-3" action="{{ route('tenant.modules.students.destroy', $student) }}" method="post" onsubmit="return confirm('{{ __('Delete this student?') }}')">
      @csrf
      @method('DELETE')
      <button class="btn btn-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</div>
@endsection
