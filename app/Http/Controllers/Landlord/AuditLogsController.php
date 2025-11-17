<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandlordAuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = LandlordAuditLog::query();

        if ($from = $request->date('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($user = trim((string) $request->get('user'))) {
            $query->where(function ($q) use ($user) {
                if (is_numeric($user)) {
                    $q->where('user_id', (int) $user);
                }
                $q->orWhereHas('user', function ($uq) use ($user) {
                    $uq->where('email', 'like', "%{$user}%")
                       ->orWhere('name', 'like', "%{$user}%");
                });
            });
        }
        if ($action = trim((string) $request->get('action'))) {
            $query->where('action', 'like', "%{$action}%");
        }

        $logs = $query->with('user')->latest()->paginate(20)->withQueryString();

        return view('landlord.audit.index', [
            'logs' => $logs,
        ]);
    }

    public function export(Request $request)
    {
        $query = LandlordAuditLog::query();
        if ($from = $request->date('from')) { $query->whereDate('created_at', '>=', $from); }
        if ($to = $request->date('to')) { $query->whereDate('created_at', '<=', $to); }
        if ($user = trim((string) $request->get('user'))) {
            $query->where(function ($q) use ($user) {
                if (is_numeric($user)) { $q->where('user_id', (int) $user); }
                $q->orWhereHas('user', function ($uq) use ($user) {
                    $uq->where('email', 'like', "%{$user}%")->orWhere('name', 'like', "%{$user}%");
                });
            });
        }
        if ($action = trim((string) $request->get('action'))) { $query->where('action', 'like', "%{$action}%"); }

        $filename = 'audit-logs-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','created_at','user_id','user_email','action','ip_address','user_agent','context']);
            $query->with('user')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        optional($row->created_at)->toDateTimeString(),
                        $row->user_id,
                        $row->user?->email,
                        $row->action,
                        $row->ip_address,
                        Str::limit((string) $row->user_agent, 180, '…'),
                        json_encode($row->context, JSON_UNESCAPED_SLASHES),
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
