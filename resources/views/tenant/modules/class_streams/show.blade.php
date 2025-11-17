@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Class Stream Details') }}</h1>
  <div>
    <a class="btn btn-light me-2" href="{{ route('tenant.modules.class_streams.index') }}">{{ __('Back') }}</a>
    @can('update', $class_stream)
      <a class="btn btn-primary" href="{{ route('tenant.modules.class_streams.edit', $class_stream) }}">{{ __('Edit') }}</a>
    @endcan
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card shadow-sm">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">{{ __('Class') }}</dt>
      <dd class="col-sm-9">{{ $class_stream->class?->name }}</dd>
      <dt class="col-sm-3">{{ __('Stream Name') }}</dt>
      <dd class="col-sm-9">{{ $class_stream->name }}</dd>
      <dt class="col-sm-3">{{ __('Created') }}</dt>
      <dd class="col-sm-9">{{ $class_stream->created_at?->format('Y-m-d H:i') }}</dd>
      <dt class="col-sm-3">{{ __('Updated') }}</dt>
      <dd class="col-sm-9">{{ $class_stream->updated_at?->format('Y-m-d H:i') }}</dd>
    </dl>
  </div>
</div>
@endsection
