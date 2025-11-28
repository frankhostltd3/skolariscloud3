@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
                <div>
                    <h1 class="h4 fw-semibold mb-1">Settings</h1>
                    <p class="text-muted mb-0">Manage platform-wide preferences, integrations, and credentials.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-gear-fill"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">General Settings</h2>
                                    <p class="text-muted mb-0">Configure school information and application preferences.</p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Configuration</span>
                                <a class="btn btn-outline-secondary" href="{{ route('tenant.settings.admin.general') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-primary bg-opacity-10 text-primary me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">Mail Delivery</h2>
                                    <p class="text-muted mb-0">Configure sender details and choose your email
                                        transport.</p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary">Notification</span>
                                <a class="btn btn-outline-primary" href="{{ route('tenant.settings.admin.mail') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-success bg-opacity-10 text-success me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-credit-card-fill"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">Payment Settings</h2>
                                    <p class="text-muted mb-0">Enable and maintain payment providers available to
                                        your schools.</p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-success bg-opacity-10 text-success">Finance</span>
                                <a class="btn btn-outline-success" href="{{ route('tenant.settings.admin.finance') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-success bg-opacity-10 text-success me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-currency-exchange"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">Currencies</h2>
                                    <p class="text-muted mb-0">Manage currencies and exchange rates for payment
                                        processing.</p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-success bg-opacity-10 text-success">Finance</span>
                                <a class="btn btn-outline-success"
                                    href="{{ route('tenant.settings.admin.currencies.index') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-info bg-opacity-10 text-info me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-chat-dots-fill"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">Messaging Channels</h2>
                                    <p class="text-muted mb-0">Manage SMS and WhatsApp gateways for internal and
                                        external
                                        notifications.</p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-info bg-opacity-10 text-info">Communication</span>
                                <a class="btn btn-outline-info" href="{{ route('tenant.settings.admin.messaging') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-warning bg-opacity-10 text-warning me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">Academic Settings</h2>
                                    <p class="text-muted mb-0">Configure academic year, grading system, and attendance
                                        policies.</p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning">Academic</span>
                                <a class="btn btn-outline-warning" href="{{ route('tenant.settings.admin.academic') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-danger bg-opacity-10 text-danger me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-server"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">System Settings</h2>
                                    <p class="text-muted mb-0">Configure performance, security, and maintenance settings.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger">System</span>
                                <a class="btn btn-outline-danger" href="{{ route('tenant.settings.admin.system') }}">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-arrow-repeat"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">Environment Sync</h2>
                                    <p class="text-muted mb-0">Use sync toggles inside each integration to update
                                        landlord environment variables.</p>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <p class="small text-muted mb-0">
                                    {{ $isTenantContext ? 'Environment sync is disabled within tenant databases.' : 'Available when updating credentials on this host.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <span class="icon-circle bg-info bg-opacity-10 text-info me-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-tools"></i>
                                </span>
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">More integrations</h2>
                                    <p class="text-muted mb-0">Additional settings modules will appear here as they
                                        are enabled.</p>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <span class="badge bg-warning bg-opacity-10 text-warning">Coming soon</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-muted small">
                Need help? Visit the documentation or contact support to configure advanced integrations.
            </div>
        </div>
    </div>
@endsection
