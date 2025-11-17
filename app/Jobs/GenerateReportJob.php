<?php

namespace App\Jobs;

use App\Models\ReportLog;
use App\Models\Attendance;
use App\Models\Academic\Enrollment;
use App\Models\Finance\FeePayment;
use App\Models\Finance\FeeInvoice;
use App\Models\Expense;
use App\Models\Grade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Stancl\Tenancy\Tenancy;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 min

    public function __construct(
        public int $reportLogId,
        public string $type,
        public string $format,
        public string $from,
        public string $to,
        public ?string $tenantId = null,
    ) {}

    public function handle(): void
    {
        // Initialize tenancy if tenant id provided
        if ($this->tenantId) {
            /** @var Tenancy $tenancy */
            $tenancy = app(Tenancy::class);
            if (!tenant() || tenant('id') !== $this->tenantId) {
                $tenancy->initialize($this->tenantId);
            }
        }
        $log = ReportLog::find($this->reportLogId);
        if (!$log) { return; }
        $log->update(['status' => 'running', 'started_at' => now()]);

        try {
            $dateFrom = Carbon::parse($this->from);
            $dateTo = Carbon::parse($this->to);
            [$headers,$rows] = $this->buildDataset($this->type,$dateFrom,$dateTo);
            $rowCount = count($rows);

            $relativeDir = 'reports/' . tenant('id');
            $absDir = storage_path('app/' . $relativeDir);
            if (!is_dir($absDir)) { mkdir($absDir,0755,true); }
            $baseName = $this->type . '_' . now()->format('Ymd_His');
            $filePath = $relativeDir . '/' . $baseName . '.' . $this->format;

            switch ($this->format) {
                case 'json':
                    file_put_contents(storage_path('app/' . $filePath), json_encode(['headers'=>$headers,'data'=>$rows], JSON_PRETTY_PRINT));
                    break;
                case 'xlsx':
                    $export = new \App\Exports\ArrayReportExport($headers,$rows,$log->name);
                    Excel::store($export,$filePath,'local', ExcelWriter::XLSX);
                    break;
                default:
                    $fh = fopen(storage_path('app/' . $filePath),'w');
                    fputcsv($fh,$headers);
                    foreach ($rows as $r) { fputcsv($fh,$r); }
                    fclose($fh);
            }

            $log->markCompleted($rowCount, storage_path('app/' . $filePath));
        } catch (\Throwable $e) {
            $log->markFailed($e->getMessage());
            Log::error('Async report generation failed', ['id'=>$log->id,'error'=>$e->getMessage()]);
        }
    }

    private function buildDataset(string $type, Carbon $from, Carbon $to): array
    {
        return match($type) {
            'academic_performance' => $this->academicPerformance($from,$to),
            'attendance_summary' => $this->attendanceSummary($from,$to),
            'financial_summary' => $this->financialSummary($from,$to),
            'enrollment_summary' => $this->enrollmentSummary($from,$to),
            default => [['Message'],[['Unsupported type']]],
        };
    }

    private function academicPerformance(Carbon $from, Carbon $to): array
    {
        $grades = Grade::query()->published()
            ->whereBetween('assessment_date', [$from->toDateString(), $to->toDateString()])
            ->select('student_id', DB::raw('AVG(grade_point) as gpa'), DB::raw('COUNT(*) as assessments'))
            ->groupBy('student_id')->get();
        $headers = ['Student','GPA','Assessments'];
        $rows = $grades->map(function($g){ $u = User::find($g->student_id); return [$u?->name ?? ('Student #'.$g->student_id), round((float)$g->gpa,2), (int)$g->assessments]; })->all();
        return [$headers,$rows];
    }

    private function attendanceSummary(Carbon $from, Carbon $to): array
    {
        $attendance = Attendance::whereBetween('attendance_date', [$from,$to])
            ->select('attendance_date', DB::raw("SUM(status='present') as present"), DB::raw("SUM(status='late') as late"), DB::raw("SUM(status='absent') as absent"))
            ->groupBy('attendance_date')->orderBy('attendance_date')->get();
        $headers=['Date','Present','Late','Absent'];
        $rows = $attendance->map(fn($r)=>[$r->attendance_date,$r->present,$r->late,$r->absent])->all();
        return [$headers,$rows];
    }

    private function financialSummary(Carbon $from, Carbon $to): array
    {
        $payments = FeePayment::where('status','confirmed')->whereBetween(DB::raw('DATE(paid_at)'),[$from->toDateString(),$to->toDateString()])->sum('amount');
        $expenses = Expense::whereIn('status',['paid','approved'])->whereBetween('expense_date',[$from->toDateString(),$to->toDateString()])->sum('amount');
        $headers=['Metric','Amount'];
        $rows=[['Revenue',$payments],['Expenses',$expenses],['Net',$payments-$expenses]];return [$headers,$rows];
    }

    private function enrollmentSummary(Carbon $from, Carbon $to): array
    {
        $enroll = Enrollment::whereBetween('enrollment_date',[$from->toDateString(),$to->toDateString()])
            ->select(DB::raw('DATE(enrollment_date) as d'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('d')->orderBy('d')->get();
        $headers=['Date','New Enrollments'];
        $rows=$enroll->map(fn($e)=>[$e->d,$e->cnt])->all();return [$headers,$rows];
    }
}
