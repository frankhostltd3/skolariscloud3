<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Arr;

class MailConfigurator
{
    protected array $baseMailConfig;
    protected array $baseServicesConfig;

    public function __construct()
    {
        $this->baseMailConfig = config('mail');
        $this->baseServicesConfig = config('services');
    }

    public function apply(): void
    {
        config(['mail' => $this->baseMailConfig]);
        config(['services' => $this->baseServicesConfig]);

        try {
            $settings = MailSetting::query()->first();
        } catch (\Throwable $exception) {
            return;
        }

        if (! $settings) {
            return;
        }

        $config = $settings->config ?? [];

        config([
            'mail.default' => $settings->mailer ?: Arr::get($this->baseMailConfig, 'default', 'mail'),
            'mail.from.address' => $settings->from_address ?: Arr::get($this->baseMailConfig, 'from.address'),
            'mail.from.name' => $settings->from_name ?: Arr::get($this->baseMailConfig, 'from.name', config('app.name')),
        ]);

        match ($settings->mailer) {
            'smtp' => $this->applySmtpConfig($config),
            'mailgun' => $this->applyMailgunConfig($config),
            'ses', 'ses-v2' => $this->applySesConfig($config),
            'postmark' => $this->applyPostmarkConfig($config),
            'sendgrid' => $this->applySendgridConfig($config),
            'resend' => $this->applyResendConfig($config),
            default => null,
        };
    }

    protected function applySmtpConfig(array $config): void
    {
        config([
            'mail.mailers.smtp.host' => $config['host'] ?? Arr::get($this->baseMailConfig, 'mailers.smtp.host'),
            'mail.mailers.smtp.port' => $config['port'] ?? Arr::get($this->baseMailConfig, 'mailers.smtp.port'),
            'mail.mailers.smtp.username' => $config['username'] ?? Arr::get($this->baseMailConfig, 'mailers.smtp.username'),
            'mail.mailers.smtp.password' => $config['password'] ?? Arr::get($this->baseMailConfig, 'mailers.smtp.password'),
            'mail.mailers.smtp.encryption' => $config['encryption'] ?? Arr::get($this->baseMailConfig, 'mailers.smtp.encryption'),
        ]);
    }

    protected function applyMailgunConfig(array $config): void
    {
        config([
            'services.mailgun.domain' => $config['domain'] ?? Arr::get($this->baseServicesConfig, 'mailgun.domain'),
            'services.mailgun.secret' => $config['secret'] ?? Arr::get($this->baseServicesConfig, 'mailgun.secret'),
            'services.mailgun.endpoint' => $config['endpoint'] ?? Arr::get($this->baseServicesConfig, 'mailgun.endpoint', 'api.mailgun.net'),
            'services.mailgun.scheme' => $config['scheme'] ?? Arr::get($this->baseServicesConfig, 'mailgun.scheme', 'https'),
            'mail.mailers.mailgun.domain' => $config['domain'] ?? Arr::get($this->baseMailConfig, 'mailers.mailgun.domain'),
            'mail.mailers.mailgun.secret' => $config['secret'] ?? Arr::get($this->baseMailConfig, 'mailers.mailgun.secret'),
            'mail.mailers.mailgun.endpoint' => $config['endpoint'] ?? Arr::get($this->baseMailConfig, 'mailers.mailgun.endpoint', 'api.mailgun.net'),
            'mail.mailers.mailgun.scheme' => $config['scheme'] ?? Arr::get($this->baseMailConfig, 'mailers.mailgun.scheme', 'https'),
        ]);
    }

    protected function applySesConfig(array $config): void
    {
        config([
            'services.ses.key' => $config['key'] ?? Arr::get($this->baseServicesConfig, 'ses.key'),
            'services.ses.secret' => $config['secret'] ?? Arr::get($this->baseServicesConfig, 'ses.secret'),
            'services.ses.region' => $config['region'] ?? Arr::get($this->baseServicesConfig, 'ses.region', 'us-east-1'),
            'mail.mailers.ses-v2.region' => $config['region'] ?? Arr::get($this->baseMailConfig, 'mailers.ses-v2.region', 'us-east-1'),
            'mail.mailers.ses-v2.credentials.key' => $config['key'] ?? Arr::get($this->baseMailConfig, 'mailers.ses-v2.credentials.key'),
            'mail.mailers.ses-v2.credentials.secret' => $config['secret'] ?? Arr::get($this->baseMailConfig, 'mailers.ses-v2.credentials.secret'),
        ]);
    }

    protected function applyPostmarkConfig(array $config): void
    {
        config([
            'services.postmark.key' => $config['token'] ?? Arr::get($this->baseServicesConfig, 'postmark.key'),
            'mail.mailers.postmark.token' => $config['token'] ?? Arr::get($this->baseMailConfig, 'mailers.postmark.token'),
            'mail.mailers.postmark.message_stream_id' => $config['message_stream_id'] ?? Arr::get($this->baseMailConfig, 'mailers.postmark.message_stream_id'),
        ]);
    }

    protected function applySendgridConfig(array $config): void
    {
        config([
            'mail.mailers.sendgrid.api_key' => $config['api_key'] ?? Arr::get($this->baseMailConfig, 'mailers.sendgrid.api_key'),
            'services.sendgrid.api_key' => $config['api_key'] ?? Arr::get($this->baseServicesConfig, 'sendgrid.api_key'),
        ]);
    }

    protected function applyResendConfig(array $config): void
    {
        config([
            'services.resend.key' => $config['api_key'] ?? Arr::get($this->baseServicesConfig, 'resend.key'),
            'mail.mailers.resend.api_key' => $config['api_key'] ?? Arr::get($this->baseMailConfig, 'mailers.resend.api_key'),
        ]);
    }
}
