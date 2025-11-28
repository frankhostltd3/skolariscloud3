<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'context',
        'is_active',
        'is_custom',
        'is_test_mode',
        'credentials',
        'settings',
        'custom_config',
        'supported_currencies',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_custom' => 'boolean',
        'is_test_mode' => 'boolean',
        'settings' => 'array',
        'custom_config' => 'array',
        'supported_currencies' => 'array',
        'display_order' => 'integer',
    ];

    /**
     * Get the connection for this model.
     */
    public function getConnectionName(): ?string
    {
        return central_connection();
    }

    /**
     * Get decrypted credentials.
     */
    public function getDecryptedCredentials(): array
    {
        if (empty($this->credentials)) {
            return [];
        }

        try {
            return Crypt::decrypt($this->credentials);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set encrypted credentials.
     */
    public function setEncryptedCredentials(array $credentials): void
    {
        $this->credentials = Crypt::encrypt($credentials);
    }

    /**
     * Get credential value by key.
     */
    public function getCredential(string $key, mixed $default = null): mixed
    {
        $credentials = $this->getDecryptedCredentials();
        return $credentials[$key] ?? $default;
    }

    /**
     * Get gateway display name.
     */
    public function getDisplayNameAttribute(): string
    {
        // Use custom name if this is a custom gateway
        if ($this->is_custom && isset($this->custom_config['name'])) {
            return $this->custom_config['name'];
        }

        return match($this->gateway) {
            'paypal' => 'PayPal',
            'flutterwave' => 'Flutterwave',
            'pesapal' => 'PesaPal',
            'dpo' => 'DPO Pay',
            'mtn_momo' => 'MTN Mobile Money',
            'airtel_money' => 'Airtel Money',
            'mpesa' => 'M-PESA',
            default => ucfirst(str_replace('_', ' ', $this->gateway)),
        };
    }

    /**
     * Get gateway logo.
     */
    public function getLogoAttribute(): string
    {
        // Use custom logo if this is a custom gateway
        if ($this->is_custom && isset($this->custom_config['logo'])) {
            return $this->custom_config['logo'];
        }

        return match($this->gateway) {
            'paypal' => '🅿️',
            'flutterwave' => '💳',
            'pesapal' => '💰',
            'dpo' => '💵',
            'mtn_momo' => '📱',
            'airtel_money' => '📞',
            'mpesa' => '💸',
            default => '💳',
        };
    }

    /**
     * Get gateway description.
     */
    public function getDescriptionAttribute(): string
    {
        // Use custom description if this is a custom gateway
        if ($this->is_custom && isset($this->custom_config['description'])) {
            return $this->custom_config['description'];
        }

        return match($this->gateway) {
            'paypal' => 'Accept payments worldwide via PayPal',
            'flutterwave' => 'Accept payments across Africa with Flutterwave',
            'pesapal' => 'East Africa payment gateway',
            'dpo' => 'Africa-wide payment processing',
            'mtn_momo' => 'MTN Mobile Money for Uganda, Ghana, Zambia',
            'airtel_money' => 'Airtel Money for East Africa',
            'mpesa' => 'Kenya M-PESA payments',
            default => 'Payment gateway',
        };
    }

    /**
     * Get credential fields for this gateway.
     */
    public function getCredentialFields(): array
    {
        // Use custom fields if this is a custom gateway
        if ($this->is_custom && isset($this->custom_config['fields'])) {
            return $this->custom_config['fields'];
        }

        return $this->getDefaultCredentialFields();
    }

    /**
     * Get default credential fields for known gateways.
     */
    private function getDefaultCredentialFields(): array
    {
        return match($this->gateway) {
            'paypal' => [
                'client_id' => ['label' => 'Client ID', 'type' => 'text', 'required' => true],
                'client_secret' => ['label' => 'Client Secret', 'type' => 'password', 'required' => true],
                'webhook_id' => ['label' => 'Webhook ID', 'type' => 'text', 'required' => false],
            ],
            'flutterwave' => [
                'public_key' => ['label' => 'Public Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'encryption_key' => ['label' => 'Encryption Key', 'type' => 'password', 'required' => true],
            ],
            default => [],
        };
    }

    /**
     * Scope: Active gateways.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By context.
     */
    public function scopeContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    /**
     * Scope: Ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('gateway');
    }
}
