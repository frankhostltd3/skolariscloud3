<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SslCertificateService
{
    protected string $apiToken;
    protected string $apiUrl = 'https://api.cloudflare.com/client/v4';

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.api_token') ?? '';
    }

    /**
     * Request SSL certificate via Cloudflare Universal SSL
     */
    public function requestCertificate(string $zoneId, string $domain): array
    {
        try {
            // Enable Universal SSL (automatically provisioned by Cloudflare)
            $response = Http::withToken($this->apiToken)
                ->patch("{$this->apiUrl}/zones/{$zoneId}/settings/ssl", [
                    'value' => 'flexible', // or 'full' for stricter security
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'cloudflare',
                    'status' => 'pending',
                    'message' => 'SSL certificate provisioning initiated',
                ];
            }

            return ['success' => false, 'error' => 'SSL enablement failed'];
        } catch (\Exception $e) {
            Log::error('SSL certificate request failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check SSL certificate status
     */
    public function checkCertificateStatus(string $zoneId): array
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->get("{$this->apiUrl}/zones/{$zoneId}/ssl/certificate_packs");

            if ($response->successful()) {
                $packs = $response->json()['result'] ?? [];

                if (empty($packs)) {
                    return [
                        'active' => false,
                        'status' => 'none',
                        'message' => 'No SSL certificates found',
                    ];
                }

                $activePack = collect($packs)->firstWhere('status', 'active');

                if ($activePack) {
                    return [
                        'active' => true,
                        'status' => 'active',
                        'provider' => 'cloudflare',
                        'expires_at' => $activePack['expires_on'] ?? null,
                        'issued_at' => $activePack['created_on'] ?? null,
                        'hosts' => $activePack['hosts'] ?? [],
                    ];
                }

                return [
                    'active' => false,
                    'status' => $packs[0]['status'] ?? 'pending',
                    'message' => 'Certificate is being provisioned',
                ];
            }

            return ['active' => false, 'error' => 'Status check failed'];
        } catch (\Exception $e) {
            Log::error('SSL status check failed', [
                'zone_id' => $zoneId,
                'error' => $e->getMessage(),
            ]);

            return ['active' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify SSL is working for domain
     */
    public function verifySsl(string $domain): bool
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $client = @stream_socket_client(
                "ssl://{$domain}:443",
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($client) {
                $params = stream_context_get_params($client);
                fclose($client);

                return isset($params['options']['ssl']['peer_certificate']);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('SSL verification failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get SSL certificate expiry date
     */
    public function getCertificateExpiry(string $domain): ?string
    {
        try {
            $get = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                ],
            ]);

            $read = @stream_socket_client(
                "ssl://{$domain}:443",
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $get
            );

            if ($read) {
                $cert = stream_context_get_params($read);
                $certInfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
                fclose($read);

                return isset($certInfo['validTo_time_t'])
                    ? date('Y-m-d H:i:s', $certInfo['validTo_time_t'])
                    : null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Enable Always Use HTTPS
     */
    public function enableAlwaysHttps(string $zoneId): bool
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->patch("{$this->apiUrl}/zones/{$zoneId}/settings/always_use_https", [
                    'value' => 'on',
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Always HTTPS enablement failed', [
                'zone_id' => $zoneId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Enable Automatic HTTPS Rewrites
     */
    public function enableAutomaticHttpsRewrites(string $zoneId): bool
    {
        try {
            $response = Http::withToken($this->apiToken)
                ->patch("{$this->apiUrl}/zones/{$zoneId}/settings/automatic_https_rewrites", [
                    'value' => 'on',
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Automatic HTTPS rewrites enablement failed', [
                'zone_id' => $zoneId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
