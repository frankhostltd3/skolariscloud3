@extends('tenant.layouts.app')

@section('title', __('Generated Employee ID'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('Employee ID Generated') }}</h4>
                    <div>
                        <a href="{{ route('tenant.modules.human_resources.employee-ids.download.svg', ['employee_id' => $employee->id, 'template_id' => $template->id]) }}"
                           class="btn btn-success btn-sm me-2" target="_blank">
                            <i class="fas fa-download"></i> {{ __('Download SVG') }}
                        </a>
                        <a href="{{ route('tenant.modules.human_resources.employee-ids.download.png', ['employee_id' => $employee->id, 'template_id' => $template->id]) }}"
                           class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-download"></i> {{ __('Download PNG') }}
                        </a>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="id-preview-container mb-4">
                        {!! $svgContent !!}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('Employee Information') }}</h6>
                                </div>
                                <div class="card-body text-start">
                                    <p><strong>{{ __('Name') }}:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
                                    <p><strong>{{ __('Employee ID') }}:</strong> {{ $employee->id }}</p>
                                    <p><strong>{{ __('Department') }}:</strong> {{ $employee->department?->name ?? __('N/A') }}</p>
                                    <p><strong>{{ __('Position') }}:</strong> {{ $employee->position?->name ?? __('N/A') }}</p>
                                    <p><strong>{{ __('Email') }}:</strong> {{ $employee->email ?? __('N/A') }}</p>
                                    <p><strong>{{ __('Phone') }}:</strong> {{ $employee->phone ?? __('N/A') }}</p>
                                    <p><strong>{{ __('Hire Date') }}:</strong> {{ $employee->hire_date?->format('M d, Y') ?? __('N/A') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('QR Code Data') }}</h6>
                                </div>
                                <div class="card-body text-start">
                                    <p><small class="text-muted">{{ __('The QR code contains the following information:') }}</small></p>
                                    <pre class="bg-white p-2 rounded border small">{{ json_encode(json_decode($qrData), JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('tenant.modules.human_resources.employee-ids.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Generate Another ID') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.id-preview-container {
    max-width: 400px;
    margin: 0 auto;
}

.id-preview-container svg {
    width: 100%;
    height: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection