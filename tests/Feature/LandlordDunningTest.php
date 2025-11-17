<?php

namespace Tests\Feature;

use App\Console\Commands\ProcessLandlordInvoices;
use App\Models\LandlordDunningPolicy;
use App\Models\LandlordInvoice;
use App\Models\LandlordInvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LandlordDunningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Central connection is default in this test context
        // Ensure policy exists
        LandlordDunningPolicy::current();
    }

    public function test_reminder_window_sends_once(): void
    {
        $invoice = LandlordInvoice::create([
            'tenant_id' => 't1',
            'tenant_name_snapshot' => 'Tenant One',
            'status' => 'sent',
            'issued_at' => now()->subDays(10)->toDateString(),
            'due_at' => now()->toDateString(),
            'total' => 100,
            'balance_due' => 100,
        ]);

        $policy = LandlordDunningPolicy::current();
        $policy->reminder_windows = [0];
        $policy->save();

        // First run should mark warning and record reminder offset 0
        Artisan::call('billing:process-landlord-invoices');
        $invoice->refresh();
        $this->assertEquals('warning', $invoice->status);
        $this->assertContains(0, $invoice->metadata['reminders_sent'] ?? []);

        // Second run same day should not duplicate
        Artisan::call('billing:process-landlord-invoices');
        $invoice->refresh();
        $sent = $invoice->metadata['reminders_sent'] ?? [];
        $this->assertEquals(1, count(array_filter($sent, fn ($v) => $v === 0)));
    }

    public function test_late_fee_applied_once(): void
    {
        $invoice = LandlordInvoice::create([
            'tenant_id' => 't1',
            'tenant_name_snapshot' => 'Tenant One',
            'status' => 'sent',
            'issued_at' => now()->subDays(40)->toDateString(),
            'due_at' => now()->subDays(1)->toDateString(),
            'total' => 100,
            'balance_due' => 100,
        ]);

        $policy = LandlordDunningPolicy::current();
        $policy->late_fee_percent = 10; // 10%
        $policy->late_fee_flat = 5;     // +5
        $policy->save();

        Artisan::call('billing:process-landlord-invoices');
        $invoice->refresh();

        $items = LandlordInvoiceItem::query()->where('landlord_invoice_id', $invoice->getKey())->get();
        $this->assertTrue($items->contains(fn ($item) => $item->category === 'late_fee'));
        $this->assertTrue(($invoice->metadata['late_fee_applied'] ?? false) === true);

        $appliedCount = LandlordInvoiceItem::query()->where('landlord_invoice_id', $invoice->getKey())->where('category', 'late_fee')->count();
        $this->assertEquals(1, $appliedCount, 'Late fee should be applied exactly once');

        // Re-run shouldn't add a second fee
        Artisan::call('billing:process-landlord-invoices');
        $this->assertEquals(1, LandlordInvoiceItem::query()->where('landlord_invoice_id', $invoice->getKey())->where('category', 'late_fee')->count());
    }
}
