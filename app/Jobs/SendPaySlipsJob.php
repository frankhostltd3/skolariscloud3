<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Mail\PaySlipEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaySlipsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 600; // 10 minutes

    protected $periodStart;
    protected $periodEnd;

    /**
     * Create a new job instance.
     */
    public function __construct(string $periodStart, string $periodEnd)
    {
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting pay slip email distribution', [
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd
            ]);

            // Get employees with valid email addresses
            $employees = Employee::where('status', 'active')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            $sentCount = 0;
            $failedCount = 0;

            foreach ($employees as $employee) {
                try {
                    // Check if employee has user account with email
                    $emailAddress = $employee->email ?? $employee->user?->email;

                    if (!$emailAddress) {
                        Log::warning('Employee has no valid email address', [
                            'employee_id' => $employee->id
                        ]);
                        continue;
                    }

                    Mail::to($emailAddress)->send(
                        new PaySlipEmail($employee, $this->periodStart, $this->periodEnd)
                    );

                    $sentCount++;

                    Log::info('Pay slip sent successfully', [
                        'employee_id' => $employee->id,
                        'email' => $emailAddress
                    ]);

                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to send pay slip', [
                        'employee_id' => $employee->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Pay slip distribution completed', [
                'total_employees' => $employees->count(),
                'sent' => $sentCount,
                'failed' => $failedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Pay slip job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendPaySlipsJob failed permanently', [
            'error' => $exception->getMessage(),
            'period' => $this->periodStart . ' to ' . $this->periodEnd
        ]);
    }
}
