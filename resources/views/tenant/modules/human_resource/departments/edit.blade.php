@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container mt-4">
        <h1 class="h4 mb-3">{{ __('Edit Department') }}</h1>
        <form method="POST" action="{{ route('tenant.modules.human-resource.departments.update', $department) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $department->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="code" class="form-label">{{ __('Code') }}</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code"
                    value="{{ old('code', $department->code) }}">
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea class="form-control wysiwyg-editor @error('description') is-invalid @enderror" id="description"
                    name="description" placeholder="{{ __('Enter department description...') }}">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            <a href="{{ route('tenant.modules.human-resource.departments.index') }}"
                class="btn btn-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>

    @include('components.wysiwyg')
@endsection
