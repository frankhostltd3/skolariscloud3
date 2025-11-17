<div class="id-preview-container">
    {!! $svgContent !!}
</div>

<div class="mt-3">
    <h6>{{ __('Student ID Preview') }}</h6>
    <p class="mb-1"><strong>{{ __('Name') }}:</strong> {{ $student->name }}</p>
    <p class="mb-1"><strong>{{ __('Admission No') }}:</strong> {{ $student->admission_no }}</p>
    <p class="mb-0"><strong>{{ __('Email') }}:</strong> {{ $student->email ?? __('N/A') }}</p>
</div>
