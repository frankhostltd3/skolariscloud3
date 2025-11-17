@extends('landlord.layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
  <div class="card-body p-4 p-lg-5">
    <h1 class="h4 fw-semibold mb-3">{{ __('Audit logs') }}</h1>
    <p class="text-secondary mb-4">{{ __('Review important activity across the platform.') }}</p>

    <form method="get" class="row g-3 mb-4">
      <div class="col-12 col-md-3">
        <label class="form-label fw-semibold">{{ __('Date from') }}</label>
        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label fw-semibold">{{ __('Date to') }}</label>
        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label fw-semibold">{{ __('User') }}</label>
        <input type="text" name="user" class="form-control" placeholder="email or id" value="{{ request('user') }}">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label fw-semibold">{{ __('Action') }}</label>
        <input type="text" name="action" class="form-control" placeholder="e.g. login, update" value="{{ request('action') }}">
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary"><span class="bi bi-search me-1"></span>{{ __('Filter') }}</button>
        <a href="{{ route('landlord.audit') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
  <a href="{{ route('landlord.audit.export', request()->query()) }}" class="btn btn-outline-primary">{{ __('Export CSV') }}</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>{{ __('When') }}</th>
            <th>{{ __('User') }}</th>
            <th>{{ __('Action') }}</th>
            <th>{{ __('IP') }}</th>
            <th>{{ __('Context') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs ?? collect() as $log)
            <tr>
              <td class="text-secondary small">{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
              <td class="small">{{ $log->user?->name ?? '—' }} <div class="text-secondary">{{ $log->user?->email }}</div></td>
              <td><span class="badge text-bg-light text-body border">{{ $log->action }}</span></td>
              <td class="text-secondary small">{{ $log->ip_address ?? '—' }}</td>
              <td class="small text-break">
                @php($ctx = $log->context ?? [])
                @if(!empty($ctx))
                  <details>
                    <summary class="text-secondary">{{ __('View') }}</summary>
                    <pre class="mb-0 small">{{ json_encode($ctx, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                  </details>
                @else
                  <span class="text-secondary">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-secondary">{{ __('No audit logs found.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if(($logs ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
      <div class="mt-3">
        {{ $logs->withQueryString()->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
