<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\PayrollSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessPayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes

    protected $periodStart;
    protected $periodEnd;
    protected $departmentId;

    /**
     * Create a new job instance.
     */
    public function __construct(?string $periodStart = null, ?string $periodEnd = null, ?int $departmentId = null)
    {
        $this->periodStart = $periodStart ?? Carbon::now()->startOfMonth()->toDateString();
        $this->periodEnd = $periodEnd ?? Carbon::now()->endOfMonth()->toDateString();
        $this->departmentId = $departmentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting automated payroll processing', [
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd,
                'department_id' => $this->departmentId
            ]);

            // Check if auto processing is enabled
            $autoProcess = PayrollSetting::getValue('auto_process_payroll');
            if (!$autoProcess || $autoProcess === 'false') {
                Log::info('Auto process payroll is disabled. Skipping.');
                return;
            }

            // Get employees to process
            $employeesQuery = Employee::where('status', 'active');
            
            if ($this->departmentId) {
                $employeesQuery->where('department_id', $this->departmentId);
            }
            
            $employees = $employeesQuery->get();

            foreach ($employees as $employee) {
                $this->processEmployeePayroll($employee);
            }

            // Trigger email sending if enabled
            if (PayrollSetting::getValue('email_pay_slips') === 'true') {
                SendPaySlipsJob::dispatch($this->periodStart, $this->periodEnd);
            }

            // Trigger accounting export if enabled
            if (PayrollSetting::getValue('export_to_accounting') === 'true') {
                ExportPayrollToAccountingJob::dispatch($this->periodStart, $this->periodEnd);
            }

            Log::info('Payroll processing completed successfully', [
                'employees_processed' => $employees->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Payroll processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process payroll for a single employee
     */
    protected function processEmployeePayroll(Employee $employee): void
    {
        // TODO: Implement actual payroll calculation logic
        // This is a placeholder that would be expanded based on your payroll model
        
        Log::info('Processing payroll for employee', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name
        ]);

        // Example structure - adapt to your actual Payroll model:
        /*
        $payroll = Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd,
            ],
            [
                'basic_salary' => $employee->salary,
                'allowances' => $this->calculateAllowances($employee),
                'deductions' => $this->calculateDeductions($employee),
                'net_salary' => $this->calculateNetSalary($employee),
                'status' => 'processed',
                'processed_at' => now(),
            ]
        );
        */
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessPayrollJob failed permanently', [
            'error' => $exception->getMessage(),
            'period' => $this->periodStart . ' to ' . $this->periodEnd
        ]);
    }
}
