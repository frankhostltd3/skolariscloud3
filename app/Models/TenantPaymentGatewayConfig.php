<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TenantPaymentGatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
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
     * Scope: Active gateways.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('gateway');
    }
}
