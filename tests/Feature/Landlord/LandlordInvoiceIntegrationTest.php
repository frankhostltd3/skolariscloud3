<?php

namespace Tests\Feature\Landlord;

use App\Models\LandlordInvoice;
use App\Models\Order;
use App\Models\Tenant;
use App\Notifications\LandlordInvoiceSuspended;
use App\Notifications\LandlordInvoiceTerminated;
use App\Notifications\LandlordInvoiceWarning;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Tests\CentralTestCase;

class LandlordInvoiceIntegrationTest extends CentralTestCase
{

    protected function tearDown(): void
    {
        App::forgetInstance(TenantContract::class);
        Date::setTestNow();

        parent::tearDown();
    }

    public function test_order_creation_triggers_landlord_invoice_auto_generation(): void
    {
        $tenant = Tenant::withoutEvents(function () {
            return Tenant::create([
                'id' => 'tenant-observer-1',
                'name' => 'Observer Academy',
                'plan' => 'Growth',
            ]);
        });

        App::instance(TenantContract::class, $tenant);

        Date::setTestNow('2025-10-01 09:00:00');

        $order = new Order([
            'item_type' => 'book',
            'item_id' => 42,
            'item_title' => 'Mathematics 101',
            'price' => 199.99,
            'buyer_name' => 'Alex Teacher',
            'buyer_email' => 'alex@example.com',
            'status' => 'pending',
            'payment_method' => 'stripe',
        ]);

        $order->setAttribute('id', 501);
        $order->exists = true;

        /** @var OrderObserver $observer */
        $observer = App::make(OrderObserver::class);

        $observer->created($order);

        $this->assertEquals(1, LandlordInvoice::count());

        $invoice = LandlordInvoice::firstOrFail();

        $this->assertSame('tenant-observer-1', $invoice->tenant_id);
        $this->assertTrue($invoice->auto_generated);
        $this->assertSame('pending', $invoice->status);
        $this->assertSame(199.99, (float) $invoice->total);
        $this->assertSame(501, $invoice->metadata['order_reference']);
        $this->assertSame('Observer Academy', $invoice->tenant_name_snapshot);

        // Ensure duplicate observer calls do not create additional invoices.
        $observer->created($order);

        $this->assertEquals(1, LandlordInvoice::count());
    }

    public function test_billing_command_updates_warning_suspension_and_termination_states(): void
    {
        Date::setTestNow('2025-10-01 12:00:00');

        Notification::fake();

        $tenant = Tenant::withoutEvents(function () {
            return Tenant::create([
                'id' => 'tenant-warning-notify',
                'name' => 'Warning Campus',
                'billing_contact_email' => 'billing@warningcampus.test',
            ]);
        });

        config([
            'skolaris.billing.warning_channels' => ['mail'],
            'skolaris.billing.warning_recipient' => 'finance-fallback@skolariscloud.test',
            'skolaris.billing.warning_recipients' => [],
            'skolaris.billing.suspension_channels' => ['mail'],
            'skolaris.billing.suspension_recipient' => null,
            'skolaris.billing.suspension_recipients' => [],
            'skolaris.billing.termination_channels' => ['mail'],
            'skolaris.billing.termination_recipient' => null,
            'skolaris.billing.termination_recipients' => [],
        ]);

        $warningInvoice = LandlordInvoice::factory()->create([
            'status' => 'pending',
            'auto_generated' => true,
            'due_at' => Date::now()->addDays(3),
            'issued_at' => Date::now()->subDays(10),
            'last_warning_sent_at' => null,
            'warning_level' => 0,
            'tenant_id' => $tenant->id,
            'tenant_name_snapshot' => 'Warning Campus',
        ]);

        $suspendedInvoice = LandlordInvoice::factory()->create([
            'status' => 'pending',
            'due_at' => Date::now()->subDays(8),
            'issued_at' => Date::now()->subDays(40),
            'last_warning_sent_at' => null,
            'warning_level' => 0,
            'tenant_id' => $tenant->id,
            'tenant_name_snapshot' => 'Warning Campus',
        ]);

        $terminatedInvoice = LandlordInvoice::factory()->create([
            'status' => 'pending',
            'due_at' => Date::now()->subDays(45),
            'issued_at' => Date::now()->subDays(90),
            'last_warning_sent_at' => null,
            'warning_level' => 0,
            'tenant_id' => $tenant->id,
            'tenant_name_snapshot' => 'Warning Campus',
        ]);

        Artisan::call('billing:process-landlord-invoices');

        $this->assertSame('warning', $warningInvoice->fresh()->status);
        $this->assertEquals(1, $warningInvoice->fresh()->warning_level);
        $this->assertNotNull($warningInvoice->fresh()->last_warning_sent_at);

        Notification::assertSentOnDemand(LandlordInvoiceWarning::class, function (LandlordInvoiceWarning $notification, array $channels, $notifiable) use ($warningInvoice) {
            $mailRoutes = $notifiable->routes['mail'] ?? [];
            $mailRoutes = is_array($mailRoutes) ? $mailRoutes : [$mailRoutes];

            return in_array('mail', $channels, true)
                && in_array('billing@warningcampus.test', $mailRoutes, true)
                && $notification->invoice->is($warningInvoice->fresh());
        });

        $this->assertSame('suspended', $suspendedInvoice->fresh()->status);
        $this->assertNotNull($suspendedInvoice->fresh()->suspension_at);
        Notification::assertSentOnDemand(LandlordInvoiceSuspended::class, function (LandlordInvoiceSuspended $notification, array $channels, $notifiable) use ($suspendedInvoice) {
            $mailRoutes = $notifiable->routes['mail'] ?? [];
            $mailRoutes = is_array($mailRoutes) ? $mailRoutes : [$mailRoutes];

            return in_array('mail', $channels, true)
                && in_array('billing@warningcampus.test', $mailRoutes, true)
                && $notification->invoice->is($suspendedInvoice->fresh());
        });

        $this->assertSame('terminated', $terminatedInvoice->fresh()->status);
        $this->assertNotNull($terminatedInvoice->fresh()->termination_at);
        Notification::assertSentOnDemand(LandlordInvoiceTerminated::class, function (LandlordInvoiceTerminated $notification, array $channels, $notifiable) use ($terminatedInvoice) {
            $mailRoutes = $notifiable->routes['mail'] ?? [];
            $mailRoutes = is_array($mailRoutes) ? $mailRoutes : [$mailRoutes];

            return in_array('mail', $channels, true)
                && in_array('billing@warningcampus.test', $mailRoutes, true)
                && $notification->invoice->is($terminatedInvoice->fresh());
        });
    }
}
