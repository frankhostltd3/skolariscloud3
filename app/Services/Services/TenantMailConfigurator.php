<?php

namespace App\Services;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Mail\MailManager;

class TenantMailConfigurator
{
    protected ConfigRepository $config;
    protected MailManager $mailManager;

    /**
     * Baseline configuration captured from the central context so we can restore it after tenancy ends.
     *
     * @var array<string, mixed>
     */
    protected static array $baseline = [];

    /**
     * Config keys we actively override when applying tenant settings.
     *
     * @var string[]
     */
    protected const TRACKED_KEYS = [
        'mail.default',
        'mail.mailers.smtp.host',
        'mail.mailers.smtp.port',
        'mail.mailers.smtp.encryption',
        'mail.mailers.smtp.username',
        'mail.mailers.smtp.password',
        'mail.from.address',
        'mail.from.name',
    ];

    public function __construct(ConfigRepository $config, MailManager $mailManager)
    {
        $this->config = $config;
        $this->mailManager = $mailManager;
    }

    /**
     * Apply the active tenant's mail settings to the runtime mail configuration.
     */
    public function applyFromSettings(): void
    {
        $this->rememberBaseline();

    $driver = strtolower((string) setting('mail_driver', 'smtp')) ?: 'smtp';
    $host = (string) setting('mail_host', $this->baseline('mail.mailers.smtp.host', '127.0.0.1'));
        $port = (int) setting('mail_port', $this->baseline('mail.mailers.smtp.port', 587));
        $encryption = (string) setting('mail_encryption', $this->baseline('mail.mailers.smtp.encryption')); // may return empty string
        if (strtolower($encryption) === 'none') {
            $encryption = '';
        }
        $username = (string) setting('mail_username', $this->baseline('mail.mailers.smtp.username'));
        $password = (string) setting('mail_password', $this->baseline('mail.mailers.smtp.password'));
    $fromAddress = (string) setting('mail_from_address', $this->baseline('mail.from.address'));
    $fromName = (string) setting('mail_from_name', $this->baseline('mail.from.name', $this->config->get('app.name', 'SkolarisCloud')));

        // Update default mailer (mailgun/sendmail fall back to the configured driver name)
    $this->config->set('mail.default', $driver);
    $this->mailManager->setDefaultDriver($driver);
    $this->mailManager->forgetMailers();

        // Apply SMTP transport overrides even if another driver is selected – Laravel will ignore them when unused
        $this->config->set('mail.mailers.smtp.host', $host ?: $this->baseline('mail.mailers.smtp.host'));
        $this->config->set('mail.mailers.smtp.port', $port > 0 ? $port : $this->baseline('mail.mailers.smtp.port'));
        $this->config->set('mail.mailers.smtp.encryption', $encryption !== '' ? $encryption : $this->baseline('mail.mailers.smtp.encryption'));
        $this->config->set('mail.mailers.smtp.username', $username !== '' ? $username : $this->baseline('mail.mailers.smtp.username'));
        $this->config->set('mail.mailers.smtp.password', $password !== '' ? $password : $this->baseline('mail.mailers.smtp.password'));

        if ($fromAddress !== '') {
            $this->config->set('mail.from.address', $fromAddress);
        } else {
            $this->config->set('mail.from.address', $this->baseline('mail.from.address'));
        }

        if ($fromName !== '') {
            $this->config->set('mail.from.name', $fromName);
        } else {
            $this->config->set('mail.from.name', $this->baseline('mail.from.name'));
        }
    }

    /**
     * Restore the original (central) mail configuration after tenancy ends.
     */
    public function restoreBaseline(): void
    {
        $this->rememberBaseline();

        foreach (self::TRACKED_KEYS as $key) {
            $this->config->set($key, $this->baseline($key));
        }

        if ($default = $this->baseline('mail.default')) {
            $this->mailManager->setDefaultDriver($default);
        }
        $this->mailManager->forgetMailers();
    }

    /**
     * Ensure we have a copy of the original config before mutating values.
     */
    protected function rememberBaseline(): void
    {
        if (self::$baseline !== []) {
            return;
        }

        foreach (self::TRACKED_KEYS as $key) {
            self::$baseline[$key] = $this->config->get($key);
        }
    }

    /**
     * Helper to fetch a stored baseline value with an optional fallback.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function baseline(string $key, $default = null)
    {
        if (self::$baseline === []) {
            $this->rememberBaseline();
        }

        return self::$baseline[$key] ?? $default;
    }
}
