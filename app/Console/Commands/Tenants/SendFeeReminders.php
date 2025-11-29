<?php

namespace App\Console\Commands\Tenants;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\FeeReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Traits\TenantAwareCommand;

class SendFeeReminders extends Command
{
    use TenantAwareCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:send-fee-reminders {--days=3 : Days before due date to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send fee reminders for invoices due soon or overdue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->processTenants(function ($school) {
            $this->info("Processing school: {$school->name}");

            $days = $this->option('days');
            $dueDate = now()->addDays($days)->startOfDay();

            // Find invoices due in X days
            $dueSoonInvoices = Invoice::whereDate('due_date', $dueDate)
                ->where('status', '!=', 'paid')
                ->with('student.parents')
                ->get();

            $this->info("Found " . $dueSoonInvoices->count() . " invoices due in {$days} days.");

            foreach ($dueSoonInvoices as $invoice) {
                $this->sendReminder($invoice, 'due_soon');
            }

            // Find overdue invoices (e.g. 1 day overdue, 7 days overdue)
            // For simplicity, let's just check for 1 day overdue for now
            $overdueDate = now()->subDays(1)->startOfDay();
            $overdueInvoices = Invoice::whereDate('due_date', $overdueDate)
                ->where('status', '!=', 'paid')
                ->with('student.parents')
                ->get();

            $this->info("Found " . $overdueInvoices->count() . " overdue invoices.");

            foreach ($overdueInvoices as $invoice) {
                $this->sendReminder($invoice, 'overdue');
            }
        });
    }

    protected function sendReminder(Invoice $invoice, $type)
    {
        $student = $invoice->student;
        if (!$student) return;

        // Notify student
        // $student->notify(new FeeReminderNotification($invoice, $type));

        // Notify parents
        if ($student->parents && $student->parents->count() > 0) {
            Notification::send($student->parents, new FeeReminderNotification($invoice, $type));
            $this->info("Sent {$type} reminder for Invoice #{$invoice->invoice_number} to parents.");
        } else {
            $this->warn("No parents found for student {$student->name} (Invoice #{$invoice->invoice_number})");
        }
    }
}
