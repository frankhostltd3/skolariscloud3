<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class MobileMoneyGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'provider',
        'country_code',
        'currency_code',
        'api_base_url',
        'public_key',
        'secret_key',
        'api_user',
        'api_password',
        'subscription_key',
        'webhook_secret',
        'encryption_key',
        'client_id',
        'client_secret',
        'access_token',
        'token_expires_at',
        'merchant_id',
        'merchant_name',
        'short_code',
        'till_number',
        'account_number',
        'sender_phone',
        'callback_phone',
        'environment',
        'callback_url',
        'return_url',
        'cancel_url',
        'custom_fields',
        'supported_networks',
        'fee_structure',
        'is_active',
        'is_default',
        'sort_order',
        'description',
        'logo_url',
        'support_email',
        'support_phone',
        'last_tested_at',
        'test_successful',
        'test_message',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'supported_networks' => 'array',
        'fee_structure' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'test_successful' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_tested_at' => 'datetime',
    ];

    protected $hidden = [
        'secret_key',
        'api_password',
        'webhook_secret',
        'encryption_key',
        'client_secret',
        'access_token',
    ];

    // ===========================
    // ENCRYPTED FIELD ACCESSORS
    // ===========================

    // Public Key
    public function setPublicKeyAttribute($value)
    {
        $this->attributes['public_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPublicKeyAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Secret Key
    public function setSecretKeyAttribute($value)
    {
        $this->attributes['secret_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getSecretKeyAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // API User
    public function setApiUserAttribute($value)
    {
        $this->attributes['api_user'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiUserAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // API Password
    public function setApiPasswordAttribute($value)
    {
        $this->attributes['api_password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiPasswordAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Subscription Key
    public function setSubscriptionKeyAttribute($value)
    {
        $this->attributes['subscription_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getSubscriptionKeyAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Webhook Secret
    public function setWebhookSecretAttribute($value)
    {
        $this->attributes['webhook_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getWebhookSecretAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Encryption Key
    public function setEncryptionKeyAttribute($value)
    {
        $this->attributes['encryption_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getEncryptionKeyAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Client ID
    public function setClientIdAttribute($value)
    {
        $this->attributes['client_id'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getClientIdAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Client Secret
    public function setClientSecretAttribute($value)
    {
        $this->attributes['client_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getClientSecretAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Access Token
    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // API Base URL
    public function setApiBaseUrlAttribute($value)
    {
        $this->attributes['api_base_url'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiBaseUrlAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool($query, $schoolId = null)
    {
        $schoolId = $schoolId ?? request()->attributes->get('currentSchool')?->id;
        return $query->where('school_id', $schoolId);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeProduction($query)
    {
        return $query->where('environment', 'production');
    }

    public function scopeSandbox($query)
    {
        return $query->where('environment', 'sandbox');
    }

    // ===========================
    // HELPER METHODS
    // ===========================

    /**
     * Get the default gateway for a school
     */
    public static function getDefault($schoolId = null)
    {
        $schoolId = $schoolId ?? request()->attributes->get('currentSchool')?->id;
        
        return static::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Set this gateway as default (removes default from others)
     */
    public function setAsDefault(): void
    {
        // Remove default from other gateways
        static::where('school_id', $this->school_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true, 'is_active' => true]);
    }

    /**
     * Check if access token is still valid
     */
    public function hasValidToken(): bool
    {
        return $this->access_token && 
               $this->token_expires_at && 
               $this->token_expires_at->isFuture();
    }

    /**
     * Get the API endpoint for a specific action
     */
    public function getEndpoint(string $action): string
    {
        $baseUrl = rtrim($this->api_base_url, '/');
        
        $endpoints = $this->getProviderEndpoints();
        
        return $baseUrl . ($endpoints[$action] ?? '');
    }

    /**
     * Get provider-specific endpoints
     */
    protected function getProviderEndpoints(): array
    {
        $endpoints = [
            'mtn_momo' => [
                'token' => '/collection/token/',
                'request_to_pay' => '/collection/v1_0/requesttopay',
                'request_status' => '/collection/v1_0/requesttopay/{referenceId}',
                'account_balance' => '/collection/v1_0/account/balance',
            ],
            'mpesa' => [
                'token' => '/oauth/v1/generate?grant_type=client_credentials',
                'stk_push' => '/mpesa/stkpush/v1/processrequest',
                'stk_query' => '/mpesa/stkpushquery/v1/query',
                'c2b_register' => '/mpesa/c2b/v1/registerurl',
                'b2c' => '/mpesa/b2c/v1/paymentrequest',
            ],
            'flutterwave' => [
                'charge' => '/v3/charges?type=mobile_money_{country}',
                'verify' => '/v3/transactions/{id}/verify',
                'banks' => '/v3/banks/{country}',
            ],
            'paystack' => [
                'initialize' => '/transaction/initialize',
                'verify' => '/transaction/verify/{reference}',
                'charge' => '/charge',
            ],
            'airtel_money' => [
                'token' => '/auth/oauth2/token',
                'payments' => '/merchant/v1/payments/',
                'status' => '/standard/v1/payments/{id}',
            ],
        ];

        return $endpoints[$this->provider] ?? [];
    }

    /**
     * Get full configuration as array (for service use)
     */
    public function getConfiguration(): array
    {
        return [
            'provider' => $this->provider,
            'environment' => $this->environment,
            'api_base_url' => $this->api_base_url,
            'public_key' => $this->public_key,
            'secret_key' => $this->secret_key,
            'api_user' => $this->api_user,
            'api_password' => $this->api_password,
            'subscription_key' => $this->subscription_key,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'merchant_id' => $this->merchant_id,
            'short_code' => $this->short_code,
            'till_number' => $this->till_number,
            'callback_url' => $this->callback_url,
            'custom_fields' => $this->custom_fields ?? [],
        ];
    }

    /**
     * Get display information
     */
    public function getDisplayInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'provider' => $this->provider,
            'country' => $this->country_code,
            'currency' => $this->currency_code,
            'environment' => $this->environment,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'logo' => $this->logo_url ?? $this->getDefaultLogo(),
            'networks' => $this->supported_networks,
        ];
    }

    /**
     * Get default logo based on provider
     */
    public function getDefaultLogo(): string
    {
        $logos = [
            'mtn_momo' => '/images/gateways/mtn-momo.png',
            'mpesa' => '/images/gateways/mpesa.png',
            'airtel_money' => '/images/gateways/airtel-money.png',
            'flutterwave' => '/images/gateways/flutterwave.png',
            'paystack' => '/images/gateways/paystack.png',
            'stripe' => '/images/gateways/stripe.png',
            'paypal' => '/images/gateways/paypal.png',
        ];

        return $logos[$this->provider] ?? '/images/gateways/default.png';
    }

    /**
     * Record test result
     */
    public function recordTestResult(bool $success, string $message = null): void
    {
        $this->update([
            'last_tested_at' => now(),
            'test_successful' => $success,
            'test_message' => $message,
        ]);
    }

    /**
     * Get provider display name
     */
    public function getProviderNameAttribute(): string
    {
        $names = self::getProviderList();
        return $names[$this->provider]['name'] ?? ucfirst(str_replace('_', ' ', $this->provider));
    }

    /**
     * Get list of all supported providers with their configurations
     */
    public static function getProviderList(): array
    {
        return [
            // Africa
            'mtn_momo' => [
                'name' => 'MTN Mobile Money',
                'countries' => ['UG', 'GH', 'CI', 'CM', 'BJ', 'CG', 'GN', 'LR', 'RW', 'ZA', 'SS', 'SZ', 'ZM'],
                'currencies' => ['UGX', 'GHS', 'XOF', 'XAF', 'GNF', 'LRD', 'RWF', 'ZAR', 'SSP', 'SZL', 'ZMW'],
                'required_fields' => ['api_base_url', 'subscription_key', 'api_user', 'api_password', 'callback_url'],
                'optional_fields' => ['environment', 'merchant_id'],
            ],
            'mpesa' => [
                'name' => 'M-Pesa (Safaricom)',
                'countries' => ['KE', 'TZ', 'MZ', 'GH', 'EG'],
                'currencies' => ['KES', 'TZS', 'MZN', 'GHS', 'EGP'],
                'required_fields' => ['api_base_url', 'client_id', 'client_secret', 'short_code', 'passkey', 'callback_url'],
                'optional_fields' => ['environment', 'initiator_name', 'security_credential'],
            ],
            'airtel_money' => [
                'name' => 'Airtel Money',
                'countries' => ['UG', 'KE', 'TZ', 'RW', 'ZM', 'MW', 'NG', 'CG', 'GA', 'NE', 'TD', 'MG', 'SC'],
                'currencies' => ['UGX', 'KES', 'TZS', 'RWF', 'ZMW', 'MWK', 'NGN', 'XAF', 'XOF', 'MGA', 'SCR'],
                'required_fields' => ['api_base_url', 'client_id', 'client_secret', 'callback_url'],
                'optional_fields' => ['environment', 'merchant_id'],
            ],
            'orange_money' => [
                'name' => 'Orange Money',
                'countries' => ['SN', 'ML', 'CI', 'BF', 'NE', 'GN', 'CM', 'MG', 'MA', 'TN', 'EG', 'JO'],
                'currencies' => ['XOF', 'XAF', 'MGA', 'MAD', 'TND', 'EGP', 'JOD'],
                'required_fields' => ['api_base_url', 'client_id', 'client_secret', 'merchant_key'],
                'optional_fields' => ['environment', 'authorization_header'],
            ],
            'flutterwave' => [
                'name' => 'Flutterwave (Rave)',
                'countries' => ['NG', 'GH', 'KE', 'UG', 'TZ', 'ZA', 'RW', 'ZM', 'CM', 'CI'],
                'currencies' => ['NGN', 'GHS', 'KES', 'UGX', 'TZS', 'ZAR', 'RWF', 'ZMW', 'XAF', 'XOF', 'USD'],
                'required_fields' => ['public_key', 'secret_key', 'encryption_key'],
                'optional_fields' => ['webhook_secret', 'environment'],
            ],
            'paystack' => [
                'name' => 'Paystack',
                'countries' => ['NG', 'GH', 'ZA', 'KE'],
                'currencies' => ['NGN', 'GHS', 'ZAR', 'KES', 'USD'],
                'required_fields' => ['public_key', 'secret_key'],
                'optional_fields' => ['webhook_secret', 'environment'],
            ],
            'dpo' => [
                'name' => 'DPO (Direct Pay Online)',
                'countries' => ['KE', 'TZ', 'UG', 'ZA', 'GH', 'NA', 'BW', 'MW', 'ZM', 'ZW', 'MU', 'RW'],
                'currencies' => ['KES', 'TZS', 'UGX', 'ZAR', 'GHS', 'NAD', 'BWP', 'MWK', 'ZMW', 'ZWL', 'MUR', 'RWF', 'USD'],
                'required_fields' => ['api_base_url', 'company_token', 'service_type'],
                'optional_fields' => ['callback_url', 'return_url'],
            ],
            'yo_payments' => [
                'name' => 'Yo! Payments Uganda',
                'countries' => ['UG'],
                'currencies' => ['UGX'],
                'required_fields' => ['api_base_url', 'api_user', 'api_password'],
                'optional_fields' => ['callback_url', 'external_reference'],
            ],

            // Asia
            'gcash' => [
                'name' => 'GCash (Philippines)',
                'countries' => ['PH'],
                'currencies' => ['PHP'],
                'required_fields' => ['api_base_url', 'client_id', 'client_secret', 'merchant_id'],
                'optional_fields' => ['callback_url', 'environment'],
            ],
            'grabpay' => [
                'name' => 'GrabPay',
                'countries' => ['SG', 'MY', 'PH', 'VN', 'TH', 'ID'],
                'currencies' => ['SGD', 'MYR', 'PHP', 'VND', 'THB', 'IDR'],
                'required_fields' => ['api_base_url', 'partner_id', 'partner_secret', 'merchant_id'],
                'optional_fields' => ['callback_url', 'environment'],
            ],
            'paytm' => [
                'name' => 'Paytm (India)',
                'countries' => ['IN'],
                'currencies' => ['INR'],
                'required_fields' => ['merchant_id', 'merchant_key', 'website', 'industry_type'],
                'optional_fields' => ['callback_url', 'environment'],
            ],
            'gopay' => [
                'name' => 'GoPay (Indonesia)',
                'countries' => ['ID'],
                'currencies' => ['IDR'],
                'required_fields' => ['api_base_url', 'client_id', 'client_secret'],
                'optional_fields' => ['callback_url', 'environment'],
            ],

            // Latin America
            'pix' => [
                'name' => 'PIX (Brazil)',
                'countries' => ['BR'],
                'currencies' => ['BRL'],
                'required_fields' => ['api_base_url', 'client_id', 'client_secret', 'pix_key'],
                'optional_fields' => ['webhook_url', 'certificate_path'],
            ],
            'mercadopago' => [
                'name' => 'Mercado Pago',
                'countries' => ['AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'UY'],
                'currencies' => ['ARS', 'BRL', 'CLP', 'COP', 'MXN', 'PEN', 'UYU'],
                'required_fields' => ['public_key', 'access_token'],
                'optional_fields' => ['webhook_url', 'environment'],
            ],

            // Global
            'stripe' => [
                'name' => 'Stripe',
                'countries' => ['*'],
                'currencies' => ['*'],
                'required_fields' => ['public_key', 'secret_key'],
                'optional_fields' => ['webhook_secret', 'environment'],
            ],
            'paypal' => [
                'name' => 'PayPal',
                'countries' => ['*'],
                'currencies' => ['*'],
                'required_fields' => ['client_id', 'client_secret'],
                'optional_fields' => ['webhook_id', 'environment'],
            ],

            // Custom
            'custom' => [
                'name' => 'Custom Gateway',
                'countries' => ['*'],
                'currencies' => ['*'],
                'required_fields' => ['api_base_url'],
                'optional_fields' => ['public_key', 'secret_key', 'api_user', 'api_password', 'custom_fields'],
            ],
        ];
    }

    /**
     * Get providers available for a specific country
     */
    public static function getProvidersForCountry(string $countryCode): array
    {
        $providers = [];
        
        foreach (self::getProviderList() as $key => $provider) {
            if (in_array($countryCode, $provider['countries']) || in_array('*', $provider['countries'])) {
                $providers[$key] = $provider;
            }
        }
        
        return $providers;
    }
}
