@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.modules.teachers._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Teacher') }} #{{ $teacher->id }}</h1>
  <div>
    <a class="btn btn-outline-secondary" href="{{ route('tenant.modules.teachers.edit', $teacher) }}">{{ __('Edit') }}</a>
    <a class="btn btn-light" href="{{ route('tenant.modules.teachers.index') }}">{{ __('Back') }}</a>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <dl class="row">
      <dt class="col-sm-3">{{ __('Name') }}</dt><dd class="col-sm-9">{{ $teacher->name }}</dd>
      <dt class="col-sm-3">{{ __('Email') }}</dt><dd class="col-sm-9">{{ $teacher->email }}</dd>
      <dt class="col-sm-3">{{ __('Phone') }}</dt><dd class="col-sm-9">{{ $teacher->phone }}</dd>
    </dl>
    <form class="mt-3" action="{{ route('tenant.modules.teachers.destroy', $teacher) }}" method="post" onsubmit="return confirm('{{ __('Delete this teacher?') }}')">
      @csrf
      @method('DELETE')
      <button class="btn btn-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</div>
@endsection
