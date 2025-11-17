@extends('tenant.layouts.app')

@section('title', __('Notification Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Notification Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- SMS Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('SMS Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="sms_provider" class="form-label">{{ __('SMS Provider') }}</label>
                                <select class="form-select @error('sms_provider') is-invalid @enderror" id="sms_provider" name="sms_provider">
                                    <option value="twilio" {{ old('sms_provider', setting('sms_provider', 'twilio')) == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                    <option value="africastalking" {{ old('sms_provider', setting('sms_provider', 'twilio')) == 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                                    <option value="none" {{ old('sms_provider', setting('sms_provider', 'twilio')) == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('sms_provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="sms_enabled" class="form-label">{{ __('SMS Notifications') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled" value="1"
                                           {{ old('sms_enabled', setting('sms_enabled', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_enabled">
                                        {{ __('Enable SMS notifications') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Twilio Settings -->
                        <div id="twilio_settings" class="row mb-4" style="{{ old('sms_provider', setting('sms_provider', 'twilio')) == 'twilio' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('Twilio Configuration') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="twilio_sid" class="form-label">{{ __('Twilio Account SID') }}</label>
                                <input type="text" class="form-control @error('twilio_sid') is-invalid @enderror"
                                       id="twilio_sid" name="twilio_sid"
                                       value="{{ old('twilio_sid', setting('twilio_sid')) }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                @error('twilio_sid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="twilio_token" class="form-label">{{ __('Twilio Auth Token') }}</label>
                                <input type="password" class="form-control @error('twilio_token') is-invalid @enderror"
                                       id="twilio_token" name="twilio_token"
                                       value="{{ old('twilio_token', setting('twilio_token')) }}" placeholder="Your auth token">
                                @error('twilio_token')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="twilio_phone" class="form-label">{{ __('Twilio Phone Number') }}</label>
                                <input type="text" class="form-control @error('twilio_phone') is-invalid @enderror"
                                       id="twilio_phone" name="twilio_phone"
                                       value="{{ old('twilio_phone', setting('twilio_phone')) }}" placeholder="+1234567890">
                                @error('twilio_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Africa's Talking Settings -->
                        <div id="africastalking_settings" class="row mb-4" style="{{ old('sms_provider', setting('sms_provider', 'twilio')) == 'africastalking' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('Africa\'s Talking Configuration') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="at_username" class="form-label">{{ __('Username') }}</label>
                                <input type="text" class="form-control @error('at_username') is-invalid @enderror"
                                       id="at_username" name="at_username"
                                       value="{{ old('at_username', setting('at_username')) }}">
                                @error('at_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="at_api_key" class="form-label">{{ __('API Key') }}</label>
                                <input type="password" class="form-control @error('at_api_key') is-invalid @enderror"
                                       id="at_api_key" name="at_api_key"
                                       value="{{ old('at_api_key', setting('at_api_key')) }}">
                                @error('at_api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="at_sender_id" class="form-label">{{ __('Sender ID') }}</label>
                                <input type="text" class="form-control @error('at_sender_id') is-invalid @enderror"
                                       id="at_sender_id" name="at_sender_id"
                                       value="{{ old('at_sender_id', setting('at_sender_id')) }}" placeholder="Your sender ID">
                                @error('at_sender_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- WhatsApp Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('WhatsApp Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_enabled" class="form-label">{{ __('WhatsApp Notifications') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled" value="1"
                                           {{ old('whatsapp_enabled', setting('whatsapp_enabled', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="whatsapp_enabled">
                                        {{ __('Enable WhatsApp notifications') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_provider" class="form-label">{{ __('WhatsApp Provider') }}</label>
                                <select class="form-select @error('whatsapp_provider') is-invalid @enderror" id="whatsapp_provider" name="whatsapp_provider">
                                    <option value="twilio" {{ old('whatsapp_provider', setting('whatsapp_provider', 'twilio')) == 'twilio' ? 'selected' : '' }}>Twilio WhatsApp</option>
                                    <option value="360dialog" {{ old('whatsapp_provider', setting('whatsapp_provider', 'twilio')) == '360dialog' ? 'selected' : '' }}>360Dialog</option>
                                    <option value="none" {{ old('whatsapp_provider', setting('whatsapp_provider', 'twilio')) == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('whatsapp_provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- WhatsApp Twilio Settings -->
                        <div id="whatsapp_twilio_settings" class="row mb-4" style="{{ old('whatsapp_provider', setting('whatsapp_provider', 'twilio')) == 'twilio' ? '' : 'display: none;' }}">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('WhatsApp Twilio Configuration') }}</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_twilio_sid" class="form-label">{{ __('WhatsApp Account SID') }}</label>
                                <input type="text" class="form-control @error('whatsapp_twilio_sid') is-invalid @enderror"
                                       id="whatsapp_twilio_sid" name="whatsapp_twilio_sid"
                                       value="{{ old('whatsapp_twilio_sid', setting('whatsapp_twilio_sid')) }}">
                                @error('whatsapp_twilio_sid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_twilio_token" class="form-label">{{ __('WhatsApp Auth Token') }}</label>
                                <input type="password" class="form-control @error('whatsapp_twilio_token') is-invalid @enderror"
                                       id="whatsapp_twilio_token" name="whatsapp_twilio_token"
                                       value="{{ old('whatsapp_twilio_token', setting('whatsapp_twilio_token')) }}">
                                @error('whatsapp_twilio_token')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp_phone_number" class="form-label">{{ __('WhatsApp Phone Number') }}</label>
                                <input type="text" class="form-control @error('whatsapp_phone_number') is-invalid @enderror"
                                       id="whatsapp_phone_number" name="whatsapp_phone_number"
                                       value="{{ old('whatsapp_phone_number', setting('whatsapp_phone_number')) }}" placeholder="+1234567890">
                                @error('whatsapp_phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notification Templates -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Notification Templates') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="sms_template_welcome" class="form-label">{{ __('Welcome SMS Template') }}</label>
                                <textarea class="form-control @error('sms_template_welcome') is-invalid @enderror"
                                          id="sms_template_welcome" name="sms_template_welcome" rows="3">{{ old('sms_template_welcome', setting('sms_template_welcome', 'Welcome to {school_name}! Your account has been created successfully.')) }}</textarea>
                                @error('sms_template_welcome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('Available variables: {school_name}, {user_name}, {user_email}') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="sms_template_fee_reminder" class="form-label">{{ __('Fee Reminder SMS Template') }}</label>
                                <textarea class="form-control @error('sms_template_fee_reminder') is-invalid @enderror"
                                          id="sms_template_fee_reminder" name="sms_template_fee_reminder" rows="3">{{ old('sms_template_fee_reminder', setting('sms_template_fee_reminder', 'Dear {user_name}, you have an outstanding fee balance of {amount}. Please make payment soon.')) }}</textarea>
                                @error('sms_template_fee_reminder')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('Available variables: {user_name}, {amount}, {due_date}') }}</div>
                            </div>
                        </div>

                        <!-- Default Notification Preferences -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Default Notification Preferences') }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Email Notifications') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_email_general" name="default_email_general" value="1"
                                           {{ old('default_email_general', setting('default_email_general', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_email_general">
                                        {{ __('General announcements') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_email_academic" name="default_email_academic" value="1"
                                           {{ old('default_email_academic', setting('default_email_academic', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_email_academic">
                                        {{ __('Academic updates') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_email_financial" name="default_email_financial" value="1"
                                           {{ old('default_email_financial', setting('default_email_financial', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_email_financial">
                                        {{ __('Financial notifications') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('SMS Notifications') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_sms_urgent" name="default_sms_urgent" value="1"
                                           {{ old('default_sms_urgent', setting('default_sms_urgent', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_sms_urgent">
                                        {{ __('Urgent messages') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_sms_fee_reminders" name="default_sms_fee_reminders" value="1"
                                           {{ old('default_sms_fee_reminders', setting('default_sms_fee_reminders', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_sms_fee_reminders">
                                        {{ __('Fee reminders') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_sms_attendance" name="default_sms_attendance" value="1"
                                           {{ old('default_sms_attendance', setting('default_sms_attendance', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_sms_attendance">
                                        {{ __('Attendance alerts') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('WhatsApp Notifications') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_whatsapp_general" name="default_whatsapp_general" value="1"
                                           {{ old('default_whatsapp_general', setting('default_whatsapp_general', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_whatsapp_general">
                                        {{ __('General announcements') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_whatsapp_urgent" name="default_whatsapp_urgent" value="1"
                                           {{ old('default_whatsapp_urgent', setting('default_whatsapp_urgent', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_whatsapp_urgent">
                                        {{ __('Urgent messages') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="default_whatsapp_results" name="default_whatsapp_results" value="1"
                                           {{ old('default_whatsapp_results', setting('default_whatsapp_results', false)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="default_whatsapp_results">
                                        {{ __('Exam results') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Test Notifications -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Test Notifications') }}</h5>
                                <p class="text-muted">{{ __('Send test notifications to verify your configuration') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label for="test_phone" class="form-label">{{ __('Test Phone Number') }}</label>
                                <input type="text" class="form-control" id="test_phone" name="test_phone" placeholder="+1234567890">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" formaction="{{ route('tenant.settings.admin.test-sms') }}" class="btn btn-outline-success me-2" onclick="return confirm('{{ __('Send test SMS?') }}')">
                                    <i class="bi bi-phone"></i> {{ __('Test SMS') }}
                                </button>
                                <button type="submit" formaction="{{ route('tenant.settings.admin.test-whatsapp') }}" class="btn btn-outline-primary" onclick="return confirm('{{ __('Send test WhatsApp message?') }}')">
                                    <i class="bi bi-whatsapp"></i> {{ __('Test WhatsApp') }}
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save Notification Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle SMS provider settings
    document.getElementById('sms_provider').addEventListener('change', function() {
        const twilioSettings = document.getElementById('twilio_settings');
        const africastalkingSettings = document.getElementById('africastalking_settings');

        if (this.value === 'twilio') {
            twilioSettings.style.display = 'block';
            africastalkingSettings.style.display = 'none';
        } else if (this.value === 'africastalking') {
            twilioSettings.style.display = 'none';
            africastalkingSettings.style.display = 'block';
        } else {
            twilioSettings.style.display = 'none';
            africastalkingSettings.style.display = 'none';
        }
    });

    // Toggle WhatsApp provider settings
    document.getElementById('whatsapp_provider').addEventListener('change', function() {
        const whatsappTwilioSettings = document.getElementById('whatsapp_twilio_settings');

        if (this.value === 'twilio') {
            whatsappTwilioSettings.style.display = 'block';
        } else {
            whatsappTwilioSettings.style.display = 'none';
        }
    });

    // Initialize visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('sms_provider').dispatchEvent(new Event('change'));
        document.getElementById('whatsapp_provider').dispatchEvent(new Event('change'));
    });
</script>
@endpush