@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h4 fw-semibold mb-0">{{ __('Salary Scale Details') }}</h1>
      <div class="small text-secondary">{{ __('View salary scale information') }}</div>
    </div>
    <div>
      <a href="{{ route('tenant.modules.human_resources.salary_scales.edit', $salaryScale) }}" class="btn btn-warning btn-sm">{{ __('Edit') }}</a>
      <a href="{{ route('tenant.modules.human_resources.salary_scales.index') }}" class="btn btn-secondary btn-sm">{{ __('Back to List') }}</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">{{ __('Name') }}</dt>
        <dd class="col-sm-9">{{ $salaryScale->name }}</dd>

        <dt class="col-sm-3">{{ __('Grade') }}</dt>
        <dd class="col-sm-9">{{ $salaryScale->grade ?: '-' }}</dd>

        <dt class="col-sm-3">{{ __('Minimum Amount') }}</dt>
        <dd class="col-sm-9">{{ number_format($salaryScale->min_amount) }}</dd>

        <dt class="col-sm-3">{{ __('Maximum Amount') }}</dt>
        <dd class="col-sm-9">{{ number_format($salaryScale->max_amount) }}</dd>

        <dt class="col-sm-3">{{ __('Notes') }}</dt>
        <dd class="col-sm-9">{{ $salaryScale->notes ?: '-' }}</dd>

        <dt class="col-sm-3">{{ __('Created At') }}</dt>
        <dd class="col-sm-9">{{ $salaryScale->created_at->format('M d, Y H:i') }}</dd>

        <dt class="col-sm-3">{{ __('Updated At') }}</dt>
        <dd class="col-sm-9">{{ $salaryScale->updated_at->format('M d, Y H:i') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
