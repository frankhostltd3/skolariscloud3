<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DomainRegistrarService
{
    protected string $apiKey;
    protected string $apiUser;
    protected string $apiUrl;
    protected bool $sandbox;

    public function __construct()
    {
        // Namecheap integration temporarily disabled
        // $this->apiKey = config('services.namecheap.api_key');
        // $this->apiUser = config('services.namecheap.api_user');
        // $this->sandbox = config('services.namecheap.sandbox', true);
        // $this->apiUrl = $this->sandbox
        //     ? 'https://api.sandbox.namecheap.com/xml.response'
        //     : 'https://api.namecheap.com/xml.response';
    }

    /**
     * Check domain availability
     */
    public function checkAvailability(string $domain): array
    {
        // Namecheap API temporarily disabled
        return [
            'available' => false,
            'error' => 'Namecheap API integration is currently disabled.'
        ];
    }

    /**
     * Get domain pricing
     */
    public function getDomainPrice(string $domain): float
    {
        // Extract TLD
        $parts = explode('.', $domain);
        $tld = '.' . end($parts);

        // Default pricing (should be fetched from API in production)
        $pricing = [
            '.com' => 12.98,
            '.school' => 34.88,
            '.academy' => 34.88,
            '.org' => 14.98,
            '.co.ke' => 15.00,
            '.net' => 14.98,
            '.edu' => 24.98,
        ];

        return $pricing[$tld] ?? 19.99;
    }

    /**
     * Register a domain
     */
    public function registerDomain(array $data): array
    {
        try {
            $params = [
                'ApiUser' => $this->apiUser,
                'ApiKey' => $this->apiKey,
                'UserName' => $this->apiUser,
                'Command' => 'namecheap.domains.create',
                'ClientIp' => request()->ip(),
                'DomainName' => $data['domain'],
                'Years' => $this->getBillingYears($data['billing_cycle']),

                // Registrant Contact
                'RegistrantFirstName' => $this->parseFirstName($data['contact_name']),
                'RegistrantLastName' => $this->parseLastName($data['contact_name']),
                'RegistrantAddress1' => $data['address'] ?? 'PO Box 12345',
                'RegistrantCity' => $data['city'] ?? 'Nairobi',
                'RegistrantStateProvince' => $data['state'] ?? 'Nairobi',
                'RegistrantPostalCode' => $data['postal_code'] ?? '00100',
                'RegistrantCountry' => $data['country'] ?? 'KE',
                'RegistrantPhone' => $data['contact_phone'] ?? '+254700000000',
                'RegistrantEmailAddress' => $data['contact_email'],

                // Tech/Admin/Billing contacts (same as registrant)
                'TechFirstName' => $this->parseFirstName($data['contact_name']),
                'TechLastName' => $this->parseLastName($data['contact_name']),
                'TechAddress1' => $data['address'] ?? 'PO Box 12345',
                'TechCity' => $data['city'] ?? 'Nairobi',
                'TechStateProvince' => $data['state'] ?? 'Nairobi',
                'TechPostalCode' => $data['postal_code'] ?? '00100',
                'TechCountry' => $data['country'] ?? 'KE',
                'TechPhone' => $data['contact_phone'] ?? '+254700000000',
                'TechEmailAddress' => $data['contact_email'],

                'AdminFirstName' => $this->parseFirstName($data['contact_name']),
                'AdminLastName' => $this->parseLastName($data['contact_name']),
                'AdminAddress1' => $data['address'] ?? 'PO Box 12345',
                'AdminCity' => $data['city'] ?? 'Nairobi',
                'AdminStateProvince' => $data['state'] ?? 'Nairobi',
                'AdminPostalCode' => $data['postal_code'] ?? '00100',
                'AdminCountry' => $data['country'] ?? 'KE',
                'AdminPhone' => $data['contact_phone'] ?? '+254700000000',
                'AdminEmailAddress' => $data['contact_email'],
            ];

            $response = Http::post($this->apiUrl, $params);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());

                if ((string) $xml->CommandResponse->DomainCreateResult['Registered'] === 'true') {
                    return [
                        'success' => true,
                        'order_id' => (string) $xml->CommandResponse->DomainCreateResult['OrderID'],
                        'transaction_id' => (string) $xml->CommandResponse->DomainCreateResult['TransactionID'],
                        'domain' => (string) $xml->CommandResponse->DomainCreateResult['Domain'],
                        'expires_at' => now()->addYears($this->getBillingYears($data['billing_cycle'])),
                    ];
                }
            }

            return ['success' => false, 'error' => 'Registration failed'];
        } catch (\Exception $e) {
            Log::error('Domain registration failed', [
                'domain' => $data['domain'],
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Renew a domain
     */
    public function renewDomain(string $domain, int $years = 1): array
    {
        try {
            $response = Http::post($this->apiUrl, [
                'ApiUser' => $this->apiUser,
                'ApiKey' => $this->apiKey,
                'UserName' => $this->apiUser,
                'Command' => 'namecheap.domains.renew',
                'ClientIp' => request()->ip(),
                'DomainName' => $domain,
                'Years' => $years,
            ]);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());

                return [
                    'success' => true,
                    'order_id' => (string) $xml->CommandResponse->DomainRenewResult['OrderID'],
                    'expires_at' => (string) $xml->CommandResponse->DomainRenewResult['DomainDetails']['ExpiredDate'],
                ];
            }

            return ['success' => false, 'error' => 'Renewal failed'];
        } catch (\Exception $e) {
            Log::error('Domain renewal failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function getBillingYears(string $cycle): int
    {
        return match($cycle) {
            'annual' => 1,
            'biennial' => 2,
            'triennial' => 3,
            default => 1,
        };
    }

    protected function parseFirstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? 'Admin';
    }

    protected function parseLastName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));
        return count($parts) > 1 ? end($parts) : 'User';
    }
}
