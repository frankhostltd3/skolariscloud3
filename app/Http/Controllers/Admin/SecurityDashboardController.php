<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use App\Models\LoginAttempt;
use App\Models\AccountLockout;
use App\Models\IpBlock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SecurityDashboardController extends Controller
{
    /**
     * Display the security dashboard
     */
    public function index(Request $request)
    {
        $period = $request->input('period', '7'); // Default 7 days
        $startDate = now()->subDays((int)$period);

        // Get security metrics
        $metrics = $this->getSecurityMetrics($startDate);

        // Get recent critical events
        $criticalEvents = SecurityAuditLog::query()
            ->where('severity', SecurityAuditLog::SEVERITY_CRITICAL)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top failed IPs
        $topFailedIps = SecurityAuditLog::query()
            ->where('event_type', SecurityAuditLog::EVENT_LOGIN_FAILED)
            ->where('created_at', '>=', $startDate)
            ->select('ip_address')
            ->selectRaw('COUNT(*) as attempts')
            ->groupBy('ip_address')
            ->orderByDesc('attempts')
            ->limit(10)
            ->get();

        // Get login success vs failure chart data
        $loginChartData = $this->getLoginChartData($startDate);

        // Get event distribution
        $eventDistribution = SecurityAuditLog::query()
            ->where('created_at', '>=', $startDate)
            ->select('event_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Active blocks
        $activeBlocks = IpBlock::activeBlocks(tenant('id'));

        return view('admin.security.dashboard', compact(
            'metrics',
            'criticalEvents',
            'topFailedIps',
            'loginChartData',
            'eventDistribution',
            'activeBlocks',
            'period'
        ));
    }

    /**
     * Get security metrics
     */
    protected function getSecurityMetrics(Carbon $startDate): array
    {
        $tenantId = tenant('id');

        return [
            'total_login_attempts' => LoginAttempt::where('created_at', '>=', $startDate)->count(),
            'failed_login_attempts' => LoginAttempt::where('created_at', '>=', $startDate)
                ->where('successful', false)
                ->count(),
            'successful_logins' => LoginAttempt::where('created_at', '>=', $startDate)
                ->where('successful', true)
                ->count(),
            'active_lockouts' => AccountLockout::where('locked_until', '>', now())->count(),
            'blocked_ips' => IpBlock::activeBlocks($tenantId)->count(),
            'critical_events' => SecurityAuditLog::where('created_at', '>=', $startDate)
                ->where('severity', SecurityAuditLog::SEVERITY_CRITICAL)
                ->count(),
            'warning_events' => SecurityAuditLog::where('created_at', '>=', $startDate)
                ->where('severity', SecurityAuditLog::SEVERITY_WARNING)
                ->count(),
            'total_audit_logs' => SecurityAuditLog::where('created_at', '>=', $startDate)->count(),
        ];
    }

    /**
     * Get login chart data for success vs failure
     */
    protected function getLoginChartData(Carbon $startDate): array
    {
        $days = [];
        $successData = [];
        $failureData = [];

        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i)->startOfDay();
            $days[] = $date->format('M d');

            $successData[] = LoginAttempt::whereDate('created_at', $date)
                ->where('successful', true)
                ->count();

            $failureData[] = LoginAttempt::whereDate('created_at', $date)
                ->where('successful', false)
                ->count();
        }

        return [
            'labels' => $days,
            'success' => $successData,
            'failure' => $failureData,
        ];
    }

    /**
     * Export security report
     */
    public function exportReport(Request $request)
    {
        $period = $request->input('period', '7');
        $startDate = now()->subDays((int)$period);

        $metrics = $this->getSecurityMetrics($startDate);

        $criticalEvents = SecurityAuditLog::query()
            ->where('severity', SecurityAuditLog::SEVERITY_CRITICAL)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'security-report-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($metrics, $criticalEvents, $period) {
            $file = fopen('php://output', 'w');

            // Report header
            fputcsv($file, ['Security Report']);
            fputcsv($file, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period', $period . ' days']);
            fputcsv($file, []);

            // Metrics section
            fputcsv($file, ['SECURITY METRICS']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Login Attempts', $metrics['total_login_attempts']]);
            fputcsv($file, ['Successful Logins', $metrics['successful_logins']]);
            fputcsv($file, ['Failed Login Attempts', $metrics['failed_login_attempts']]);
            fputcsv($file, ['Active Account Lockouts', $metrics['active_lockouts']]);
            fputcsv($file, ['Blocked IP Addresses', $metrics['blocked_ips']]);
            fputcsv($file, ['Critical Events', $metrics['critical_events']]);
            fputcsv($file, ['Warning Events', $metrics['warning_events']]);
            fputcsv($file, ['Total Audit Logs', $metrics['total_audit_logs']]);
            fputcsv($file, []);

            // Critical events section
            fputcsv($file, ['CRITICAL EVENTS']);
            fputcsv($file, ['Date/Time', 'Event Type', 'Email', 'IP Address', 'Description']);
            foreach ($criticalEvents as $event) {
                fputcsv($file, [
                    $event->created_at->format('Y-m-d H:i:s'),
                    $event->event_type,
                    $event->email,
                    $event->ip_address,
                    $event->description,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
