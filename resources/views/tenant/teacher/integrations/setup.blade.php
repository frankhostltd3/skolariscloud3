@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Setup ' . ucfirst(str_replace('_', ' ', $platform)))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        @if($platform === 'zoom')
                            <i class="bi bi-camera-video me-2 text-info"></i>Setup Zoom Integration
                        @elseif($platform === 'google_meet')
                            <i class="bi bi-google me-2 text-success"></i>Setup Google Meet Integration
                        @elseif($platform === 'microsoft_teams')
                            <i class="bi bi-microsoft me-2 text-primary"></i>Setup Microsoft Teams Integration
                        @endif
                    </h1>
                    <p class="text-muted mb-0">Configure API credentials for automatic meeting creation</p>
                </div>
                <a href="{{ route('tenant.teacher.classroom.integrations.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>

            <!-- Setup Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tenant.teacher.classroom.integrations.store', $platform) }}" method="POST">
                        @csrf

                        <!-- Enable Integration -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="is_enabled" name="is_enabled" value="1"
                                       {{ old('is_enabled', $integration->is_enabled ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_enabled">
                                    <strong>Enable {{ ucfirst(str_replace('_', ' ', $platform)) }} Integration</strong>
                                </label>
                            </div>
                            <small class="text-muted">When enabled, the system will automatically create meetings using this platform's API</small>
                        </div>

                        <hr class="my-4">

                        @if($platform === 'zoom')
                            <!-- Zoom Credentials -->
                            <div class="mb-3">
                                <label for="api_key" class="form-label">
                                    API Key / Account ID <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('api_key') is-invalid @enderror" 
                                       id="api_key" name="api_key" 
                                       value="{{ old('api_key', $integration->decrypted_api_key ?? '') }}"
                                       placeholder="Enter your Zoom API Key or Account ID">
                                @error('api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Found in Zoom Marketplace under your Server-to-Server OAuth app</small>
                            </div>

                            <div class="mb-3">
                                <label for="api_secret" class="form-label">
                                    API Secret / Client Secret <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('api_secret') is-invalid @enderror" 
                                       id="api_secret" name="api_secret" 
                                       value="{{ old('api_secret', $integration->decrypted_api_secret ?? '') }}"
                                       placeholder="Enter your Zoom API Secret or Client Secret">
                                @error('api_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Keep this secret secure - it will be encrypted in our database</small>
                            </div>

                            <!-- Help Text -->
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>How to get Zoom credentials:</h6>
                                <ol class="mb-0 small">
                                    <li>Visit <a href="https://marketplace.zoom.us/" target="_blank" class="alert-link">Zoom Marketplace</a></li>
                                    <li>Click "Develop" → "Build App" → Choose "Server-to-Server OAuth"</li>
                                    <li>Fill in app information and create</li>
                                    <li>Copy the "Account ID" as API Key</li>
                                    <li>Copy the "Client Secret" as API Secret</li>
                                    <li>Add required scopes: meeting:write, meeting:read, user:read</li>
                                </ol>
                            </div>

                        @elseif($platform === 'google_meet')
                            <!-- Google Meet Credentials -->
                            <div class="mb-3">
                                <label for="client_id" class="form-label">
                                    OAuth Client ID <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('client_id') is-invalid @enderror" 
                                       id="client_id" name="client_id" 
                                       value="{{ old('client_id', $integration->decrypted_client_id ?? '') }}"
                                       placeholder="Enter your Google OAuth Client ID">
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Found in Google Cloud Console under "Credentials"</small>
                            </div>

                            <div class="mb-3">
                                <label for="client_secret" class="form-label">
                                    OAuth Client Secret <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('client_secret') is-invalid @enderror" 
                                       id="client_secret" name="client_secret" 
                                       value="{{ old('client_secret', $integration->decrypted_client_secret ?? '') }}"
                                       placeholder="Enter your Google OAuth Client Secret">
                                @error('client_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Keep this secret secure - it will be encrypted</small>
                            </div>

                            <!-- Help Text -->
                            <div class="alert alert-success">
                                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>How to get Google Meet credentials:</h6>
                                <ol class="mb-0 small">
                                    <li>Visit <a href="https://console.cloud.google.com/" target="_blank" class="alert-link">Google Cloud Console</a></li>
                                    <li>Create a new project or select existing one</li>
                                    <li>Enable "Google Calendar API" and "Google Meet API"</li>
                                    <li>Go to "Credentials" → "Create Credentials" → "OAuth client ID"</li>
                                    <li>Choose "Web application" as application type</li>
                                    <li>Add authorized redirect URI: <code>{{ route('tenant.dashboard') }}</code></li>
                                    <li>Copy Client ID and Client Secret</li>
                                </ol>
                            </div>

                        @elseif($platform === 'microsoft_teams')
                            <!-- Microsoft Teams Credentials -->
                            <div class="mb-3">
                                <label for="client_id" class="form-label">
                                    Application (Client) ID <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('client_id') is-invalid @enderror" 
                                       id="client_id" name="client_id" 
                                       value="{{ old('client_id', $integration->decrypted_client_id ?? '') }}"
                                       placeholder="Enter your Azure Application (Client) ID">
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Found in Azure Portal under "App registrations"</small>
                            </div>

                            <div class="mb-3">
                                <label for="client_secret" class="form-label">
                                    Client Secret Value <span class="text-danger">*</span>
                                </label>
                                <input type="password" class="form-control @error('client_secret') is-invalid @enderror" 
                                       id="client_secret" name="client_secret" 
                                       value="{{ old('client_secret', $integration->decrypted_client_secret ?? '') }}"
                                       placeholder="Enter your Azure Client Secret">
                                @error('client_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Keep this secret secure - it will be encrypted</small>
                            </div>

                            <!-- Help Text -->
                            <div class="alert alert-primary">
                                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>How to get Microsoft Teams credentials:</h6>
                                <ol class="mb-0 small">
                                    <li>Visit <a href="https://portal.azure.com/" target="_blank" class="alert-link">Azure Portal</a></li>
                                    <li>Go to "Azure Active Directory" → "App registrations" → "New registration"</li>
                                    <li>Enter name and select account type</li>
                                    <li>Set redirect URI to: <code>{{ route('tenant.dashboard') }}</code></li>
                                    <li>Copy "Application (client) ID"</li>
                                    <li>Go to "Certificates & secrets" → "New client secret"</li>
                                    <li>Copy the secret value (not the ID)</li>
                                    <li>Add API permissions: OnlineMeetings.ReadWrite, Calendars.ReadWrite</li>
                                </ol>
                            </div>
                        @endif

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('tenant.teacher.classroom.integrations.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="card mt-3 border-warning">
                <div class="card-body">
                    <h6 class="text-warning">
                        <i class="bi bi-shield-exclamation me-2"></i>Security Notice
                    </h6>
                    <p class="small text-muted mb-0">
                        All API credentials are encrypted using AES-256 encryption before being stored in the database. 
                        Never share your credentials with anyone. If you believe your credentials have been compromised, 
                        revoke them immediately in the respective platform's console and generate new ones.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


