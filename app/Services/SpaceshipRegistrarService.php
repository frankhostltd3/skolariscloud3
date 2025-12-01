<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpaceshipRegistrarService
{
    protected string $apiKey;
    protected string $apiUser;
    protected bool $sandbox;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.spaceship.api_key') ?? '';
        $this->apiUser = config('services.spaceship.api_user') ?? '';
        $this->sandbox = config('services.spaceship.sandbox', true);
        $this->apiUrl = $this->sandbox
            ? 'https://api.sandbox.spaceship.com/v1'
            : 'https://api.spaceship.com/v1';
    }

    /**
     * Check domain availability
     */
    public function checkAvailability(string $domain): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->apiUrl}/domains/availability", [
                'domain' => $domain,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'available' => $data['available'] ?? false,
                    'premium' => $data['premium'] ?? false,
                    'price' => $data['price'] ?? null,
                ];
            }
            return ['available' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Spaceship domain availability failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return ['available' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Register domain
     */
    public function registerDomain(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/domains/register", [
                'domain' => $data['domain'],
                'years' => $data['years'],
                'contact' => [
                    'name' => $data['contact_name'],
                    'email' => $data['contact_email'],
                    'phone' => $data['contact_phone'],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'order_id' => $result['order_id'] ?? null,
                    'expires_at' => $result['expires_at'] ?? null,
                ];
            }
            return ['success' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Spaceship domain registration failed', [
                'domain' => $data['domain'],
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Renew domain
     */
    public function renewDomain(string $domain, int $years): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/domains/renew", [
                'domain' => $domain,
                'years' => $years,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'expires_at' => $result['expires_at'] ?? null,
                ];
            }
            return ['success' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Spaceship domain renewal failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
