@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.users.partials.sidebar')
@endsection

@section('content')
  <h1 class="h4 fw-semibold mb-3">{{ __('Add :title', ['title' => $title]) }}</h1>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route($routePrefix . '.store') }}" class="row g-3">
        @csrf
        <div class="col-12 col-md-6">
          <label class="form-label" for="name">{{ __('Name') }}</label>
          <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="email">{{ __('Email') }}</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="password">{{ __('Password') }}</label>
          <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
          <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
        </div>
        <div class="col-12 d-flex gap-2">
          <a class="btn btn-outline-secondary" href="{{ route($routePrefix) }}">{{ __('Cancel') }}</a>
          <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
