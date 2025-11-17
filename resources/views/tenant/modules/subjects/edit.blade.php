@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Edit Subject') }}</h1>
  <a class="btn btn-light" href="{{ route('tenant.modules.subjects.show', $subject) }}">{{ __('Back') }}</a>
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
    <form action="{{ route('tenant.modules.subjects.update', $subject) }}" method="post">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('Code') }}</label>
          <input type="text" name="code" value="{{ old('code', $subject->code) }}" class="form-control @error('code') is-invalid @enderror" />
          @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
          <label class="form-label">{{ __('Name') }}</label>
          <input type="text" name="name" value="{{ old('name', $subject->name) }}" class="form-control @error('name') is-invalid @enderror" />
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
