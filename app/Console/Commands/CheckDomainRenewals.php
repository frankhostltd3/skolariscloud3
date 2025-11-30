<?php

namespace App\Console\Commands;

use App\Models\DomainOrder;
use App\Services\DomainRegistrarService;
use App\Notifications\DomainRenewalNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDomainRenewals extends Command
{
    protected $signature = 'domains:check-renewals {--auto-renew : Automatically renew expiring domains}';
    protected $description = 'Check for expiring domains and send renewal notifications';

    protected DomainRegistrarService $registrarService;

    public function __construct(DomainRegistrarService $registrarService)
    {
        parent::__construct();
        $this->registrarService = $registrarService;
    }

    public function handle(): int
    {
        $this->info('Checking for expiring domains...');

        // Get domains expiring within 30 days
        $expiringDomains = DomainOrder::active()
            ->needsRenewal()
            ->where('billing_cycle', '!=', 'free')
            ->with(['school', 'creator'])
            ->get();

        if ($expiringDomains->isEmpty()) {
            $this->info('No domains require renewal at this time.');
            return self::SUCCESS;
        }

        $this->table(
            ['Domain', 'School', 'Expires', 'Days Until Expiry', 'Auto-Renew'],
            $expiringDomains->map(fn($order) => [
                $order->full_domain,
                $order->school->name,
                $order->expires_at?->format('Y-m-d'),
                $order->days_until_expiry,
                $order->auto_renew ? 'Yes' : 'No',
            ])
        );

        $renewedCount = 0;
        $notifiedCount = 0;

        foreach ($expiringDomains as $order) {
            // Auto-renew if enabled and flag is set
            if ($this->option('auto-renew') && $order->auto_renew) {
                if ($this->renewDomain($order)) {
                    $renewedCount++;
                    continue;
                }
            }

            // Send renewal notification
            if ($this->sendRenewalNotification($order)) {
                $notifiedCount++;
            }
        }

        if ($renewedCount > 0) {
            $this->info("✓ Renewed {$renewedCount} domain(s) automatically");
        }

        if ($notifiedCount > 0) {
            $this->info("✓ Sent renewal notifications for {$notifiedCount} domain(s)");
        }

        return self::SUCCESS;
    }

    /**
     * Renew a domain
     */
    protected function renewDomain(DomainOrder $order): bool
    {
        try {
            $this->line("Renewing: {$order->full_domain}...");

            $result = $this->registrarService->renewDomain(
                $order->full_domain,
                $this->registrarService->getBillingYears($order->billing_cycle)
            );

            if ($result['success']) {
                $order->update([
                    'expires_at' => $result['expires_at'],
                    'last_renewed_at' => now(),
                ]);

                $this->info("  ✓ Successfully renewed until {$result['expires_at']}");

                Log::info('Domain auto-renewed', [
                    'domain' => $order->full_domain,
                    'order_id' => $order->id,
                    'new_expiry' => $result['expires_at'],
                ]);

                return true;
            }

            $this->error("  ✗ Renewal failed: {$result['error']}");
            return false;

        } catch (\Exception $e) {
            $this->error("  ✗ Exception: {$e->getMessage()}");

            Log::error('Domain renewal failed', [
                'domain' => $order->full_domain,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send renewal notification
     */
    protected function sendRenewalNotification(DomainOrder $order): bool
    {
        try {
            // Notify school creator
            if ($order->creator) {
                $order->creator->notify(new DomainRenewalNotification($order));
            }

            // Notify school admins
            $admins = $order->school->users()
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['Super Admin', 'Admin']))
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new DomainRenewalNotification($order));
            }

            Log::info('Domain renewal notification sent', [
                'domain' => $order->full_domain,
                'order_id' => $order->id,
                'days_until_expiry' => $order->days_until_expiry,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send renewal notification', [
                'domain' => $order->full_domain,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
