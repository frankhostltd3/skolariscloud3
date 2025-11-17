@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container mt-4">
  <h1 class="h4 mb-3">{{ __('Department Details') }}</h1>
  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">{{ __('Name') }}</dt>
        <dd class="col-sm-9">{{ $department->name }}</dd>
        <dt class="col-sm-3">{{ __('Code') }}</dt>
        <dd class="col-sm-9">{{ $department->code }}</dd>
        <dt class="col-sm-3">{{ __('Description') }}</dt>
        <dd class="col-sm-9">{{ $department->description }}</dd>
        <dt class="col-sm-3">{{ __('Members') }}</dt>
        <dd class="col-sm-9">{{ $department->employees()->count() }}</dd>
      </dl>
      <div class="mt-3">
        <a href="{{ route('tenant.modules.human_resources.departments.edit', $department) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a>
        <a href="{{ route('tenant.modules.human_resources.departments.index') }}" class="btn btn-secondary btn-sm">{{ __('Back to list') }}</a>
      </div>
    </div>
  </div>
</div>
@endsection
