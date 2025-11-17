@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <a class="btn btn-light" href="{{ route('tenant.modules.grades.index') }}">{{ __('Back') }}</a>
  <h1 class="h5 fw-semibold mb-0">{{ __('Grade details') }}</h1>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">{{ __('Student') }}</dt><dd class="col-sm-9">{{ optional($grade->student)->name ?: '—' }}</dd>
      <dt class="col-sm-3">{{ __('Subject') }}</dt><dd class="col-sm-9">{{ optional($grade->subject)->name ?: '—' }}</dd>
      <dt class="col-sm-3">{{ __('Score') }}</dt><dd class="col-sm-9">{{ $grade->score }}</dd>
      <dt class="col-sm-3">{{ __('Band') }}</dt><dd class="col-sm-9">{{ $grade->band_label ?: $grade->band_code ?: '—' }}</dd>
      <dt class="col-sm-3">{{ __('Teacher') }}</dt><dd class="col-sm-9">{{ optional($grade->teacher)->name ?: '—' }}</dd>
      <dt class="col-sm-3">{{ __('Term') }}</dt><dd class="col-sm-9">{{ $grade->term ?: '—' }}</dd>
      <dt class="col-sm-3">{{ __('Awarded on') }}</dt><dd class="col-sm-9">{{ optional($grade->awarded_on) ? $grade->awarded_on->toDateString() : '—' }}</dd>
    </dl>
  </div>
</div>
@endsection
