<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SecurityAuditController extends Controller
{
    /**
     * Display the audit logs.
     */
    public function index(Request $request)
    {
        $query = SecurityAuditLog::query()->with('user');

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by IP address
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $logs = $query->paginate(50)->withQueryString();

        // Get event types for filter dropdown
        $eventTypes = [
            SecurityAuditLog::EVENT_LOGIN_SUCCESS => 'Login Success',
            SecurityAuditLog::EVENT_LOGIN_FAILED => 'Login Failed',
            SecurityAuditLog::EVENT_LOGOUT => 'Logout',
            SecurityAuditLog::EVENT_PASSWORD_CHANGED => 'Password Changed',
            SecurityAuditLog::EVENT_PASSWORD_RESET_REQUESTED => 'Password Reset Requested',
            SecurityAuditLog::EVENT_PASSWORD_RESET_COMPLETED => 'Password Reset Completed',
            SecurityAuditLog::EVENT_ACCOUNT_LOCKED => 'Account Locked',
            SecurityAuditLog::EVENT_ACCOUNT_UNLOCKED => 'Account Unlocked',
            SecurityAuditLog::EVENT_TWO_FACTOR_ENABLED => 'Two-Factor Enabled',
            SecurityAuditLog::EVENT_TWO_FACTOR_DISABLED => 'Two-Factor Disabled',
            SecurityAuditLog::EVENT_SETTINGS_CHANGED => 'Settings Changed',
            SecurityAuditLog::EVENT_PERMISSION_CHANGED => 'Permission Changed',
        ];

        $severityLevels = [
            SecurityAuditLog::SEVERITY_INFO => 'Info',
            SecurityAuditLog::SEVERITY_WARNING => 'Warning',
            SecurityAuditLog::SEVERITY_CRITICAL => 'Critical',
        ];

        return view('admin.security.audit-logs', compact('logs', 'eventTypes', 'severityLevels'));
    }

    /**
     * Export audit logs to CSV.
     */
    public function export(Request $request)
    {
        $query = SecurityAuditLog::query()->with('user');

        // Apply same filters as index
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $query->orderBy('created_at', 'desc');
        
        // Get all matching records (limit to 10000 for safety)
        $logs = $query->limit(10000)->get();

        // Generate CSV
        $filename = 'security-audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date/Time',
                'Event Type',
                'Email',
                'User ID',
                'IP Address',
                'User Agent',
                'Description',
                'Severity',
                'Metadata',
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->event_type,
                    $log->email,
                    $log->user_id,
                    $log->ip_address,
                    $log->user_agent,
                    $log->description,
                    $log->severity,
                    json_encode($log->metadata),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Delete old audit logs.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30|max:730',
        ]);

        $days = $request->input('days');
        $deleted = SecurityAuditLog::cleanup($days);

        return back()->with('success', "Successfully deleted {$deleted} audit log records older than {$days} days.");
    }

    /**
     * Show details of a specific audit log.
     */
    public function show($id)
    {
        $log = SecurityAuditLog::with('user')->findOrFail($id);

        return view('admin.security.audit-log-details', compact('log'));
    }
}
