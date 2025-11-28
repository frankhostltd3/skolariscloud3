@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <h1 class="h4 mb-4">{{ __('Edit Leave Type') }}</h1>
        <form method="POST" action="{{ route('tenant.modules.human-resource.leave-types.update', $leaveType) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $leaveType->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="code" class="form-label">{{ __('Code') }}</label>
                <input type="text" class="form-control" id="code" name="code"
                    value="{{ old('code', $leaveType->code) }}" maxlength="10" required>
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="default_days" class="form-label">{{ __('Default Days') }}</label>
                <input type="number" class="form-control" id="default_days" name="default_days"
                    value="{{ old('default_days', $leaveType->default_days) }}" min="0" max="365" required>
                @error('default_days')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="requires_approval" name="requires_approval"
                    value="1" {{ old('requires_approval', $leaveType->requires_approval) ? 'checked' : '' }}>
                <label for="requires_approval" class="form-check-label">{{ __('Requires Approval') }}</label>
                @error('requires_approval')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('Description') }}</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $leaveType->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            <a href="{{ route('tenant.modules.human-resource.leave-types.index') }}"
                class="btn btn-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#description').summernote({
                placeholder: 'Enter description...',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
@endpush
