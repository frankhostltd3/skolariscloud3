<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use App\Services\MailConfigurator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MailSettingsController extends Controller
{
    private array $mailers = [
        'mail' => 'PHP Mail',
        'smtp' => 'SMTP',
        'mailgun' => 'Mailgun',
        'ses' => 'Amazon SES',
        'ses-v2' => 'Amazon SES (v2)',
        'postmark' => 'Postmark',
        'sendgrid' => 'SendGrid',
        'resend' => 'Resend',
    ];

    private array $fieldMap = [
        'smtp' => [
            'smtp_host' => ['label' => 'SMTP Host', 'config_key' => 'host'],
            'smtp_port' => ['label' => 'SMTP Port', 'config_key' => 'port'],
            'smtp_username' => ['label' => 'SMTP Username', 'config_key' => 'username'],
            'smtp_password' => ['label' => 'SMTP Password', 'config_key' => 'password'],
            'smtp_encryption' => ['label' => 'SMTP Encryption', 'config_key' => 'encryption'],
        ],
        'mailgun' => [
            'mailgun_domain' => ['label' => 'Mailgun Domain', 'config_key' => 'domain'],
            'mailgun_secret' => ['label' => 'Mailgun API Key', 'config_key' => 'secret'],
            'mailgun_endpoint' => ['label' => 'Mailgun Endpoint', 'config_key' => 'endpoint'],
            'mailgun_scheme' => ['label' => 'Mailgun Scheme', 'config_key' => 'scheme'],
        ],
        'ses' => [
            'ses_key' => ['label' => 'AWS Access Key ID', 'config_key' => 'key'],
            'ses_secret' => ['label' => 'AWS Secret Access Key', 'config_key' => 'secret'],
            'ses_region' => ['label' => 'AWS Region', 'config_key' => 'region'],
        ],
        'ses-v2' => [
            'ses_key' => ['label' => 'AWS Access Key ID', 'config_key' => 'key'],
            'ses_secret' => ['label' => 'AWS Secret Access Key', 'config_key' => 'secret'],
            'ses_region' => ['label' => 'AWS Region', 'config_key' => 'region'],
        ],
        'postmark' => [
            'postmark_token' => ['label' => 'Postmark Server Token', 'config_key' => 'token'],
            'postmark_message_stream_id' => ['label' => 'Message Stream ID', 'config_key' => 'message_stream_id'],
        ],
        'sendgrid' => [
            'sendgrid_api_key' => ['label' => 'SendGrid API Key', 'config_key' => 'api_key'],
        ],
        'resend' => [
            'resend_api_key' => ['label' => 'Resend API Key', 'config_key' => 'api_key'],
        ],
        'mail' => [],
    ];

    private array $sensitiveConfigKeys = ['password', 'secret', 'token', 'api_key', 'key'];

    public function __construct(private MailConfigurator $configurator)
    {
    }

    public function edit(Request $request): View
    {
        $this->authorizeAdmin($request);
        $settings = MailSetting::query()->firstOrNew();

        return view('settings.mail', [
            'settings' => $settings,
            'mailers' => $this->mailers,
            'fieldMap' => $this->fieldMap,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $existingSettings = MailSetting::query()->first();

        $mailer = $request->input('mailer', $existingSettings?->mailer ?? 'mail');

        $rules = [
            'mailer' => ['required', Rule::in(array_keys($this->mailers))],
            'from_name' => ['nullable', 'string', 'max:255'],
            'from_address' => ['nullable', 'email'],
        ];

        $rules = array_merge(
            $rules,
            $this->validationRulesForMailer($mailer, $existingSettings?->mailer, $existingSettings?->config ?? [])
        );

        $validated = $request->validate($rules);

        $config = $this->extractConfigForMailer(
            $request,
            $mailer,
            $existingSettings?->config ?? [],
            $existingSettings?->mailer
        );

        $settings = $existingSettings ?? MailSetting::query()->firstOrNew();

        $settings->fill([
            'mailer' => $validated['mailer'],
            'from_name' => $validated['from_name'] ?? null,
            'from_address' => $validated['from_address'] ?? null,
            'config' => $config,
        ])->save();

        $this->configurator->apply();

        return redirect()
            ->route('settings.mail.edit')
            ->with('status', 'Mail settings updated successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user, 403);
        abort_if(! $user->hasUserType(UserType::ADMIN), 403);
    }

    private function validationRulesForMailer(?string $mailer, ?string $previousMailer = null, array $existingConfig = []): array
    {
        return match ($mailer) {
            'smtp' => [
                'smtp_host' => ['required', 'string', 'max:255'],
                'smtp_port' => ['required', 'integer'],
                'smtp_username' => ['nullable', 'string', 'max:255'],
                'smtp_password' => $this->credentialRule('password', $mailer, $previousMailer, $existingConfig),
                'smtp_encryption' => ['nullable', 'in:ssl,tls,starttls'],
            ],
            'mailgun' => [
                'mailgun_domain' => ['required', 'string', 'max:255'],
                'mailgun_secret' => $this->credentialRule('secret', $mailer, $previousMailer, $existingConfig),
                'mailgun_endpoint' => ['nullable', 'string', 'max:255'],
                'mailgun_scheme' => ['nullable', 'in:http,https'],
            ],
            'ses', 'ses-v2' => [
                'ses_key' => $this->credentialRule('key', $mailer, $previousMailer, $existingConfig),
                'ses_secret' => $this->credentialRule('secret', $mailer, $previousMailer, $existingConfig),
                'ses_region' => ['required', 'string', 'max:255'],
            ],
            'postmark' => [
                'postmark_token' => $this->credentialRule('token', $mailer, $previousMailer, $existingConfig),
                'postmark_message_stream_id' => ['nullable', 'string', 'max:255'],
            ],
            'sendgrid' => [
                'sendgrid_api_key' => $this->credentialRule('api_key', $mailer, $previousMailer, $existingConfig),
            ],
            'resend' => [
                'resend_api_key' => $this->credentialRule('api_key', $mailer, $previousMailer, $existingConfig),
            ],
            default => [],
        };
    }

    private function extractConfigForMailer(Request $request, ?string $mailer, array $existingConfig = [], ?string $previousMailer = null): array
    {
        $config = [];

        foreach (Arr::get($this->fieldMap, $mailer, []) as $field => $meta) {
            $value = $request->input($field);

            if ($value !== null) {
                if ($value === '' && ! $this->shouldPreserve($meta['config_key'])) {
                    $config[$meta['config_key']] = null;
                } elseif ($value !== '') {
                    $config[$meta['config_key']] = $value;
                }

                if ($value !== '' || ! $this->shouldPreserve($meta['config_key'])) {
                    continue;
                }
            }

            if ($mailer === $previousMailer && array_key_exists($meta['config_key'], $existingConfig)) {
                $config[$meta['config_key']] = $existingConfig[$meta['config_key']];
            }
        }

        return $config;
    }

    private function shouldPreserve(string $configKey): bool
    {
        return in_array($configKey, $this->sensitiveConfigKeys, true);
    }

    private function credentialRule(string $configKey, ?string $mailer, ?string $previousMailer, array $existingConfig): array
    {
        return $this->hasPersistedValue($configKey, $mailer, $previousMailer, $existingConfig)
            ? ['nullable', 'string']
            : ['required', 'string'];
    }

    private function hasPersistedValue(string $configKey, ?string $mailer, ?string $previousMailer, array $existingConfig): bool
    {
        return $mailer === $previousMailer && array_key_exists($configKey, $existingConfig ?? [])
            && filled($existingConfig[$configKey]);
    }
}
