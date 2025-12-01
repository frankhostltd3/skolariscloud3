<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DnsManagementService
{
    protected string $apiToken;
    protected string $apiUrl = 'https://api.cloudflare.com/client/v4';

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.api_token') ?? '';
    }

    /**
     * Create DNS zone for domain
     */
    public function createZone(string $domain, string $accountId): array
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->post("{$this->apiUrl}/zones", [
                    'account' => ['id' => $accountId],
                    'name' => $domain,
                    'type' => 'full',
                    'jump_start' => true,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'zone_id' => $data['result']['id'],
                    'name_servers' => $data['result']['name_servers'],
                    'status' => $data['result']['status'],
                ];
            }

            return ['success' => false, 'error' => $response->json()['errors'][0]['message'] ?? 'Zone creation failed'];
        } catch (\Exception $e) {
            Log::error('DNS zone creation failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate DNS records for tenant routing
     */
    public function generateTenantRecords(string $domain, string $subdomain): array
    {
        $verificationToken = \Illuminate\Support\Str::random(32);

        return [
            [
                'type' => 'CNAME',
                'name' => '@',
                'content' => 'schools.skolariscloud.com',
                'ttl' => 300,
                'proxied' => true,
            ],
            [
                'type' => 'CNAME',
                'name' => 'www',
                'content' => 'schools.skolariscloud.com',
                'ttl' => 300,
                'proxied' => true,
            ],
            [
                'type' => 'TXT',
                'name' => '@',
                'content' => "tenant-verification={$verificationToken}",
                'ttl' => 3600,
            ],
            [
                'type' => 'TXT',
                'name' => '_tenant',
                'content' => "subdomain={$subdomain}",
                'ttl' => 3600,
            ],
        ];
    }

    /**
     * Create DNS records in Cloudflare
     */
    public function createRecords(string $zoneId, array $records): array
    {
        $results = [];

        foreach ($records as $record) {
            try {
                $response = Http::withToken($this->apiToken)
                    ->post("{$this->apiUrl}/zones/{$zoneId}/dns_records", $record);

                if ($response->successful()) {
                    $results[] = [
                        'success' => true,
                        'record_id' => $response->json()['result']['id'],
                        'type' => $record['type'],
                        'name' => $record['name'],
                    ];
                } else {
                    $results[] = [
                        'success' => false,
                        'error' => $response->json()['errors'][0]['message'] ?? 'Record creation failed',
                        'record' => $record,
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'record' => $record,
                ];
            }
        }

        return $results;
    }

    /**
     * Verify DNS records are correctly configured
     */
    public function verifyRecords(string $domain, string $expectedToken): bool
    {
        try {
            // Check TXT record for verification token
            $records = dns_get_record($domain, DNS_TXT);

            foreach ($records as $record) {
                if (isset($record['txt']) && str_contains($record['txt'], $expectedToken)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('DNS verification failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if DNS has propagated
     */
    public function checkPropagation(string $domain): array
    {
        try {
            $results = [
                'a_record' => false,
                'cname_record' => false,
                'txt_record' => false,
                'propagated' => false,
            ];

            // Check A/CNAME records
            $aRecords = @dns_get_record($domain, DNS_A);
            $cnameRecords = @dns_get_record($domain, DNS_CNAME);

            $results['a_record'] = !empty($aRecords);
            $results['cname_record'] = !empty($cnameRecords);

            // Check TXT record
            $txtRecords = @dns_get_record($domain, DNS_TXT);
            $results['txt_record'] = !empty($txtRecords);

            // Consider propagated if at least CNAME or A record exists
            $results['propagated'] = $results['a_record'] || $results['cname_record'];

            return $results;
        } catch (\Exception $e) {
            Log::error('DNS propagation check failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return ['propagated' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get nameservers for a domain
     */
    public function getNameservers(string $domain): array
    {
        try {
            $records = dns_get_record($domain, DNS_NS);
            return array_column($records, 'target');
        } catch (\Exception $e) {
            return [];
        }
    }
}
