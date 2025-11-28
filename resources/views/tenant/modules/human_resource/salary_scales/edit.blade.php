@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('content')
    <div class="container">
        <h1 class="h4 mb-4">{{ __('Edit Salary Scale') }}</h1>
        <form method="POST" action="{{ route('tenant.modules.human-resource.salary-scales.update', $salaryScale) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Position') }}</label>
                <select class="form-select" id="name" name="name" required>
                    <option value="">{{ __('Select Position') }}</option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->title }}"
                            {{ old('name', $salaryScale->name) == $position->title ? 'selected' : '' }}>
                            {{ $position->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="grade" class="form-label">{{ __('Grade') }}</label>
                <input type="text" class="form-control" id="grade" name="grade"
                    value="{{ old('grade', $salaryScale->grade) }}">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="min_amount" class="form-label">{{ __('Minimum Amount') }}</label>
                    <input type="number" class="form-control" id="min_amount" name="min_amount"
                        value="{{ old('min_amount', $salaryScale->min_amount) }}" step="0.01">
                </div>
                <div class="col-md-6">
                    <label for="max_amount" class="form-label">{{ __('Maximum Amount') }}</label>
                    <input type="number" class="form-control" id="max_amount" name="max_amount"
                        value="{{ old('max_amount', $salaryScale->max_amount) }}" step="0.01">
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                <textarea class="form-control" id="notes" name="notes">{{ old('notes', $salaryScale->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            <a href="{{ route('tenant.modules.human-resource.salary-scales.index') }}"
                class="btn btn-secondary">{{ __('Cancel') }}</a>
        </form>
    </div>

    <!-- Summernote CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#notes').summernote({
                placeholder: 'Enter notes here...',
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
@endsection
