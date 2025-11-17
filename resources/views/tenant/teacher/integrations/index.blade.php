@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Platform Integrations')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-plug me-2 text-primary"></i>Platform Integrations
            </h1>
            <p class="text-muted mb-0">Connect Zoom, Google Meet, and Microsoft Teams for seamless virtual class management</p>
        </div>
        <a href="{{ route('tenant.teacher.classroom.virtual.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Virtual Classes
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Platform Cards -->
    <div class="row g-4">
        <!-- Zoom Integration -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-{{ $platforms['zoom'] && $platforms['zoom']->is_enabled ? 'success' : 'secondary' }}">
                <div class="card-header bg-{{ $platforms['zoom'] && $platforms['zoom']->is_enabled ? 'success' : 'light' }} text-{{ $platforms['zoom'] && $platforms['zoom']->is_enabled ? 'white' : 'dark' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-camera-video me-2"></i>Zoom
                            </h5>
                        </div>
                        @if($platforms['zoom'] && $platforms['zoom']->is_enabled)
                            <span class="badge bg-white text-success">
                                <i class="bi bi-check-circle"></i> Active
                            </span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Professional video conferencing platform with recording, breakout rooms, and screen sharing.
                    </p>
                    
                    @if($platforms['zoom'] && $platforms['zoom']->isConfigured())
                        <div class="mb-3">
                            <div class="d-flex align-items-center text-success mb-2">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <span class="small">Credentials configured</span>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-key me-2"></i>API Key: •••••••••
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning alert-sm py-2 small">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Not configured. Setup required.
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.teacher.classroom.integrations.setup', 'zoom') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-gear me-2"></i>Configure
                        </a>
                        @if($platforms['zoom'] && $platforms['zoom']->isConfigured())
                            <form action="{{ route('tenant.teacher.classroom.integrations.test', 'zoom') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-2"></i>Test Connection
                                </button>
                            </form>
                            @if($platforms['zoom']->is_enabled)
                                <form action="{{ route('tenant.teacher.classroom.integrations.disable', 'zoom') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Disable Zoom integration?')">
                                        <i class="bi bi-x-circle me-2"></i>Disable
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Meet Integration -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-{{ $platforms['google_meet'] && $platforms['google_meet']->is_enabled ? 'success' : 'secondary' }}">
                <div class="card-header bg-{{ $platforms['google_meet'] && $platforms['google_meet']->is_enabled ? 'success' : 'light' }} text-{{ $platforms['google_meet'] && $platforms['google_meet']->is_enabled ? 'white' : 'dark' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-google me-2"></i>Google Meet
                            </h5>
                        </div>
                        @if($platforms['google_meet'] && $platforms['google_meet']->is_enabled)
                            <span class="badge bg-white text-success">
                                <i class="bi bi-check-circle"></i> Active
                            </span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Integrated with Google Workspace for seamless calendar scheduling and meeting management.
                    </p>
                    
                    @if($platforms['google_meet'] && $platforms['google_meet']->isConfigured())
                        <div class="mb-3">
                            <div class="d-flex align-items-center text-success mb-2">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <span class="small">OAuth configured</span>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-key me-2"></i>Client ID: •••••••••
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning alert-sm py-2 small">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Not configured. Setup required.
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.teacher.classroom.integrations.setup', 'google_meet') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-gear me-2"></i>Configure
                        </a>
                        @if($platforms['google_meet'] && $platforms['google_meet']->isConfigured())
                            <form action="{{ route('tenant.teacher.classroom.integrations.test', 'google_meet') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-2"></i>Test Connection
                                </button>
                            </form>
                            @if($platforms['google_meet']->is_enabled)
                                <form action="{{ route('tenant.teacher.classroom.integrations.disable', 'google_meet') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Disable Google Meet integration?')">
                                        <i class="bi bi-x-circle me-2"></i>Disable
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Microsoft Teams Integration -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-{{ $platforms['microsoft_teams'] && $platforms['microsoft_teams']->is_enabled ? 'success' : 'secondary' }}">
                <div class="card-header bg-{{ $platforms['microsoft_teams'] && $platforms['microsoft_teams']->is_enabled ? 'success' : 'light' }} text-{{ $platforms['microsoft_teams'] && $platforms['microsoft_teams']->is_enabled ? 'white' : 'dark' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-microsoft me-2"></i>Microsoft Teams
                            </h5>
                        </div>
                        @if($platforms['microsoft_teams'] && $platforms['microsoft_teams']->is_enabled)
                            <span class="badge bg-white text-success">
                                <i class="bi bi-check-circle"></i> Active
                            </span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Enterprise-grade collaboration platform with chat, file sharing, and Microsoft 365 integration.
                    </p>
                    
                    @if($platforms['microsoft_teams'] && $platforms['microsoft_teams']->isConfigured())
                        <div class="mb-3">
                            <div class="d-flex align-items-center text-success mb-2">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <span class="small">OAuth configured</span>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-key me-2"></i>Client ID: •••••••••
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning alert-sm py-2 small">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Not configured. Setup required.
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.teacher.classroom.integrations.setup', 'microsoft_teams') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-gear me-2"></i>Configure
                        </a>
                        @if($platforms['microsoft_teams'] && $platforms['microsoft_teams']->isConfigured())
                            <form action="{{ route('tenant.teacher.classroom.integrations.test', 'microsoft_teams') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-2"></i>Test Connection
                                </button>
                            </form>
                            @if($platforms['microsoft_teams']->is_enabled)
                                <form action="{{ route('tenant.teacher.classroom.integrations.disable', 'microsoft_teams') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Disable Microsoft Teams integration?')">
                                        <i class="bi bi-x-circle me-2"></i>Disable
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-question-circle me-2 text-info"></i>Setup Guide
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-primary">Zoom Setup</h6>
                    <ol class="small text-muted">
                        <li>Go to <a href="https://marketplace.zoom.us/" target="_blank">Zoom Marketplace</a></li>
                        <li>Create a Server-to-Server OAuth app</li>
                        <li>Copy your API Key and Secret</li>
                        <li>Paste credentials in setup form</li>
                    </ol>
                </div>
                <div class="col-md-4">
                    <h6 class="text-success">Google Meet Setup</h6>
                    <ol class="small text-muted">
                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                        <li>Enable Google Meet API</li>
                        <li>Create OAuth 2.0 credentials</li>
                        <li>Add redirect URI and save credentials</li>
                    </ol>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary">Microsoft Teams Setup</h6>
                    <ol class="small text-muted">
                        <li>Go to <a href="https://portal.azure.com/" target="_blank">Azure Portal</a></li>
                        <li>Register a new application</li>
                        <li>Configure API permissions</li>
                        <li>Generate and save client credentials</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


