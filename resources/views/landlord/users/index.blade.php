@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Users') }}</span>
            <h1 class="h3 fw-semibold mb-1">{{ __('Landlord users & access') }}</h1>
            <p class="text-secondary mb-0">
                {{ __('Search, filter by role, and manage access for central (landlord) users.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteUserModal">
                <span class="bi bi-person-plus me-2"></span>{{ __('Invite user') }}
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">{{ __('Please fix the following issues:') }}</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('landlord.users') }}" class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input type="search" id="search" name="search" class="form-control"
                        placeholder="{{ __('Name, email, phone') }}" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="role" class="form-label">{{ __('Role') }}</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">{{ __('All roles') }}</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r }}" @selected(($filters['role'] ?? '') === $r)>
                                {{ \Illuminate\Support\Str::headline($r) }}</option>
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
                                    @if ($role->guard_name === 'landlord')
                                        <span
                                            class="badge text-bg-light text-body border me-1">{{ \Illuminate\Support\Str::headline($role->name) }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-end text-secondary small">{{ optional($user->created_at)->format('M j, Y') }}
                            </td>
                            <td class="text-end">
                                @php $isSelf = auth('landlord')->id() === $user->id; @endphp
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <span class="bi bi-three-dots"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item" type="button" data-bs-toggle="modal"
                                                data-bs-target="#manageRolesModal" data-user-name="{{ $user->name }}"
                                                data-update-url="{{ route('landlord.users.roles.update', $user) }}"
                                                data-user-roles='@json($user->roles->pluck('name')->values())'>
                                                <span class="bi bi-shield-lock me-2"></span>{{ __('Manage roles') }}
                                            </button>
                                        </li>
                                        <li>
                                            @if ($isSelf)
                                                <span class="dropdown-item text-secondary small opacity-75">
                                                    <span
                                                        class="bi bi-ban me-2"></span>{{ __('Cannot remove current user') }}
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('landlord.users.destroy', $user) }}"
                                                    onsubmit="return confirm('{{ __('Remove :name from landlord access?', ['name' => $user->name]) }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <span class="bi bi-trash me-2"></span>{{ __('Remove user') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
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

    <!-- Invite User Modal -->
    <div class="modal fade" id="inviteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Invite landlord user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <form method="POST" action="{{ route('landlord.users.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="invite-name" class="form-label">{{ __('Full name') }}</label>
                            <input type="text" class="form-control" id="invite-name" name="name"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="invite-email" class="form-label">{{ __('Email address') }}</label>
                            <input type="email" class="form-control" id="invite-email" name="email"
                                value="{{ old('email') }}" required>
                            <small
                                class="text-secondary">{{ __('We will email a password setup link to this address.') }}</small>
                        </div>
                        <div class="mb-3">
                            <label for="invite-phone" class="form-label">{{ __('Phone (optional)') }}</label>
                            <input type="text" class="form-control" id="invite-phone" name="phone"
                                value="{{ old('phone') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Assign roles') }}</label>
                            <div class="d-flex flex-column gap-2">
                                @forelse ($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $role }}"
                                            id="invite-role-{{ $loop->index }}" name="roles[]"
                                            @checked(collect(old('roles', []))->contains($role))>
                                        <label class="form-check-label"
                                            for="invite-role-{{ $loop->index }}">{{ \Illuminate\Support\Str::headline($role) }}</label>
                                    </div>
                                @empty
                                    <div class="alert alert-warning mb-0">
                                        {{ __('No roles found. Create landlord roles first.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="bi bi-send me-2"></span>{{ __('Send invite') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Roles Modal -->
    <div class="modal fade" id="manageRolesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Manage roles for') }} <span class="fw-semibold"
                            id="manageRolesUserName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <form method="POST" id="manageRolesForm">
                    @csrf
                    <div class="modal-body">
                        @if (count($roles))
                            <p class="text-secondary small">{{ __('Select the landlord roles this user should have.') }}
                            </p>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input role-checkbox" type="checkbox"
                                            value="{{ $role }}" id="role-{{ $loop->index }}" name="roles[]">
                                        <label class="form-check-label"
                                            for="role-{{ $loop->index }}">{{ \Illuminate\Support\Str::headline($role) }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">{{ __('No landlord roles have been defined yet.') }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" @if (!count($roles)) disabled @endif>
                            <span class="bi bi-save me-2"></span>{{ __('Save changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rolesModal = document.getElementById('manageRolesModal');

            rolesModal?.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                const userName = trigger.getAttribute('data-user-name') || '';
                const updateUrl = trigger.getAttribute('data-update-url');
                const rawRoles = trigger.getAttribute('data-user-roles') || '[]';
                let assignedRoles = [];
                try {
                    assignedRoles = JSON.parse(rawRoles);
                } catch (error) {
                    assignedRoles = [];
                }

                const nameTarget = document.getElementById('manageRolesUserName');
                if (nameTarget) {
                    nameTarget.textContent = userName;
                }

                const form = document.getElementById('manageRolesForm');
                form?.setAttribute('action', updateUrl || '#');

                form?.querySelectorAll('.role-checkbox').forEach((checkbox) => {
                    checkbox.checked = assignedRoles.includes(checkbox.value);
                });
            });

            const showInviteModal = {{ session('showInviteModal', false) ? 'true' : 'false' }};
            if (showInviteModal) {
                const inviteModalEl = document.getElementById('inviteUserModal');
                if (inviteModalEl) {
                    const inviteModal = new bootstrap.Modal(inviteModalEl);
                    inviteModal.show();
                }
            }
        });
    </script>
@endpush
