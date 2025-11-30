<?php

namespace App\Jobs;

use App\Models\DomainOrder;
use App\Services\DnsManagementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyDnsPropagationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tries = 12; // 12 attempts over 1 hour (every 5 minutes)

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(DnsManagementService $dnsService): void
    {
        $order = DomainOrder::find($this->orderId);

        if (!$order || $order->dns_verified) {
            return;
        }

        $domain = $order->full_domain;

        // Verify DNS records
        $verified = $dnsService->verifyRecords($domain, $order->verification_token);

        if ($verified) {
            $order->update(['dns_verified' => true]);

            Log::info('DNS verification successful', [
                'order_id' => $order->id,
                'domain' => $domain,
            ]);

            // Dispatch SSL provisioning job
            ProvisionSslCertificateJob::dispatch($order->id)->delay(now()->addMinutes(2));

            return;
        }

        // Check propagation status
        $propagation = $dnsService->checkPropagation($domain);

        if (!$propagation['complete']) {
            Log::info('DNS propagation incomplete, retrying...', [
                'order_id' => $order->id,
                'domain' => $domain,
                'attempt' => $this->attempts(),
            ]);

            // Retry in 5 minutes if not verified
            if ($this->attempts() < $this->tries) {
                $this->release(300); // 5 minutes
            } else {
                Log::error('DNS verification failed after max attempts', [
                    'order_id' => $order->id,
                    'domain' => $domain,
                ]);
            }
        }
    }
}
