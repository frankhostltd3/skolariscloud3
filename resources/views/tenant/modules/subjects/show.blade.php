@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Subject') }} #{{ $subject->id }}</h1>
  <div>
    <a class="btn btn-outline-secondary" href="{{ route('tenant.modules.subjects.edit', $subject) }}">{{ __('Edit') }}</a>
    <a class="btn btn-light" href="{{ route('tenant.modules.subjects.index') }}">{{ __('Back') }}</a>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <dl class="row">
      <dt class="col-sm-3">{{ __('Code') }}</dt><dd class="col-sm-9">{{ $subject->code }}</dd>
      <dt class="col-sm-3">{{ __('Name') }}</dt><dd class="col-sm-9">{{ $subject->name }}</dd>
    </dl>
    <form class="mt-3" action="{{ route('tenant.modules.subjects.destroy', $subject) }}" method="post" onsubmit="return confirm('{{ __('Delete this subject?') }}')">
      @csrf
      @method('DELETE')
      <button class="btn btn-danger">{{ __('Delete') }}</button>
    </form>
  </div>
</div>
@endsection
