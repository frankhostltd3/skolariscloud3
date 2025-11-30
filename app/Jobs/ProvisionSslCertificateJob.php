<?php

namespace App\Jobs;

use App\Models\DomainOrder;
use App\Services\SslCertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProvisionSslCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tries = 10;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(SslCertificateService $sslService): void
    {
        $order = DomainOrder::find($this->orderId);

        if (!$order || !$order->dns_verified) {
            Log::warning('SSL provisioning skipped - DNS not verified', [
                'order_id' => $this->orderId,
            ]);
            return;
        }

        if ($order->ssl_status === 'active') {
            return; // Already provisioned
        }

        $domain = $order->full_domain;
        $zoneId = $order->cloudflare_zone_id;

        if (!$zoneId) {
            Log::error('SSL provisioning failed - missing zone ID', [
                'order_id' => $order->id,
            ]);
            return;
        }

        // Request SSL certificate
        $result = $sslService->requestCertificate($zoneId, $domain);

        if ($result['success']) {
            $order->update([
                'ssl_enabled' => true,
                'ssl_status' => 'pending',
            ]);

            Log::info('SSL certificate provisioning initiated', [
                'order_id' => $order->id,
                'domain' => $domain,
            ]);

            // Check status after 2 minutes
            CheckSslStatusJob::dispatch($order->id)->delay(now()->addMinutes(2));
        } else {
            Log::error('SSL certificate request failed', [
                'order_id' => $order->id,
                'error' => $result['error'] ?? 'Unknown error',
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release(600); // Retry in 10 minutes
            }
        }
    }
}
