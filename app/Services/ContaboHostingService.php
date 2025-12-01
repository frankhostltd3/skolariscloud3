<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContaboHostingService
{
    protected string $apiKey;
    protected string $apiUser;
    protected string $apiUrl = 'https://api.contabo.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.contabo.api_key') ?? '';
        $this->apiUser = config('services.contabo.api_user') ?? '';
    }

    /**
     * List available hosting plans
     */
    public function getPlans(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->apiUrl}/hosting/plans");

            if ($response->successful()) {
                return $response->json()['plans'] ?? [];
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Contabo get plans failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Provision hosting for a domain
     */
    public function provisionHosting(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post("{$this->apiUrl}/hosting/provision", [
                'domain' => $data['domain'],
                'plan_id' => $data['plan_id'],
                'school_id' => $data['school_id'],
                'contact' => [
                    'name' => $data['contact_name'],
                    'email' => $data['contact_email'],
                    'phone' => $data['contact_phone'],
                ],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'hosting_id' => $response->json()['hosting_id'] ?? null,
                    'details' => $response->json()['details'] ?? [],
                ];
            }
            return ['success' => false, 'error' => 'API error'];
        } catch (\Exception $e) {
            Log::error('Contabo hosting provision failed', [
                'domain' => $data['domain'],
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
