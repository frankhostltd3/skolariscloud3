@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.users.partials.sidebar')
@endsection

@section('content')
  <h1 class="h4 fw-semibold mb-3">{{ __('Edit :title', ['title' => $title]) }}</h1>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route($routePrefix . '.update', $user) }}" class="row g-3">
        @csrf
        @method('PUT')
        <div class="col-12 col-md-6">
          <label class="form-label" for="name">{{ __('Name') }}</label>
          <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="email">{{ __('Email') }}</label>
          <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="password">{{ __('Password') }}</label>
          <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Leave blank to keep current') }}">
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
          <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="{{ __('Leave blank to keep current') }}">
        </div>
        <div class="col-12 d-flex gap-2">
          <a class="btn btn-outline-secondary" href="{{ route($routePrefix . '.show', $user) }}">{{ __('Cancel') }}</a>
          <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
