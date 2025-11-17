<div class="id-preview-container">
    {!! $svgContent !!}
</div>

<div class="mt-3">
    <h6>{{ __('Employee ID Preview') }}</h6>
    <p class="mb-1"><strong>{{ __('Name') }}:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
    <p class="mb-1"><strong>{{ __('Employee ID') }}:</strong> {{ $employee->id }}</p>
    <p class="mb-1"><strong>{{ __('Department') }}:</strong> {{ $employee->department?->name ?? __('N/A') }}</p>
    <p class="mb-0"><strong>{{ __('Position') }}:</strong> {{ $employee->position?->name ?? __('N/A') }}</p>
</div>