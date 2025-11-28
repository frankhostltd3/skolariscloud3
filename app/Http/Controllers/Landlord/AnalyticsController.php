<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandlordInvoice;
use App\Models\PaymentTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Tenant;

class AnalyticsController extends Controller
{
    public function __invoke(): View
    {
        $connection = Tenant::query()->getConnection();
        $driver = $connection->getDriverName();

        $monthExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        // Central billing tables may not exist in lightweight test runs
        $hasInvoices = Schema::hasTable((new LandlordInvoice())->getTable());
        $hasTransactions = Schema::hasTable((new PaymentTransaction())->getTable());

        // Tenants
        $monthlySignups = Tenant::query()
            ->selectRaw($monthExpression.' as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(6)
            ->pluck('total', 'month')
            ->reverse();

        $planVelocity = Tenant::query()
            ->select(['data'])
            ->get()
            ->map(function (Tenant $tenant) {
                $payload = $tenant->getAttribute('data');
                if (is_string($payload)) {
                    $payload = json_decode($payload, true) ?: [];
                }
                return $payload['plan'] ?? 'unassigned';
            })
            ->countBy();

        // Billing KPIs (central)
        $now = now();
        $start6 = $now->copy()->subMonths(5)->startOfMonth();
        $months = collect(range(0, 5))->map(fn ($i) => $start6->copy()->addMonths($i)->format('Y-m'));

        $invoiceMonthExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', issued_at)",
            'pgsql' => "to_char(issued_at, 'YYYY-MM')",
            default => "DATE_FORMAT(issued_at, '%Y-%m')",
        };

        $monthlyInvoiced = $hasInvoices
            ? LandlordInvoice::query()
                ->selectRaw($invoiceMonthExpr.' as month, SUM(total) as amount')
                ->whereNotNull('issued_at')
                ->groupBy('month')
                ->pluck('amount', 'month')
            : collect();

        $monthlyCollected = $hasTransactions
            ? PaymentTransaction::query()
                ->selectRaw($monthExpression.' as month, SUM(amount) as amount')
                ->where('status', 'completed')
                ->groupBy('month')
                ->pluck('amount', 'month')
            : collect();

        $revenueTrend = $months->mapWithKeys(fn ($m) => [
            $m => [
                'invoiced' => (float) ($monthlyInvoiced[$m] ?? 0),
                'collected' => (float) ($monthlyCollected[$m] ?? 0),
            ],
        ]);

        $totalReceivables = $hasInvoices
            ? (float) LandlordInvoice::query()
                ->whereNull('paid_at')
                ->whereNull('cancelled_at')
                ->get()
                ->sum(function (LandlordInvoice $invoice) {
                    $paid = data_get($invoice->metadata, 'amount_paid', 0);
                    return max(0, ($invoice->total ?? 0) - (float) $paid);
                })
            : 0.0;

        $overdueInvoices = $hasInvoices
            ? LandlordInvoice::query()
                ->whereNull('paid_at')
                ->whereNull('cancelled_at')
                ->where('due_at', '<', $now->toDateString())
                ->count()
            : 0;

        $dunningCounts = $hasInvoices
            ? LandlordInvoice::query()
                ->select('status', DB::raw('COUNT(*) as total'))
                ->whereIn('status', ['warning','suspended','terminated'])
                ->groupBy('status')
                ->pluck('total', 'status')
            : collect();

        $gatewayBreakdown = $hasTransactions
            ? PaymentTransaction::query()
                ->select('gateway', DB::raw('COUNT(*) as attempts'), DB::raw("SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as successes"))
                ->groupBy('gateway')
                ->get()
                ->map(function ($row) {
                    $attempts = (int) $row->attempts;
                    $successes = (int) $row->successes;
                    return [
                        'gateway' => $row->gateway,
                        'attempts' => $attempts,
                        'successes' => $successes,
                        'conversion' => $attempts > 0 ? round(($successes / $attempts) * 100, 1) : 0.0,
                    ];
                })
            : collect();

        return view('landlord.analytics.index', [
            'monthlySignups' => $monthlySignups,
            'planVelocity' => $planVelocity,
            'revenueTrend' => $revenueTrend,
            'totalReceivables' => $totalReceivables,
            'overdueInvoices' => $overdueInvoices,
            'dunningCounts' => $dunningCounts,
            'gatewayBreakdown' => $gatewayBreakdown,
        ]);
    }

    public function data()
    {
        // Reuse logic from __invoke by inlining minimal queries for API output
        $driver = Tenant::query()->getConnection()->getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $now = now();
        $start6 = $now->copy()->subMonths(5)->startOfMonth();
        $months = collect(range(0, 5))->map(fn ($i) => $start6->copy()->addMonths($i)->format('Y-m'));

        $monthlySignups = Tenant::query()
            ->selectRaw($monthExpression.' as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(6)
            ->pluck('total', 'month')
            ->reverse();

        $planVelocity = Tenant::query()
            ->select(['data'])
            ->get()
            ->map(function (Tenant $tenant) {
                $payload = $tenant->getAttribute('data');
                if (is_string($payload)) {
                    $payload = json_decode($payload, true) ?: [];
                }
                return $payload['plan'] ?? 'unassigned';
            })
            ->countBy();

        $invoiceMonthExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', issued_at)",
            'pgsql' => "to_char(issued_at, 'YYYY-MM')",
            default => "DATE_FORMAT(issued_at, '%Y-%m')",
        };

        $hasInvoices = Schema::hasTable((new LandlordInvoice())->getTable());
        $hasTransactions = Schema::hasTable((new PaymentTransaction())->getTable());

        $monthlyInvoiced = $hasInvoices
            ? \App\Models\LandlordInvoice::query()
                ->selectRaw($invoiceMonthExpr.' as month, SUM(total) as amount')
                ->whereNotNull('issued_at')
                ->groupBy('month')
                ->pluck('amount', 'month')
            : collect();

        $monthlyCollected = $hasTransactions
            ? \App\Models\PaymentTransaction::query()
                ->selectRaw($monthExpression.' as month, SUM(amount) as amount')
                ->where('status', 'completed')
                ->groupBy('month')
                ->pluck('amount', 'month')
            : collect();

        $revenueTrend = $months->mapWithKeys(fn ($m) => [
            $m => [
                'invoiced' => (float) ($monthlyInvoiced[$m] ?? 0),
                'collected' => (float) ($monthlyCollected[$m] ?? 0),
            ],
        ]);

        $totalReceivables = $hasInvoices
            ? (float) \App\Models\LandlordInvoice::query()
                ->whereNull('paid_at')
                ->whereNull('cancelled_at')
                ->get()
                ->sum(function (\App\Models\LandlordInvoice $invoice) {
                    $paid = data_get($invoice->metadata, 'amount_paid', 0);
                    return max(0, ($invoice->total ?? 0) - (float) $paid);
                })
            : 0.0;

        $overdueInvoices = $hasInvoices
            ? \App\Models\LandlordInvoice::query()
                ->whereNull('paid_at')
                ->whereNull('cancelled_at')
                ->where('due_at', '<', $now->toDateString())
                ->count()
            : 0;

        $dunningCounts = $hasInvoices
            ? \App\Models\LandlordInvoice::query()
                ->select('status', DB::raw('COUNT(*) as total'))
                ->whereIn('status', ['warning','suspended','terminated'])
                ->groupBy('status')
                ->pluck('total', 'status')
            : collect();

        $gatewayBreakdown = $hasTransactions
            ? \App\Models\PaymentTransaction::query()
                ->select('gateway', DB::raw('COUNT(*) as attempts'), DB::raw("SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as successes"))
                ->groupBy('gateway')
                ->get()
                ->map(function ($row) {
                    $attempts = (int) $row->attempts;
                    $successes = (int) $row->successes;
                    return [
                        'gateway' => $row->gateway,
                        'attempts' => $attempts,
                        'successes' => $successes,
                        'conversion' => $attempts > 0 ? round(($successes / $attempts) * 100, 1) : 0.0,
                    ];
                })
                ->values()
            : collect();

        return response()->json([
            'months' => $months->values(),
            'monthlySignups' => $monthlySignups->values(),
            'planVelocity' => $planVelocity,
            'revenueTrend' => $revenueTrend->values(),
            'totalReceivables' => $totalReceivables,
            'overdueInvoices' => $overdueInvoices,
            'dunningCounts' => $dunningCounts,
            'gatewayBreakdown' => $gatewayBreakdown,
        ]);
    }
}
