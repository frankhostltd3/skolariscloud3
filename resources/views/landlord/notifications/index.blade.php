@extends('landlord.layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
  <div>
    <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Notifications') }}</span>
    <h1 class="h3 fw-semibold mb-1">{{ __('Platform notifications') }}</h1>
    <p class="text-secondary mb-0">{{ __('Create, schedule, and view notifications to tenants or internal users.') }}</p>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="#" class="btn btn-primary btn-sm disabled" title="{{ __('Coming soon') }}">
      <span class="bi bi-plus-lg me-2"></span>{{ __('New notification') }}
    </a>
  </div>
</div>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <form method="get" class="row g-3 align-items-end">
      <div class="col-sm-6 col-lg-3">
        <label class="form-label">{{ __('Channel') }}</label>
        <select name="channel" class="form-select">
          <option value="">{{ __('All') }}</option>
          @foreach (['system','email','sms','slack','webhook'] as $ch)
            <option value="{{ $ch }}" @selected(request('channel')===$ch)>{{ \Illuminate\Support\Str::upper($ch) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-6 col-lg-3">
        <label class="form-label">{{ __('Status') }}</label>
        <select name="status" class="form-select">
          <option value="">{{ __('All') }}</option>
          <option value="draft" @selected(request('status')==='draft')>{{ __('Draft') }}</option>
          <option value="scheduled" @selected(request('status')==='scheduled')>{{ __('Scheduled') }}</option>
          <option value="sent" @selected(request('status')==='sent')>{{ __('Sent') }}</option>
        </select>
      </div>
      <div class="col-lg-2">
        <button class="btn btn-outline-secondary w-100">{{ __('Filter') }}</button>
      </div>
    </form>
  </div>
  </div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="text-secondary text-uppercase small">
        <tr>
          <th>{{ __('Title') }}</th>
          <th>{{ __('Channel') }}</th>
          <th>{{ __('Scheduled') }}</th>
          <th>{{ __('Sent') }}</th>
          <th>{{ __('Creator') }}</th>
          <th class="text-end">{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($notifications as $n)
          <tr>
            <td class="fw-semibold">{{ $n->title }}</td>
            <td><span class="badge text-bg-light text-body border">{{ \Illuminate\Support\Str::upper($n->channel) }}</span></td>
            <td class="text-secondary small">{{ optional($n->scheduled_at)->format('Y-m-d H:i') ?? '—' }}</td>
            <td class="text-secondary small">{{ optional($n->sent_at)->format('Y-m-d H:i') ?? '—' }}</td>
            <td class="text-secondary small">{{ $n->creator?->name ?? '—' }}</td>
            <td class="text-end">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <span class="bi bi-three-dots"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item disabled" href="#"><span class="bi bi-eye me-2"></span>{{ __('View') }}</a></li>
                  <li><a class="dropdown-item disabled" href="#"><span class="bi bi-pencil me-2"></span>{{ __('Edit') }}</a></li>
                </ul>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-secondary py-5">{{ __('No notifications found.') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if ($notifications instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="card-footer bg-white border-0">
      {{ $notifications->withQueryString()->links() }}
    </div>
  @endif
</div>
@endsection
