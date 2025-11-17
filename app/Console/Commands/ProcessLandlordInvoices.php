<?php

namespace App\Console\Commands;

use App\Models\LandlordInvoice;
use App\Models\LandlordInvoiceItem;
use App\Models\LandlordDunningPolicy;
use App\Notifications\LandlordInvoiceSuspended;
use App\Notifications\LandlordInvoiceTerminated;
use App\Notifications\LandlordInvoiceWarning;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessLandlordInvoices extends Command
{
    protected $signature = 'billing:process-landlord-invoices {--dry-run : Only log the actions without persisting changes}';

    protected $description = 'Send reminders and enforce suspensions/terminations for overdue landlord invoices using dunning policy.';

    public function handle(): int
    {
    $now = now();
    $policy = LandlordDunningPolicy::current();
    $warningThreshold = (int) ($policy->warning_threshold_days ?? 5);
    $suspensionGrace = (int) ($policy->suspension_grace_days ?? 7);
    $terminationGrace = (int) ($policy->termination_grace_days ?? 30);
    $reminderWindows = (array) ($policy->reminder_windows ?? []);
        $dryRun = (bool) $this->option('dry-run');

        $invoices = LandlordInvoice::query()
            ->whereNull('paid_at')
            ->whereNull('cancelled_at')
            ->whereNotIn('status', ['terminated'])
            ->get();

        $this->info(sprintf('Processing %d landlord invoice(s)', $invoices->count()));

        foreach ($invoices as $invoice) {
            if (($invoice->metadata['skip_enforcement'] ?? false) === true) {
                $this->line("Skipping invoice {$invoice->invoice_number} due to enforcement override.");
                continue;
            }

            $dueAt = $invoice->due_at ?? $invoice->issued_at ?? $invoice->created_at;
            if (! $dueAt) {
                continue;
            }

            $diff = $now->copy()->startOfDay()->diffInDays($dueAt->copy()->startOfDay(), false);
            $daysUntilDue = $diff;
            $daysOverdue = $diff < 0 ? abs($diff) : 0;

            $warningCooldownPassed = $invoice->last_warning_sent_at === null
                || $invoice->last_warning_sent_at->diffInHours($now) >= 24;

            // Reminder windows: send on exact day offsets relative to due date, once per offset
            $metadata = $invoice->metadata ?? [];
            $remindersSent = (array) ($metadata['reminders_sent'] ?? []);
            if (in_array($daysUntilDue, $reminderWindows, true) && ! in_array($daysUntilDue, $remindersSent, true) && $warningCooldownPassed && ! in_array($invoice->status, ['suspended', 'terminated'], true)) {
                $this->warn("Reminder window hit for invoice {$invoice->invoice_number} at offset {$daysUntilDue} days.");
                if (! $dryRun) {
                    $invoice->markWarning();
                    $this->dispatchInvoiceNotification($invoice, 'warning', new LandlordInvoiceWarning($invoice));
                    // record this offset as sent
                    $metadata['reminders_sent'] = array_values(array_unique([...$remindersSent, $daysUntilDue]));
                    $invoice->metadata = $metadata;
                    $invoice->save();
                }
            } elseif ($daysUntilDue <= $warningThreshold && $daysUntilDue >= 0 && $warningCooldownPassed && ! in_array($invoice->status, ['warning', 'suspended', 'terminated'], true)) {
                // Fallback early warning threshold if not covered by windows
                $this->warn("Invoice {$invoice->invoice_number} is approaching due date ({$dueAt->toDateString()}).");
                if (! $dryRun) {
                    $invoice->markWarning();
                    $this->dispatchInvoiceNotification($invoice, 'warning', new LandlordInvoiceWarning($invoice));
                }
            }

            // Apply a one-time late fee when invoice becomes overdue
            if ($daysOverdue > 0 && ! in_array($invoice->status, ['terminated'], true)) {
                $latePercent = $policy->late_fee_percent;
                $lateFlat = $policy->late_fee_flat;
                $lateAlreadyApplied = (bool) (($invoice->metadata['late_fee_applied'] ?? false) === true);

                if (($latePercent || $lateFlat) && ! $lateAlreadyApplied && ! $dryRun) {
                    $base = (float) ($invoice->balance_due ?? $invoice->total ?? 0);
                    $amount = 0.0;
                    if ($latePercent) {
                        $amount += ($base * ((float) $latePercent) / 100.0);
                    }
                    if ($lateFlat) {
                        $amount += (float) $lateFlat;
                    }
                    $amount = round($amount, 2);
                    if ($amount > 0) {
                        LandlordInvoiceItem::create([
                            'landlord_invoice_id' => $invoice->getKey(),
                            'line_type' => 'fee',
                            'description' => __('Late payment fee'),
                            'category' => 'late_fee',
                            'quantity' => 1,
                            'unit_price' => $amount,
                            'line_total' => $amount,
                            'metadata' => [
                                'source' => 'policy',
                                'applied_at' => now()->toIso8601String(),
                            ],
                        ]);
                        $metadata = $invoice->metadata ?? [];
                        $metadata['late_fee_applied'] = true;
                        $invoice->metadata = $metadata;
                        $invoice->refreshFinancials();
                        $this->line("Applied late fee of {$amount} to invoice {$invoice->invoice_number}.");
                    }
                }
            }

            if ($daysOverdue >= $suspensionGrace && ! in_array($invoice->status, ['suspended', 'terminated'], true)) {
                $this->error("Invoice {$invoice->invoice_number} is overdue by {$daysOverdue} day(s). Suspending tenant.");
                if (! $dryRun) {
                    $invoice->markSuspended();
                    $this->dispatchInvoiceNotification($invoice, 'suspension', new LandlordInvoiceSuspended($invoice));
                }
            }

            if ($daysOverdue >= ($suspensionGrace + $terminationGrace) && $invoice->status !== 'terminated') {
                $this->error("Invoice {$invoice->invoice_number} is overdue by {$daysOverdue} day(s). Terminating tenant access.");
                if (! $dryRun) {
                    $invoice->markTerminated();
                    $this->dispatchInvoiceNotification($invoice, 'termination', new LandlordInvoiceTerminated($invoice));
                }
            }
        }

        return self::SUCCESS;
    }

    protected function dispatchInvoiceNotification(LandlordInvoice $invoice, string $action, BaseNotification $notification): void
    {
        Log::notice('Landlord invoice notification dispatched', [
            'invoice' => $invoice->invoice_number,
            'tenant_id' => $invoice->tenant_id,
            'action' => $action,
            'due_at' => optional($invoice->due_at)->toDateString(),
            'total' => $invoice->total,
        ]);

        $policy = LandlordDunningPolicy::current();
        $channels = (array) ($policy?->{$action . '_channels'} ?? []);

        // MAIL
        if (in_array('mail', $channels, true)) {
            $emails = array_values(array_unique(array_filter(array_merge(
                $this->resolveNotificationRecipients($invoice),
                (array) ($policy?->{$action . '_recipients'} ?? []),
                (array) config("skolaris.billing.{$action}_recipients", [])
            ))));
            foreach ($emails as $email) {
                Notification::route('mail', $email)->notify($notification);
            }
        }

        // SMS (phone recipients)
        if (in_array('sms', $channels, true)) {
            $phones = array_values(array_unique(array_filter(array_merge(
                (array) ($policy?->{$action . '_phones'} ?? []),
                (array) config("skolaris.billing.{$action}_phone_recipients", [])
            ))));
            foreach ($phones as $phone) {
                Notification::route('sms', $phone)->notify($notification);
            }
        }

        // Slack (webhook URLs)
        if (in_array('slack', $channels, true)) {
            $slackWebhooks = array_values(array_unique(array_filter(array_merge(
                (array) ($policy?->{$action . '_slack_webhooks'} ?? []),
                (array) config("skolaris.billing.{$action}_slack_webhooks", [])
            ))));
            foreach ($slackWebhooks as $url) {
                Notification::route('slack', $url)->notify($notification);
            }
        }

        // Webhook (generic JSON POST)
        if (in_array('webhook', $channels, true)) {
            $webhooks = array_values(array_unique(array_filter(array_merge(
                (array) ($policy?->{$action . '_webhooks'} ?? []),
                (array) config("skolaris.billing.{$action}_webhooks", [])
            ))));
            foreach ($webhooks as $url) {
                Notification::route('webhook', $url)->notify($notification);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    protected function resolveNotificationRecipients(LandlordInvoice $invoice): array
    {
        $emails = [];
        $tenant = $invoice->tenant;

        if ($tenant) {
            $candidateKeys = [
                'billing_contact_email',
                'billing_primary_email',
                'owner_email',
                'email',
            ];

            foreach ($candidateKeys as $key) {
                $value = $tenant->getAttribute($key);
                if (is_string($value) && $this->isValidEmail($value)) {
                    $emails[] = $value;
                }
            }

            $additional = $tenant->getAttribute('billing_contact_emails');
            if (is_array($additional)) {
                foreach ($additional as $address) {
                    if (is_string($address) && $this->isValidEmail($address)) {
                        $emails[] = $address;
                    }
                }
            }
        }

        $groups = ['warning', 'suspension', 'termination'];
        foreach ($groups as $group) {
            $single = config("skolaris.billing.{$group}_recipient");
            if (is_string($single)) {
                $emails = array_merge($emails, $this->splitConfiguredEmails($single));
            }

            $multiple = config("skolaris.billing.{$group}_recipients", []);
            if (is_string($multiple)) {
                $emails = array_merge($emails, $this->splitConfiguredEmails($multiple));
            } elseif (is_array($multiple)) {
                foreach ($multiple as $address) {
                    if (is_string($address) && $this->isValidEmail($address)) {
                        $emails[] = $address;
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($emails, fn ($email) => $this->isValidEmail($email))));
    }

    protected function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @return array<int, string>
     */
    protected function splitConfiguredEmails(string $value): array
    {
        return array_filter(array_map('trim', explode(',', $value)), fn ($email) => $this->isValidEmail($email));
    }
}
