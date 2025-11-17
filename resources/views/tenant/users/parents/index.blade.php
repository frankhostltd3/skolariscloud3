@extends('tenant.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            @include('tenant.admin._sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h4 fw-semibold mb-0">
                    <i class="bi bi-people me-2"></i>{{ __('Parents & Guardians') }}
                </h1>
                <a class="btn btn-primary" href="{{ route('tenant.users.parents.create') }}">
                    <i class="bi bi-plus-circle me-2"></i>{{ __('Add Parent/Guardian') }}
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="get" class="row g-2 mb-3">
                        <div class="col-auto">
                            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" 
                                   placeholder="{{ __('Search name, email, phone') }}" />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search me-1"></i>{{ __('Search') }}
                            </button>
                            @if (!empty($q))
                                <a class="btn btn-link" href="{{ route('tenant.users.parents') }}">{{ __('Clear') }}</a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Children') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    @php
                                        $profile = $user->parentProfile;
                                        $statusClass = $user->is_active ? 'text-success' : 'text-danger';
                                        $statusIcon = $user->is_active ? 'bi-check-circle' : 'bi-x-circle';
                                        $statusText = $user->is_active ? __('Active') : __('Inactive');
                                    @endphp
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <a href="{{ route('tenant.users.parents.show', $user) }}" class="text-decoration-none">
                                                <strong>{{ $profile?->full_name ?? $user->name }}</strong>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                        </td>
                                        <td>
                                            @if($profile?->phone)
                                                <a href="tel:{{ $profile->phone }}">{{ $profile->phone }}</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($profile && $profile->students->count() > 0)
                                                <span class="badge bg-primary">{{ $profile->students->count() }} {{ __('Student(s)') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $statusClass }}">
                                                <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusText }}
                                            </span>
                                            @if($profile && $profile->status === 'inactive')
                                                <span class="badge bg-warning text-dark ms-1">{{ __('Profile Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-outline-secondary" 
                                                   href="{{ route('tenant.users.parents.show', $user) }}" 
                                                   title="{{ __('View') }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-primary" 
                                                   href="{{ route('tenant.users.parents.edit', $user) }}" 
                                                   title="{{ __('Edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                @if($user->is_active)
                                                    <button type="button" class="btn btn-outline-warning" 
                                                            onclick="deactivateUser({{ $user->id }})" 
                                                            title="{{ __('Deactivate') }}">
                                                        <i class="bi bi-pause-circle"></i>
                                                    </button>
                                                @else
                                                    <form action="{{ route('tenant.users.parents.activate', $user) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" 
                                                                title="{{ __('Activate') }}">
                                                            <i class="bi bi-play-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('tenant.users.parents.destroy', $user) }}" 
                                                      method="post" class="d-inline" 
                                                      onsubmit="return confirm('{{ __('Delete this parent/guardian?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger" type="submit" title="{{ __('Delete') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            {{ __('No parents/guardians yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivation Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deactivateForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Deactivate Parent/Guardian') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="deactivation_reason" class="form-label">{{ __('Reason for deactivation (optional)') }}</label>
                        <textarea class="form-control" id="deactivation_reason" name="reason" rows="3" 
                                  placeholder="{{ __('Enter reason...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('Deactivate') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deactivateUser(userId) {
    const modal = new bootstrap.Modal(document.getElementById('deactivateModal'));
    const form = document.getElementById('deactivateForm');
    form.action = `/users/parents/${userId}/deactivate`;
    modal.show();
}
</script>
@endpush
@endsection
