@extends('landlord.layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
  <div>
    <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Users') }}</span>
    <h1 class="h3 fw-semibold mb-1">{{ __('Landlord users & access') }}</h1>
    <p class="text-secondary mb-0">{{ __('Search, filter by role, and manage access for central (landlord) users.') }}</p>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="#" class="btn btn-primary btn-sm disabled" title="{{ __('Coming soon') }}">
      <span class="bi bi-person-plus me-2"></span>{{ __('Invite user') }}
    </a>
  </div>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('landlord.users') }}" class="row g-3 align-items-end">
        <div class="col-sm-6 col-lg-4">
          <label for="search" class="form-label">{{ __('Search') }}</label>
          <input type="search" id="search" name="search" class="form-control" placeholder="{{ __('Name, email, phone') }}" value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-sm-6 col-lg-3">
          <label for="role" class="form-label">{{ __('Role') }}</label>
          <select id="role" name="role" class="form-select">
            <option value="">{{ __('All roles') }}</option>
            @foreach ($roles as $r)
              <option value="{{ $r }}" @selected(($filters['role'] ?? '') === $r)>{{ \Illuminate\Support\Str::headline($r) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-lg-2">
          <button type="submit" class="btn btn-outline-secondary w-100">{{ __('Filter') }}</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="text-secondary text-uppercase small">
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Roles') }}</th>
            <th class="text-end">{{ __('Created') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td class="fw-semibold">{{ $user->name }}</td>
              <td class="text-secondary small">{{ $user->email }}</td>
              <td class="text-secondary small">{{ $user->phone ?? '—' }}</td>
              <td>
                @foreach ($user->roles as $role)
                  @if($role->guard_name === 'landlord')
                    <span class="badge text-bg-light text-body border me-1">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                  @endif
                @endforeach
              </td>
              <td class="text-end text-secondary small">{{ optional($user->created_at)->format('M j, Y') }}</td>
              <td class="text-end">
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <span class="bi bi-three-dots"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item disabled" href="#"><span class="bi bi-shield-lock me-2"></span>{{ __('Manage roles') }}</a></li>
                    <li><a class="dropdown-item disabled" href="#"><span class="bi bi-trash me-2"></span>{{ __('Remove user') }}</a></li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-secondary py-5">{{ __('No users found.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($users instanceof \Illuminate\Contracts\Pagination\Paginator)
      <div class="card-footer bg-white border-0">
        {{ $users->withQueryString()->links() }}
      </div>
    @endif
  </div>
@endsection
