@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <h1 class="h4 mb-4">{{ __('Edit Leave Type') }}</h1>
  <form method="POST" action="{{ route('tenant.modules.human_resources.leave_types.update', $leaveType) }}">
    @csrf
    @method('PATCH')
    <div class="mb-3">
      <label for="name" class="form-label">{{ __('Name') }}</label>
      <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $leaveType->name) }}" required>
      @error('name') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
      <label for="code" class="form-label">{{ __('Code') }}</label>
      <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $leaveType->code) }}" maxlength="10" required>
      @error('code') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
      <label for="default_days" class="form-label">{{ __('Default Days') }}</label>
      <input type="number" class="form-control" id="default_days" name="default_days" value="{{ old('default_days', $leaveType->default_days) }}" min="0" max="365" required>
      @error('default_days') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3 form-check">
      <input type="checkbox" class="form-check-input" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval', $leaveType->requires_approval) ? 'checked' : '' }}>
      <label for="requires_approval" class="form-check-label">{{ __('Requires Approval') }}</label>
      @error('requires_approval') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">{{ __('Description') }}</label>
      <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $leaveType->description) }}</textarea>
      @error('description') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
    <a href="{{ route('tenant.modules.human_resources.leave_types.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection