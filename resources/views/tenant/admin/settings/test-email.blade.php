@extends('tenant.admin.layout')

@section('title', __('Test Email'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Test Email Configuration') }}</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('tenant.settings.admin.test-email.send') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="test_email" class="form-label">{{ __('Test Email Address') }}</label>
                                    <input type="email" class="form-control @error('test_email') is-invalid @enderror"
                                           id="test_email" name="test_email"
                                           value="{{ old('test_email', auth()->user()->email) }}" required>
                                    <div class="form-text">{{ __('Enter the email address where you want to receive the test email.') }}</div>
                                    @error('test_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email_subject" class="form-label">{{ __('Email Subject') }}</label>
                                    <input type="text" class="form-control @error('email_subject') is-invalid @enderror"
                                           id="email_subject" name="email_subject"
                                           value="{{ old('email_subject', 'SkolarisCloud Email Test') }}" required>
                                    @error('email_subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email_message" class="form-label">{{ __('Email Message') }}</label>
                                    <textarea class="form-control @error('email_message') is-invalid @enderror"
                                              id="email_message" name="email_message" rows="5" required>{{ old('email_message', 'This is a test email from SkolarisCloud.

If you received this email, it means your email configuration is working correctly.

Sent at: ' . now()->format('Y-m-d H:i:s') . '

Best regards,
SkolarisCloud Team') }}</textarea>
                                    <div class="form-text">{{ __('Enter the message you want to send in the test email.') }}</div>
                                    @error('email_message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> {{ __('Send Test Email') }}
                                </button>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-info-circle"></i> {{ __('Current Email Configuration') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-5">{{ __('Driver') }}</dt>
                                        <dd class="col-sm-7">{{ setting('mail_driver', 'smtp') }}</dd>

                                        <dt class="col-sm-5">{{ __('Host') }}</dt>
                                        <dd class="col-sm-7">{{ setting('mail_host', 'Not configured') }}</dd>

                                        <dt class="col-sm-5">{{ __('Port') }}</dt>
                                        <dd class="col-sm-7">{{ setting('mail_port', 'Not configured') }}</dd>

                                        <dt class="col-sm-5">{{ __('Encryption') }}</dt>
                                        <dd class="col-sm-7">{{ setting('mail_encryption', 'Not configured') }}</dd>

                                        <dt class="col-sm-5">{{ __('From Email') }}</dt>
                                        <dd class="col-sm-7">{{ setting('mail_from_address', 'Not configured') }}</dd>

                                        <dt class="col-sm-5">{{ __('From Name') }}</dt>
                                        <dd class="col-sm-7">{{ setting('mail_from_name', 'Not configured') }}</dd>
                                    </dl>

                                    <div class="mt-3">
                                        <a href="{{ route('tenant.settings.admin.email') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-gear"></i> {{ __('Configure Email Settings') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-warning mt-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-lightbulb"></i> {{ __('Troubleshooting Tips') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success"></i>
                                            {{ __('Make sure your SMTP credentials are correct') }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success"></i>
                                            {{ __('Check if your email provider allows SMTP') }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success"></i>
                                            {{ __('Verify firewall settings allow outgoing connections') }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success"></i>
                                            {{ __('Try different ports (587 for TLS, 465 for SSL)') }}
                                        </li>
                                        <li class="mb-0">
                                            <i class="bi bi-check-circle text-success"></i>
                                            {{ __('Check spam/junk folder if email is not received') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Log -->
                    @if(isset($emailLogs) && $emailLogs->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>{{ __('Recent Email Logs') }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('To') }}</th>
                                                <th>{{ __('Subject') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($emailLogs as $log)
                                                <tr>
                                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                                    <td>{{ $log->to_email }}</td>
                                                    <td>{{ Str::limit($log->subject, 50) }}</td>
                                                    <td>
                                                        @if($log->status == 'sent')
                                                            <span class="badge bg-success">{{ __('Sent') }}</span>
                                                        @elseif($log->status == 'failed')
                                                            <span class="badge bg-danger">{{ __('Failed') }}</span>
                                                        @else
                                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-info" onclick="viewEmailLog({{ $log->id }})">
                                                            <i class="bi bi-eye"></i> {{ __('View') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Log Modal -->
<div class="modal fade" id="emailLogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Email Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="emailLogContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewEmailLog(logId) {
    fetch(`{{ route('tenant.settings.admin.email-log.show', '') }}/${logId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('emailLogContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('To') }}:</strong> ${data.to_email}</p>
                        <p><strong>{{ __('Subject') }}:</strong> ${data.subject}</p>
                        <p><strong>{{ __('Status') }}:</strong> ${data.status}</p>
                        <p><strong>{{ __('Sent At') }}:</strong> ${data.created_at}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('Error') }}:</strong> ${data.error_message || 'None'}</p>
                        <p><strong>{{ __('Attempts') }}:</strong> ${data.attempts || 1}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <strong>{{ __('Message') }}:</strong>
                    <div class="border p-3 mt-2" style="white-space: pre-wrap;">${data.message}</div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('emailLogModal')).show();
        })
        .catch(error => {
            alert('{{ __('Error loading email log details') }}');
        });
}
</script>
@endsection