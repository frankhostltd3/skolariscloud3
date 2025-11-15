@extends('tenant.layouts.app')

@section('content')
    @php($currentMailer = old('mailer', $settings->mailer ?? 'mail'))
    @php($config = $settings->config ?? [])

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 fw-semibold mb-2">Mail Delivery Settings</h1>
                    <p class="text-muted mb-4">Choose how the platform sends email notifications. Update the sender
                        details and credentials for your preferred provider.</p>

                    <form method="POST" action="{{ route('settings.mail.update') }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="from_name" class="form-label">From name</label>
                                <input type="text" class="form-control @error('from_name') is-invalid @enderror"
                                    id="from_name" name="from_name" value="{{ old('from_name', $settings->from_name) }}"
                                    placeholder="SMATCAMPUS Support">
                                @error('from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="from_address" class="form-label">From email</label>
                                <input type="email" class="form-control @error('from_address') is-invalid @enderror"
                                    id="from_address" name="from_address"
                                    value="{{ old('from_address', $settings->from_address) }}"
                                    placeholder="no-reply@{{ config('tenancy.central_domain') ?? 'example.com' }}">
                                @error('from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="mailer" class="form-label fw-semibold">Delivery provider</label>
                            <select class="form-select @error('mailer') is-invalid @enderror" id="mailer" name="mailer"
                                data-mailer-selector>
                                @foreach ($mailers as $value => $label)
                                    <option value="{{ $value }}" {{ $currentMailer === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mailer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mailer-settings" data-mailer-section="mail">
                            <div class="alert alert-info mb-0">
                                PHP mail uses the hosting server's native mail transport. No additional credentials
                                are required,
                                but deliverability depends on your server configuration.
                            </div>
                        </div>

                        <div class="mailer-settings" data-mailer-section="smtp">
                            <h2 class="h5 fw-semibold mt-4">SMTP credentials</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="smtp_host" class="form-label">Host</label>
                                    <input type="text" class="form-control @error('smtp_host') is-invalid @enderror"
                                        id="smtp_host" name="smtp_host"
                                        value="{{ old('smtp_host', $currentMailer === 'smtp' ? $config['host'] ?? '' : '') }}"
                                        placeholder="smtp.mailtrap.io">
                                    @error('smtp_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="smtp_port" class="form-label">Port</label>
                                    <input type="number" min="1"
                                        class="form-control @error('smtp_port') is-invalid @enderror" id="smtp_port"
                                        name="smtp_port"
                                        value="{{ old('smtp_port', $currentMailer === 'smtp' ? $config['port'] ?? '' : '') }}"
                                        placeholder="587">
                                    @error('smtp_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="smtp_encryption" class="form-label">Encryption</label>
                                    <select class="form-select @error('smtp_encryption') is-invalid @enderror"
                                        id="smtp_encryption" name="smtp_encryption">
                                        <option value=""
                                            {{ old('smtp_encryption', $config['encryption'] ?? '') === '' ? 'selected' : '' }}>
                                            None</option>
                                        @foreach (['ssl', 'tls', 'starttls'] as $option)
                                            <option value="{{ $option }}"
                                                {{ old('smtp_encryption', $currentMailer === 'smtp' ? $config['encryption'] ?? '' : '') === $option ? 'selected' : '' }}>
                                                {{ strtoupper($option) }}</option>
                                        @endforeach
                                    </select>
                                    @error('smtp_encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="smtp_username" class="form-label">Username</label>
                                    <input type="text" class="form-control @error('smtp_username') is-invalid @enderror"
                                        id="smtp_username" name="smtp_username"
                                        value="{{ old('smtp_username', $currentMailer === 'smtp' ? $config['username'] ?? '' : '') }}">
                                    @error('smtp_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="smtp_password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('smtp_password') is-invalid @enderror"
                                        id="smtp_password" name="smtp_password" value="{{ old('smtp_password') }}"
                                        placeholder="Leave blank to keep existing password">
                                    @error('smtp_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mailer-settings" data-mailer-section="mailgun">
                            <h2 class="h5 fw-semibold mt-4">Mailgun credentials</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="mailgun_domain" class="form-label">Domain</label>
                                    <input type="text"
                                        class="form-control @error('mailgun_domain') is-invalid @enderror"
                                        id="mailgun_domain" name="mailgun_domain"
                                        value="{{ old('mailgun_domain', $currentMailer === 'mailgun' ? $config['domain'] ?? '' : '') }}"
                                        placeholder="mg.example.com">
                                    @error('mailgun_domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mailgun_secret" class="form-label">API key</label>
                                    <input type="text"
                                        class="form-control @error('mailgun_secret') is-invalid @enderror"
                                        id="mailgun_secret" name="mailgun_secret" value="{{ old('mailgun_secret') }}"
                                        placeholder="Paste your Mailgun private API key">
                                    @error('mailgun_secret')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mailgun_endpoint" class="form-label">Endpoint</label>
                                    <input type="text"
                                        class="form-control @error('mailgun_endpoint') is-invalid @enderror"
                                        id="mailgun_endpoint" name="mailgun_endpoint"
                                        value="{{ old('mailgun_endpoint', $currentMailer === 'mailgun' ? $config['endpoint'] ?? 'api.mailgun.net' : '') }}">
                                    @error('mailgun_endpoint')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mailgun_scheme" class="form-label">Scheme</label>
                                    <select class="form-select @error('mailgun_scheme') is-invalid @enderror"
                                        id="mailgun_scheme" name="mailgun_scheme">
                                        @foreach (['https', 'http'] as $scheme)
                                            <option value="{{ $scheme }}"
                                                {{ old('mailgun_scheme', $currentMailer === 'mailgun' ? $config['scheme'] ?? 'https' : 'https') === $scheme ? 'selected' : '' }}>
                                                {{ strtoupper($scheme) }}</option>
                                        @endforeach
                                    </select>
                                    @error('mailgun_scheme')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mailer-settings" data-mailer-section="ses-v2">
                            <h2 class="h5 fw-semibold mt-4">Amazon SES credentials</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="ses_key" class="form-label">Access key ID</label>
                                    <input type="text" class="form-control @error('ses_key') is-invalid @enderror"
                                        id="ses_key" name="ses_key" value="{{ old('ses_key') }}"
                                        placeholder="Leave blank to keep existing key">
                                    @error('ses_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="ses_secret" class="form-label">Secret access key</label>
                                    <input type="text" class="form-control @error('ses_secret') is-invalid @enderror"
                                        id="ses_secret" name="ses_secret" value="{{ old('ses_secret') }}"
                                        placeholder="Leave blank to keep existing secret">
                                    @error('ses_secret')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="ses_region" class="form-label">Region</label>
                                    <input type="text" class="form-control @error('ses_region') is-invalid @enderror"
                                        id="ses_region" name="ses_region"
                                        value="{{ old('ses_region', in_array($currentMailer, ['ses', 'ses-v2']) ? $config['region'] ?? 'us-east-1' : '') }}">
                                    @error('ses_region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mailer-settings" data-mailer-section="postmark">
                            <h2 class="h5 fw-semibold mt-4">Postmark credentials</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="postmark_token" class="form-label">Server token</label>
                                    <input type="text"
                                        class="form-control @error('postmark_token') is-invalid @enderror"
                                        id="postmark_token" name="postmark_token" value="{{ old('postmark_token') }}"
                                        placeholder="Leave blank to keep existing token">
                                    @error('postmark_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="postmark_message_stream_id" class="form-label">Message stream
                                        ID</label>
                                    <input type="text"
                                        class="form-control @error('postmark_message_stream_id') is-invalid @enderror"
                                        id="postmark_message_stream_id" name="postmark_message_stream_id"
                                        value="{{ old('postmark_message_stream_id', $currentMailer === 'postmark' ? $config['message_stream_id'] ?? '' : '') }}">
                                    @error('postmark_message_stream_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mailer-settings" data-mailer-section="sendgrid">
                            <h2 class="h5 fw-semibold mt-4">SendGrid credentials</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="sendgrid_api_key" class="form-label">API key</label>
                                    <input type="text"
                                        class="form-control @error('sendgrid_api_key') is-invalid @enderror"
                                        id="sendgrid_api_key" name="sendgrid_api_key"
                                        value="{{ old('sendgrid_api_key') }}"
                                        placeholder="Leave blank to keep existing key">
                                    @error('sendgrid_api_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mailer-settings" data-mailer-section="resend">
                            <h2 class="h5 fw-semibold mt-4">Resend credentials</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="resend_api_key" class="form-label">API key</label>
                                    <input type="text"
                                        class="form-control @error('resend_api_key') is-invalid @enderror"
                                        id="resend_api_key" name="resend_api_key" value="{{ old('resend_api_key') }}"
                                        placeholder="Leave blank to keep existing key">
                                    @error('resend_api_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selector = document.querySelector('[data-mailer-selector]');
            const sections = document.querySelectorAll('.mailer-settings');

            if (!selector) {
                return;
            }

            const toggleSections = () => {
                sections.forEach((section) => {
                    const target = section.getAttribute('data-mailer-section');
                    if (!target) {
                        return;
                    }

                    section.style.display = target === selector.value || (selector.value.startsWith(
                            'ses') && target === 'ses-v2') ?
                        'block' :
                        'none';
                });
            };

            toggleSections();
            selector.addEventListener('change', toggleSections);
        });
    </script>
@endpush
