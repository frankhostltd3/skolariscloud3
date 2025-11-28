<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\BookstoreOrder;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrdersController extends Controller
{
    public function index(): View
    {
        $q = request('q');
        $status = request('status');
        $orders = BookstoreOrder::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('order_number', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%")
                      ->orWhere('customer_email', 'like', "%{$q}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('tenant.modules.bookstore.orders.index', compact('orders', 'q', 'status'));
    }

    public function show(BookstoreOrder $order): View
    {
        return view('tenant.modules.bookstore.orders.show', compact('order'));
    }

    public function updateNotes(BookstoreOrder $order): RedirectResponse
    {
        $data = request()->validate([
            'admin_notes' => ['nullable','string'],
        ]);
        $order->update(['admin_notes' => $data['admin_notes'] ?? null]);
        return back()->with('success', __('Notes updated.'));
    }

    public function markPaid(BookstoreOrder $order): RedirectResponse
    {
        $order->update(['payment_status' => 'paid', 'status' => 'processing']);
        AuditLog::create([
            'tenant_id' => tenant('id') ?? null,
            'user_id' => auth()->id(),
            'action' => 'order_paid',
            'description' => 'Order marked as paid by admin',
            'ip' => request()->ip(),
            'context' => json_encode(['order_id' => $order->id]),
        ]);
        return back()->with('success', __('Order marked as paid.'));
    }

    public function markCancelled(BookstoreOrder $order): RedirectResponse
    {
        $order->update(['status' => 'cancelled']);
        AuditLog::create([
            'tenant_id' => tenant('id') ?? null,
            'user_id' => auth()->id(),
            'action' => 'order_cancelled',
            'description' => 'Order cancelled by admin',
            'ip' => request()->ip(),
            'context' => json_encode(['order_id' => $order->id]),
        ]);
        return back()->with('success', __('Order cancelled.'));
    }
}
