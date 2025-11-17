<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrdersController extends Controller
{
    public function index(): View
    {
        $q = request('q');
        $status = request('status');
        $orders = Order::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('item_title', 'like', "%{$q}%")
                      ->orWhere('buyer_name', 'like', "%{$q}%")
                      ->orWhere('buyer_email', 'like', "%{$q}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('tenant.modules.bookstore.orders.index', compact('orders', 'q', 'status'));
    }

    public function show(Order $order): View
    {
        return view('tenant.modules.bookstore.orders.show', compact('order'));
    }

    public function updateNotes(Order $order): RedirectResponse
    {
        $data = request()->validate([
            'admin_notes' => ['nullable','string'],
        ]);
        $order->update(['admin_notes' => $data['admin_notes'] ?? null]);
        return back()->with('success', __('Notes updated.'));
    }

    public function markPaid(Order $order): RedirectResponse
    {
        $order->update(['status' => 'paid', 'paid_at' => now()]);
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

    public function markCancelled(Order $order): RedirectResponse
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
