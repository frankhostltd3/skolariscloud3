<?php

namespace App\Jobs;

use App\Models\DomainOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ActivateDomainRoutingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        $order = DomainOrder::find($this->orderId);

        if (!$order) {
            return;
        }

        // Verify prerequisites
        if (!$order->dns_verified || $order->ssl_status !== 'active') {
            Log::warning('Domain routing activation skipped - prerequisites not met', [
                'order_id' => $order->id,
                'dns_verified' => $order->dns_verified,
                'ssl_status' => $order->ssl_status,
            ]);
            return;
        }

        // Activate routing
        $order->update([
            'routing_active' => true,
            'status' => 'active',
        ]);

        // Cache domain-to-school mapping for performance
        $cacheKey = 'domain:' . $order->full_domain;
        Cache::put($cacheKey, [
            'school_id' => $order->school_id,
            'database' => $order->school->database,
            'subdomain' => $order->school->subdomain,
        ], now()->addDays(7));

        Log::info('Domain routing activated', [
            'order_id' => $order->id,
            'domain' => $order->full_domain,
            'school_id' => $order->school_id,
        ]);
    }
}
