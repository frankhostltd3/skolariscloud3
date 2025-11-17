@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.admin._sidebar')
@endsection

@section('title', __('User Password Management'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">
                <i class="bi bi-shield-lock me-2"></i>{{ __('User Password Management') }}
            </h1>
            <p class="text-muted mb-0">{{ __('Reset and manage user passwords') }}</p>
        </div>
    </div>

    <!-- Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Temporary Password Display (Show Only Once) -->
    @if(session('temp_password'))
        <div class="alert alert-warning border-0 shadow-sm">
            <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Temporary Password Generated') }}
            </h5>
            <p class="mb-3">{{ __('The following temporary password has been generated for') }} <strong>{{ session('temp_user_name') }}</strong> ({{ session('temp_user_email') }}):</p>
            
            <div class="card bg-dark text-light mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <code class="fs-4" id="tempPasswordDisplay">{{ session('temp_password') }}</code>
                        <button class="btn btn-sm btn-outline-light" onclick="copyTempPassword()">
                            <i class="bi bi-clipboard me-1"></i>{{ __('Copy') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert alert-danger mb-0">
                <i class="bi bi-shield-exclamation me-2"></i>
                <strong>{{ __('Important:') }}</strong> {{ __('This password will only be shown once. Please save it securely or send it to the user immediately. The user will be required to change this password on next login.') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.password.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="q" class="form-label">{{ __('Search') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               id="q" 
                               name="q" 
                               value="{{ request('q') }}" 
                               placeholder="{{ __('Search by name or email...') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="role" class="form-label">{{ __('Role') }}</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">{{ __('All Roles') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">{{ __('Status') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>{{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-people me-2"></i>{{ __('Users') }}
                <span class="badge bg-secondary ms-2">{{ $users->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Last Password Change') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php $listAvatar = $user->profile_photo_url; @endphp
                                        @if($listAvatar)
                                            <img src="{{ $listAvatar }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="rounded-circle me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-person text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-info">{{ __('You') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-secondary">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->password_changed_at)
                                        <small class="text-muted">
                                            {{ $user->password_changed_at->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="text-muted">{{ __('Never') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->deactivated_at)
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($user->id !== auth()->id())
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.password.show', $user) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="{{ __('Reset Password') }}">
                                                <i class="bi bi-key"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#tempPasswordModal{{ $user->id }}"
                                                    title="{{ __('Generate Temporary Password') }}">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        </div>
                                    @else
                                        <a href="{{ route('tenant.profile.password.change') }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>{{ __('Change My Password') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>

                            <!-- Temporary Password Modal -->
                            <div class="modal fade" id="tempPasswordModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.users.password.temp', $user) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Generate Temporary Password') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ __('Generate a temporary password for') }} <strong>{{ $user->name }}</strong>?</p>
                                                <p class="text-muted small">{{ __('The user will be required to change this password on their next login.') }}</p>
                                                
                                                <div class="mb-3">
                                                    <label for="reason{{ $user->id }}" class="form-label required">{{ __('Reason') }}</label>
                                                    <textarea class="form-control" 
                                                              id="reason{{ $user->id }}" 
                                                              name="reason" 
                                                              rows="3" 
                                                              required 
                                                              placeholder="{{ __('Explain why you are generating a temporary password...') }}"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit" class="btn btn-warning">{{ __('Generate') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    {{ __('No users found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyTempPassword() {
    const passwordText = document.getElementById('tempPasswordDisplay').textContent;
    navigator.clipboard.writeText(passwordText).then(() => {
        alert('{{ __("Temporary password copied to clipboard!") }}');
    });
}
</script>
@endpush
@endsection
