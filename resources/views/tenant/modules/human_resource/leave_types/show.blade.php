@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h1 class="h4 fw-semibold mb-0">{{ __('Leave Type Details') }}</h1>
      <div class="small text-secondary">{{ __('View leave type information') }}</div>
    </div>
    <div>
      <a href="{{ route('tenant.modules.human_resources.leave_types.edit', $leaveType) }}" class="btn btn-warning btn-sm">{{ __('Edit') }}</a>
      <a href="{{ route('tenant.modules.human_resources.leave_types.index') }}" class="btn btn-secondary btn-sm">{{ __('Back to List') }}</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">{{ __('Name') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->name }}</dd>

        <dt class="col-sm-3">{{ __('Code') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->code }}</dd>

        <dt class="col-sm-3">{{ __('Default Days') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->default_days }}</dd>

        <dt class="col-sm-3">{{ __('Requires Approval') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->requires_approval ? __('Yes') : __('No') }}</dd>

        <dt class="col-sm-3">{{ __('Description') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->description ?: '-' }}</dd>

        <dt class="col-sm-3">{{ __('Created At') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->created_at->format('M d, Y H:i') }}</dd>

        <dt class="col-sm-3">{{ __('Updated At') }}</dt>
        <dd class="col-sm-9">{{ $leaveType->updated_at->format('M d, Y H:i') }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection
