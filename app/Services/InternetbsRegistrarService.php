<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InternetbsRegistrarService
{
    protected string $apiKey;
    protected string $apiUser;
    protected bool $sandbox;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.internetbs.api_key');
        $this->apiUser = config('services.internetbs.api_user');
        $this->sandbox = config('services.internetbs.sandbox', true);
        $this->apiUrl = $this->sandbox
            ? 'https://testapi.internetbs.net'
            : 'https://api.internetbs.net';
    }

    /**
     * Check domain availability
     */
    public function checkAvailability(string $domain): array
    {
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->apiUrl}/Domain/CheckAvailability", [
                'Domain' => $domain,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'available' => $data['Available'] ?? false,
                    'premium' => $data['Premium'] ?? false,
                    'price' => $data['Price'] ?? null,
                ];
            }
            return ['available' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Internet.bs domain availability failed', [
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
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/Domain/Create", [
                'Domain' => $data['domain'],
                'Years' => $data['years'],
                'Registrant' => [
                    'Name' => $data['contact_name'],
                    'Email' => $data['contact_email'],
                    'Phone' => $data['contact_phone'],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'order_id' => $result['OrderId'] ?? null,
                    'expires_at' => $result['ExpiresAt'] ?? null,
                ];
            }
            return ['success' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Internet.bs domain registration failed', [
                'domain' => $data['domain'],
                'error' => $e->getMessage()];
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
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/Domain/Renew", [
                'Domain' => $domain,
                'Years' => $years,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'expires_at' => $result['ExpiresAt'] ?? null,
                ];
            }
            return ['success' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Internet.bs domain renewal failed', [
                'domain' => $domain,
                'error' => $e->getMessage()];
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
