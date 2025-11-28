<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\LandlordBilling\InvoiceBuilder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;

class OrderObserver
{
    public function __construct(private InvoiceBuilder $invoiceBuilder)
    {
    }

    public function created(Order $order): void
    {
        if (! App::bound(TenantContract::class)) {
            return;
        }

        /** @var \Stancl\Tenancy\Contracts\Tenant|null $tenant */
        $tenant = App::make(TenantContract::class);
        if (! $tenant) {
            return;
        }

        $quantity = (int) ($order->quantity ?? 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $metadata = array_filter([
            'item_type' => $order->item_type,
            'item_id' => $order->item_id,
            'payment_method' => $order->payment_method,
            'order_status' => $order->status,
        ], static fn ($value) => $value !== null && $value !== '');

        $payload = [
            'tenant_id' => $tenant->getTenantKey(),
            'tenant_name_snapshot' => $tenant->name ?? null,
            'plan' => $tenant->plan ?? null,
            'order_reference' => $order->getKey(),
            'buyer_email' => $order->buyer_email,
            'buyer_name' => $order->buyer_name,
            'amount' => (float) ($order->price ?? 0),
            'quantity' => $quantity,
            'line_description' => $order->item_title,
            'metadata' => $metadata,
            'source' => 'orders',
            'period_start' => Date::now()->startOfMonth(),
            'period_end' => Date::now()->endOfMonth(),
        ];

        $this->invoiceBuilder->createFromOrder($payload);
    }
}
