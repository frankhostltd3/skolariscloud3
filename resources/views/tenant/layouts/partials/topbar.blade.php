<header class="border-bottom bg-white">
    <div class="tenant-content mx-auto px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            @php
                $logoUrl = setting('school_logo')
                    ? \Illuminate\Support\Facades\Storage::url(setting('school_logo'))
                    : null;
            @endphp
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $school->name ?? 'School' }} Logo"
                    style="height: 45px; width: 45px; object-fit: contain;" onerror="this.style.display='none'">
            @endif
            <div>
                <div class="fw-semibold text-uppercase text-muted small">{{ $school->name ?? 'Workspace' }}</div>
                <div class="h5 mb-0">{{ $pageTitle ?? ($title ?? 'Overview') }}</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            @if ($school)
                <span class="badge text-bg-light text-secondary">
                    <i class="bi bi-globe me-1"></i>
                    {{ $school->domain ?? ($school->subdomain ? $school->subdomain . '.' . config('tenancy.central_domain') : 'Workspace') }}
                </span>
            @endif

            @if ($user)
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle me-2"></i>{{ $user->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 small text-muted">
                            {{ $user->user_type?->label() ?? 'Member' }}
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('two-factor.show') }}">
                                <i class="bi bi-shield-lock me-2"></i>Two-Factor Authentication
                                @if ($user->two_factor_confirmed_at)
                                    <span class="badge bg-success badge-sm ms-1">Enabled</span>
                                @else
                                    @if (setting('enable_two_factor_auth', false))
                                        <span class="badge bg-warning badge-sm ms-1">Required</span>
                                    @endif
                                @endif
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('tenant.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</header>
