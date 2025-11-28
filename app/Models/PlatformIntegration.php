<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PlatformIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'is_enabled',
        'api_key',
        'api_secret',
        'client_id',
        'client_secret',
        'redirect_uri',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'additional_settings',
        'managed_by_admin',
        'status',
        'status_message',
        'last_tested_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'token_expires_at' => 'datetime',
        'managed_by_admin' => 'boolean',
        'last_tested_at' => 'datetime',
        'additional_settings' => 'array',
    ];

    /**
     * Get decrypted API key
     */
    public function getDecryptedApiKeyAttribute()
    {
        return $this->api_key ? Crypt::decryptString($this->api_key) : null;
    }

    /**
     * Get decrypted API secret
     */
    public function getDecryptedApiSecretAttribute()
    {
        return $this->api_secret ? Crypt::decryptString($this->api_secret) : null;
    }

    /**
     * Get decrypted client ID
     */
    public function getDecryptedClientIdAttribute()
    {
        return $this->client_id ? Crypt::decryptString($this->client_id) : null;
    }

    /**
     * Get decrypted client secret
     */
    public function getDecryptedClientSecretAttribute()
    {
        return $this->client_secret ? Crypt::decryptString($this->client_secret) : null;
    }

    /**
     * Set encrypted API key
     */
    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted API secret
     */
    public function setApiSecretAttribute($value)
    {
        $this->attributes['api_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted client ID
     */
    public function setClientIdAttribute($value)
    {
        $this->attributes['client_id'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted client secret
     */
    public function setClientSecretAttribute($value)
    {
        $this->attributes['client_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Check if platform is configured
     */
    public function isConfigured(): bool
    {
        return $this->is_enabled && 
               ($this->api_key !== null || $this->client_id !== null);
    }

    public function managedByAdmin(): bool
    {
        return (bool) $this->managed_by_admin;
    }

    /**
     * Check if token is expired
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        return now()->gte($this->token_expires_at);
    }

    /**
     * Determine if the integrations table exists for the current tenant connection.
     */
    public static function tableExists(): bool
    {
        $instance = new static();

        try {
            $connection = $instance->getConnectionName() ?? $instance->getConnection()->getName();
        } catch (\Throwable $e) {
            return false;
        }

        return tenant_table_exists($instance->getTable(), $connection);
    }

    /**
     * Get platform configuration by name
     */
    public static function getByPlatform(string $platform): ?self
    {
        if (! static::tableExists()) {
            return null;
        }

        return static::where('platform', $platform)->first();
    }
}
