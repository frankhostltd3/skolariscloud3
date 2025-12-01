<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntegrationTelemetryClient
{
    public function pullTelemetry(?string $region = null, ?string $integrationType = null): ?array
    {
        if (! config('integration-health.telemetry.enabled')) {
            return null;
        }

        $baseUrl = rtrim((string) config('integration-health.telemetry.base_url'), '/');

        if (empty($baseUrl)) {
            Log::warning('Integration telemetry base URL missing.');
            return null;
        }

        try {
            $response = Http::withToken(config('integration-health.telemetry.token'))
                ->timeout(config('integration-health.telemetry.timeout', 5))
                ->get($baseUrl . '/status', array_filter([
                    'region' => $region,
                    'integration_type' => $integrationType,
                ]));

            if (! $response->successful()) {
                Log::warning('Integration telemetry request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (\Throwable $throwable) {
            Log::warning('Integration telemetry request threw an exception', [
                'message' => $throwable->getMessage(),
            ]);

            return null;
        }
    }
}
