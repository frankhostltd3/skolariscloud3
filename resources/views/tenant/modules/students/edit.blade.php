@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Edit Student') }}</h1>
  <a class="btn btn-light" href="{{ route('tenant.modules.students.show', $student) }}">{{ __('Back') }}</a>
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
    <form action="{{ route('tenant.modules.students.update', $student) }}" method="post">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('Full name') }}</label>
          <input type="text" name="name" value="{{ old('name', $student->name) }}" class="form-control @error('name') is-invalid @enderror" />
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Admission No.') }}</label>
          <input type="text" name="admission_no" value="{{ old('admission_no', $student->admission_no) }}" class="form-control @error('admission_no') is-invalid @enderror" />
          @error('admission_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Email') }}</label>
          <input type="email" name="email" value="{{ old('email', $student->email) }}" class="form-control @error('email') is-invalid @enderror" />
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Date of birth') }}</label>
          <input type="date" name="dob" value="{{ old('dob', $student->dob) }}" class="form-control @error('dob') is-invalid @enderror" />
          @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
