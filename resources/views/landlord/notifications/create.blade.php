@extends('landlord.layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <h1 class="h4 fw-semibold mb-3">{{ __('New notification') }}</h1>
    <form action="{{ route('landlord.notifications.store') }}" method="post" class="row g-3">
      @csrf
      <div class="col-md-6">
        <label class="form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Channel') }}</label>
        <select name="channel" class="form-select @error('channel') is-invalid @enderror" required>
          @foreach (['system','email','sms','slack','webhook'] as $ch)
            <option value="{{ $ch }}" @selected(old('channel')===$ch)>{{ Str::upper($ch) }}</option>
          @endforeach
        </select>
        @error('channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12">
        <label class="form-label">{{ __('Message') }}</label>
        <textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Plans (optional)') }}</label>
        <select name="audience[plans][]" class="form-select" multiple>
          @foreach (['starter','growth','premium','enterprise'] as $plan)
            <option value="{{ $plan }}">{{ Str::headline($plan) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Countries (optional)') }}</label>
        <input type="text" name="audience[countries][]" class="form-control" placeholder="KE, UG, TZ">
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Landlord roles (optional)') }}</label>
        <input type="text" name="audience[landlord_roles][]" class="form-control" placeholder="landlord-admin">
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Schedule at (optional)') }}</label>
        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><span class="bi bi-check2 me-1"></span>{{ __('Save') }}</button>
        <a href="{{ route('landlord.notifications.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
