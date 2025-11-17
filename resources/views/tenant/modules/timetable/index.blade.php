@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h5 fw-semibold mb-0">{{ __('Timetable') }}</h1>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive small">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>{{ __('Time') }}</th>
            <th>{{ __('Subject') }}</th>
            <th>{{ __('Room') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($entries as $row)
            <tr>
              <td>{{ $row['time'] }}</td>
              <td>{{ $row['subject'] }}</td>
              <td>{{ $row['room'] }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-secondary">{{ __('No entries yet') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
