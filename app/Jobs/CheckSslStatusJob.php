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

class CheckSslStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tries = 20; // Check for up to 1 hour (every 3 minutes)

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(SslCertificateService $sslService): void
    {
        $order = DomainOrder::find($this->orderId);

        if (!$order || $order->ssl_status === 'active') {
            return;
        }

        $zoneId = $order->cloudflare_zone_id;

        if (!$zoneId) {
            Log::error('SSL status check failed - missing zone ID', [
                'order_id' => $order->id,
            ]);
            return;
        }

        // Check certificate status
        $status = $sslService->checkCertificateStatus($zoneId);

        if ($status['active']) {
            $order->update([
                'ssl_status' => 'active',
                'ssl_issued_at' => $status['issued_at'],
                'ssl_expires_at' => $status['expires_at'],
            ]);

            // Enable HTTPS features
            $sslService->enableAlwaysHttps($zoneId);
            $sslService->enableAutomaticHttpsRewrites($zoneId);

            Log::info('SSL certificate activated', [
                'order_id' => $order->id,
                'domain' => $order->full_domain,
                'expires_at' => $status['expires_at'],
            ]);

            // Dispatch routing activation job
            ActivateDomainRoutingJob::dispatch($order->id)->delay(now()->addMinutes(1));

        } else {
            Log::info('SSL certificate not ready yet', [
                'order_id' => $order->id,
                'status' => $status['status'] ?? 'pending',
                'attempt' => $this->attempts(),
            ]);

            // Keep checking every 3 minutes
            if ($this->attempts() < $this->tries) {
                $this->release(180);
            } else {
                Log::error('SSL activation timeout after max attempts', [
                    'order_id' => $order->id,
                ]);

                $order->update(['ssl_status' => 'failed']);
            }
        }
    }
}
