@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container mt-4">
  <h1 class="h4 mb-3">{{ __('Position Details') }}</h1>
  <div class="card">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">{{ __('Title') }}</dt>
        <dd class="col-sm-9">{{ $position->title }}</dd>
        <dt class="col-sm-3">{{ __('Department') }}</dt>
        <dd class="col-sm-9">{{ $position->department ? $position->department->name : '' }}</dd>
        <dt class="col-sm-3">{{ __('Code') }}</dt>
        <dd class="col-sm-9">{{ $position->code }}</dd>
        <dt class="col-sm-3">{{ __('Description') }}</dt>
        <dd class="col-sm-9">{{ $position->description }}</dd>
      </dl>
    </div>
  </div>
  <div class="mt-3">
    <a href="{{ route('tenant.modules.human_resources.positions.edit', $position) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a>
    <a href="{{ route('tenant.modules.human_resources.positions.index') }}" class="btn btn-secondary btn-sm">{{ __('Back to list') }}</a>
  </div>
</div>
@endsection
