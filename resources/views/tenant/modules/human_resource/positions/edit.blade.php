@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container mt-4">
  <h1 class="h4 mb-3">{{ __('Edit Position') }}</h1>
  <form method="POST" action="{{ route('tenant.modules.human_resources.positions.update', $position) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label for="title" class="form-label">{{ __('Title') }}</label>
      <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $position->title) }}" required>
      @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="mb-3">
      <label for="department_id" class="form-label">{{ __('Department') }}</label>
      <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
        <option value="">-- {{ __('Select department') }} --</option>
        @foreach($departments as $department)
          <option value="{{ $department->id }}" {{ old('department_id', $position->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
        @endforeach
      </select>
      @error('department_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="mb-3">
      <label for="code" class="form-label">{{ __('Code') }}</label>
      <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $position->code) }}">
      @error('code')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">{{ __('Description') }}</label>
      <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $position->description) }}</textarea>
      @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
    <a href="{{ route('tenant.modules.human_resources.positions.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
  </form>
</div>
@endsection
