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
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportPayrollToAccountingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 300;

    protected $periodStart;
    protected $periodEnd;
    protected $format;

    /**
     * Create a new job instance.
     */
    public function __construct(string $periodStart, string $periodEnd, string $format = 'csv')
    {
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting payroll export to accounting', [
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd,
                'format' => $this->format
            ]);

            // Get all processed payroll records for the period
            $employees = Employee::where('status', 'active')->get();
            
            $exportData = $this->prepareExportData($employees);
            
            // Generate export file based on format
            $fileName = $this->generateExportFile($exportData);

            Log::info('Payroll export completed successfully', [
                'file' => $fileName,
                'records' => count($exportData)
            ]);

        } catch (\Exception $e) {
            Log::error('Payroll export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Prepare data for export
     */
    protected function prepareExportData($employees): array
    {
        $data = [];
        $currencyCode = PayrollSetting::getValue('default_currency') ?? 'USD';

        foreach ($employees as $employee) {
            // TODO: Fetch actual payroll record from Payroll model
            // This is placeholder structure following standard accounting format
            
            $data[] = [
                'employee_id' => $employee->employee_number ?? $employee->id,
                'employee_name' => $employee->full_name,
                'department' => $employee->department?->name ?? 'N/A',
                'position' => $employee->position?->name ?? 'N/A',
                'period_start' => $this->periodStart,
                'period_end' => $this->periodEnd,
                'currency' => $currencyCode,
                'basic_salary' => number_format($employee->salary ?? 0, 2, '.', ''),
                'housing_allowance' => '0.00',
                'transport_allowance' => '0.00',
                'medical_allowance' => '0.00',
                'other_allowances' => '0.00',
                'gross_salary' => number_format($employee->salary ?? 0, 2, '.', ''),
                'income_tax' => '0.00',
                'pension_contribution' => '0.00',
                'insurance' => '0.00',
                'other_deductions' => '0.00',
                'total_deductions' => '0.00',
                'net_salary' => number_format($employee->salary ?? 0, 2, '.', ''),
                'bank_account' => $employee->bank_account_number ?? '',
                'payment_method' => 'Bank Transfer',
                'status' => 'Processed',
                'processed_date' => now()->toDateString(),
            ];
        }

        return $data;
    }

    /**
     * Generate export file
     */
    protected function generateExportFile(array $data): string
    {
        $periodMonth = Carbon::parse($this->periodStart)->format('Y-m');
        $timestamp = now()->format('YmdHis');
        $fileName = "payroll_export_{$periodMonth}_{$timestamp}.{$this->format}";

        switch ($this->format) {
            case 'json':
                $content = json_encode($data, JSON_PRETTY_PRINT);
                break;
            
            case 'csv':
            default:
                $content = $this->generateCsv($data);
                break;
        }

        // Store in tenant-specific exports folder
        Storage::disk('local')->put("exports/payroll/{$fileName}", $content);

        return $fileName;
    }

    /**
     * Generate CSV content
     */
    protected function generateCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $csv = '';
        
        // Headers
        $headers = array_keys($data[0]);
        $csv .= implode(',', array_map(function($header) {
            return '"' . str_replace('"', '""', ucwords(str_replace('_', ' ', $header))) . '"';
        }, $headers)) . "\n";

        // Data rows
        foreach ($data as $row) {
            $csv .= implode(',', array_map(function($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row)) . "\n";
        }

        return $csv;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ExportPayrollToAccountingJob failed permanently', [
            'error' => $exception->getMessage(),
            'period' => $this->periodStart . ' to ' . $this->periodEnd
        ]);
    }
}
