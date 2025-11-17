@extends('landlord.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 fw-semibold mb-0">{{ __('Edit notification') }}</h1>
  <form action="{{ route('landlord.notifications.dispatch', $notification) }}" method="post" onsubmit="return confirm('{{ __('Dispatch now?') }}')">
    @csrf
    <button class="btn btn-success btn-sm" type="submit"><span class="bi bi-send me-1"></span>{{ __('Dispatch now') }}</button>
  </form>
  </div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <form action="{{ route('landlord.notifications.update', $notification) }}" method="post" class="row g-3">
      @csrf
      @method('PUT')
      <div class="col-md-6">
        <label class="form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $notification->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Channel') }}</label>
        <select name="channel" class="form-select @error('channel') is-invalid @enderror" required>
          @foreach (['system','email','sms','slack','webhook'] as $ch)
            <option value="{{ $ch }}" @selected(old('channel', $notification->channel)===$ch)>{{ Str::upper($ch) }}</option>
          @endforeach
        </select>
        @error('channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12">
        <label class="form-label">{{ __('Message') }}</label>
        <textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror" required>{{ old('message', $notification->message) }}</textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      @php($aud = $notification->audience ?? [])
      <div class="col-md-4">
        <label class="form-label">{{ __('Plans (optional)') }}</label>
        <select name="audience[plans][]" class="form-select" multiple>
          @foreach (['starter','growth','premium','enterprise'] as $plan)
            <option value="{{ $plan }}" @selected(in_array($plan, (array)($aud['plans'] ?? []), true))>{{ Str::headline($plan) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Countries (optional)') }}</label>
        <input type="text" name="audience[countries][]" class="form-control" value="{{ isset($aud['countries']) ? implode(', ', (array)$aud['countries']) : '' }}" placeholder="KE, UG, TZ">
      </div>
      <div class="col-md-4">
        <label class="form-label">{{ __('Landlord roles (optional)') }}</label>
        <input type="text" name="audience[landlord_roles][]" class="form-control" value="{{ isset($aud['landlord_roles']) ? implode(', ', (array)$aud['landlord_roles']) : '' }}" placeholder="landlord-admin">
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('Schedule at (optional)') }}</label>
        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ optional($notification->scheduled_at)->format('Y-m-d\TH:i') }}">
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><span class="bi bi-check2 me-1"></span>{{ __('Save') }}</button>
        <a href="{{ route('landlord.notifications.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
