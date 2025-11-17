<?php

namespace App\Services\LandlordBilling;

use App\Models\LandlordInvoice;
use App\Models\LandlordInvoiceItem;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceBuilder
{
    /**
     * Create and persist a landlord invoice using supplied payload.
     *
     * @param array<string,mixed> $payload
     */
    public function createFromPayload(array $payload): LandlordInvoice
    {
        return DB::transaction(function () use ($payload) {
            $tenant = null;
            $tenantId = Arr::get($payload, 'tenant_id');
            if ($tenantId) {
                $tenant = Tenant::query()->find($tenantId);
            }

            $issuedAt = Arr::get($payload, 'issued_at') ?? now()->toDateString();
            if ($issuedAt instanceof \DateTimeInterface) {
                $issuedAt = $issuedAt->format('Y-m-d');
            }

            $dueAt = Arr::get($payload, 'due_at');
            if ($dueAt instanceof \DateTimeInterface) {
                $dueAt = $dueAt->format('Y-m-d');
            }
            if (! $dueAt) {
                $dueAt = now()->addDays((int) config('skolaris.billing.auto_due_in_days', 14))->toDateString();
            }

            $periodStart = Arr::get($payload, 'period_start');
            if ($periodStart instanceof \DateTimeInterface) {
                $periodStart = $periodStart->format('Y-m-d');
            }

            $periodEnd = Arr::get($payload, 'period_end');
            if ($periodEnd instanceof \DateTimeInterface) {
                $periodEnd = $periodEnd->format('Y-m-d');
            }

            $invoice = LandlordInvoice::query()->create([
                'tenant_id' => $tenant?->getKey(),
                'tenant_name_snapshot' => $tenant?->name ?? Arr::get($payload, 'tenant_name_snapshot'),
                'tenant_plan_snapshot' => $tenant?->plan ?? Arr::get($payload, 'tenant_plan_snapshot'),
                'status' => Arr::get($payload, 'status', 'draft'),
                'auto_generated' => (bool) Arr::get($payload, 'auto_generated', false),
                'issued_at' => $issuedAt,
                'due_at' => $dueAt,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'subtotal' => Arr::get($payload, 'subtotal', 0),
                'tax_total' => Arr::get($payload, 'tax_total', 0),
                'discount_total' => Arr::get($payload, 'discount_total', 0),
                'total' => Arr::get($payload, 'total', 0),
                'balance_due' => Arr::get($payload, 'balance_due'),
                'notes' => Arr::get($payload, 'notes'),
                'metadata' => Arr::get($payload, 'metadata', []),
            ]);

            $items = Arr::get($payload, 'items', []);
            foreach ($items as $itemPayload) {
                $this->attachItem($invoice, $itemPayload);
            }

            $invoice->refreshFinancials();

            return $invoice;
        });
    }

    /**
     * Create a landlord invoice from an e-commerce order.
     *
     * @param array<string,mixed> $orderPayload
     */
    public function createFromOrder(array $orderPayload): LandlordInvoice
    {
        $orderReference = Arr::get($orderPayload, 'order_reference');
        if ($orderReference) {
            $existing = LandlordInvoice::query()
                ->where('metadata->order_reference', $orderReference)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $tenantId = Arr::get($orderPayload, 'tenant_id');
        $plan = Arr::get($orderPayload, 'plan');
        $periodStart = Arr::get($orderPayload, 'period_start', now()->startOfMonth());
        $periodEnd = Arr::get($orderPayload, 'period_end', now()->endOfMonth());
        $unitPrice = (float) Arr::get($orderPayload, 'amount', 0);

        $metadata = [
            'source' => Arr::get($orderPayload, 'source', 'storefront'),
            'order_reference' => Arr::get($orderPayload, 'order_reference'),
            'buyer_email' => Arr::get($orderPayload, 'buyer_email'),
            'buyer_name' => Arr::get($orderPayload, 'buyer_name'),
            'plan' => $plan,
        ];

        $metadata = array_merge($metadata, Arr::get($orderPayload, 'metadata', []));

        $payload = [
            'tenant_id' => $tenantId,
            'status' => 'pending',
            'auto_generated' => true,
            'issued_at' => now()->toDateString(),
            'due_at' => Arr::get($orderPayload, 'due_at', now()->addDays(config('skolaris.billing.auto_due_in_days', 14))->toDateString()),
            'period_start' => $periodStart instanceof \DateTimeInterface ? $periodStart->format('Y-m-d') : (string) $periodStart,
            'period_end' => $periodEnd instanceof \DateTimeInterface ? $periodEnd->format('Y-m-d') : (string) $periodEnd,
            'notes' => Arr::get($orderPayload, 'notes'),
            'metadata' => $metadata,
            'items' => [[
                'description' => Arr::get($orderPayload, 'line_description', __('Subscription order')),
                'category' => 'subscription',
                'quantity' => (int) Arr::get($orderPayload, 'quantity', 1),
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * ((int) Arr::get($orderPayload, 'quantity', 1) ?: 1),
            ]],
        ];

        return $this->createFromPayload($payload);
    }

    /**
     * @param array<string,mixed> $itemPayload
     */
    private function attachItem(LandlordInvoice $invoice, array $itemPayload): LandlordInvoiceItem
    {
        $quantity = max(1, (int) Arr::get($itemPayload, 'quantity', 1));
        $unitPrice = (float) Arr::get($itemPayload, 'unit_price', 0);
        $lineTotal = Arr::get($itemPayload, 'line_total');
        if ($lineTotal === null) {
            $lineTotal = $quantity * $unitPrice;
        }

        return $invoice->items()->create([
            'line_type' => Arr::get($itemPayload, 'line_type', 'service'),
            'description' => Str::of(Arr::get($itemPayload, 'description', ''))->limit(255),
            'category' => Arr::get($itemPayload, 'category'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
            'metadata' => Arr::get($itemPayload, 'metadata', []),
        ]);
    }
}
