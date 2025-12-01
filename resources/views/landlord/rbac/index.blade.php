@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Access Control') }}</span>
            <h1 class="h3 fw-semibold mb-1">{{ __('Roles & Permissions') }}</h1>
            <p class="text-secondary mb-0">
                {{ __('Manage system roles and define access permissions for the landlord panel.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <span class="bi bi-plus-lg me-2"></span>{{ __('Create Role') }}
            </button>
        </div>
    </div>

    <div class="row g-4">
        @foreach ($roles as $role)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-shield-lock fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1">{{ $role->name }}</h5>
                                    <span class="text-secondary small">{{ $role->permissions->count() }}
                                        {{ __('permissions') }}</span>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal{{ $role->id }}">
                                            <i class="bi bi-pencil me-2"></i>{{ __('Edit Role') }}
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#permissionsModal{{ $role->id }}">
                                            <i class="bi bi-shield-check me-2"></i>{{ __('Manage Permissions') }}
                                        </button>
                                    </li>
                                    @if (!in_array($role->name, ['Super Admin', 'Admin']))
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('landlord.rbac.destroy', $role->id) }}" method="POST"
                                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>{{ __('Delete Role') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($role->permissions->take(5) as $perm)
                                    <span class="badge bg-light text-secondary border">{{ $perm->name }}</span>
                                @empty
                                    <span class="text-muted small fst-italic">{{ __('No permissions assigned') }}</span>
                                @endforelse
                                @if ($role->permissions->count() > 5)
                                    <span
                                        class="badge bg-light text-secondary border">+{{ $role->permissions->count() - 5 }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                            data-bs-target="#permissionsModal{{ $role->id }}">
                            {{ __('Configure Access') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Role Modal -->
            <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('landlord.rbac.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Edit Role') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Role Name') }}</label>
                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light"
                                    data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Permissions Modal -->
            <div class="modal fade" id="permissionsModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('landlord.rbac.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="permissions_submitted" value="1">
                            <div class="modal-header bg-light">
                                <div>
                                    <h5 class="modal-title">{{ __('Manage Permissions') }}</h5>
                                    <p class="text-secondary small mb-0">{{ __('Role:') }} <span
                                            class="fw-bold text-dark">{{ $role->name }}</span></p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="accordion accordion-flush" id="accordionPermissions{{ $role->id }}">
                                    @foreach ($permissions as $module => $modulePermissions)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed bg-light-subtle" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $role->id }}-{{ \Illuminate\Support\Str::slug($module) }}">
                                                    <span class="fw-semibold text-capitalize">{{ $module }}</span>
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary ms-2">{{ $modulePermissions->count() }}</span>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $role->id }}-{{ \Illuminate\Support\Str::slug($module) }}"
                                                class="accordion-collapse collapse"
                                                data-bs-parent="#accordionPermissions{{ $role->id }}">
                                                <div class="accordion-body">
                                                    <div class="row g-3">
                                                        @foreach ($modulePermissions as $perm)
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="permissions[]" value="{{ $perm->name }}"
                                                                        id="perm{{ $role->id }}{{ $perm->id }}"
                                                                        {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="perm{{ $role->id }}{{ $perm->id }}">
                                                                        {{ $perm->name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <div class="d-flex justify-content-between w-100 align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            id="selectAll{{ $role->id }}"
                                            onclick="toggleAllPermissions(this, '{{ $role->id }}')">
                                        <label class="form-check-label small text-secondary"
                                            for="selectAll{{ $role->id }}">{{ __('Select All') }}</label>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('Save Permissions') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('landlord.rbac.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Create New Role') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Role Name') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Support Manager"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAllPermissions(source, roleId) {
            const checkboxes = document.querySelectorAll(`#permissionsModal${roleId} input[name="permissions[]"]`);
            checkboxes.forEach(cb => cb.checked = source.checked);
        }
    </script>
@endsection
