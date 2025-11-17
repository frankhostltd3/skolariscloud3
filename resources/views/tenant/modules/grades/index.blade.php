@extends('tenant.layouts.app')

@section('content')
@php($currentBody = \App\Models\ExaminationBody::where('is_current', true)->first())
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h5 fw-semibold mb-0">{{ __('Grades') }}</h1>
    @if($currentBody)
      <div class="small text-secondary">{{ __('Exam body') }}: {{ $currentBody->name_translations[app()->getLocale()] ?? $currentBody->name }} ({{ $currentBody->code }})</div>
    @endif
  </div>
  <div class="small text-secondary">{{ __('Last updated') }}: {{ now()->toDayDateTimeString() }}</div>
  
</div>

@can('manage grades')
  <div class="alert alert-warning small">{{ __('You have permission to manage grades.') }}</div>
@endcan

<div class="card shadow-sm">
  <div class="card-body">
    @if($currentBody)
      <div class="mb-3">
        <form method="get" class="row g-2 align-items-center">
          <div class="col-auto">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="onlyAligned" name="only_aligned" value="1" disabled>
              <label class="form-check-label" for="onlyAligned">{{ __('Only show results for subjects aligned to current exam body') }}</label>
            </div>
          </div>
          <div class="col-auto">
            <span class="text-muted small">{{ __('Coming soon') }}</span>
          </div>
        </form>
      </div>
    @endif
    <h2 class="h6 fw-semibold mb-3">{{ __('Recent results') }}</h2>
    <div class="table-responsive small">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>{{ __('Course') }}</th>
            <th>{{ __('Grade') }}</th>
            <th>{{ __('Band') }}</th>
            <th>{{ __('Date') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recent as $row)
            <tr>
              <td>
                @if(isset($row['id']))
                  <a href="{{ route('tenant.modules.grades.show', $row['id']) }}">{{ $row['course'] }}</a>
                @else
                  {{ $row['course'] }}
                @endif
              </td>
              <td>{{ $row['grade'] }}</td>
              <td>{{ $row['band'] ?? '—' }}</td>
              <td>{{ $row['date'] }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-secondary">{{ __('No data yet') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
