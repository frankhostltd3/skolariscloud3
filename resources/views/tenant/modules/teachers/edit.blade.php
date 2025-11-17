@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.modules.teachers._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Edit Teacher') }}</h1>
  <a class="btn btn-light" href="{{ route('tenant.modules.teachers.show', $teacher) }}">{{ __('Back') }}</a>
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
    <form action="{{ route('tenant.modules.teachers.update', $teacher) }}" method="post">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('Full name') }}</label>
          <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="form-control @error('name') is-invalid @enderror" />
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Email') }}</label>
          <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="form-control @error('email') is-invalid @enderror" />
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Phone') }}</label>
          <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="form-control @error('phone') is-invalid @enderror" />
          @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
