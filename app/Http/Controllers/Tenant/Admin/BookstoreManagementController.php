<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\BookstoreOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BookstoreManagementController extends Controller
{
    /**
     * Display bookstore dashboard
     */
    public function index(): View
    {
        // Statistics
        $totalBooks = LibraryBook::forSale()->count();
        $inStockBooks = LibraryBook::forSale()->inStock()->count();
        $totalRevenue = BookstoreOrder::where('payment_status', 'paid')->sum('total');
        $pendingOrders = BookstoreOrder::where('status', 'pending')->count();
        $todayOrders = BookstoreOrder::whereDate('created_at', today())->count();
        $todayRevenue = BookstoreOrder::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');

        // Recent orders
        $recentOrders = BookstoreOrder::with(['items.book', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Best sellers
        $bestSellers = LibraryBook::forSale()
            ->orderBy('sold_count', 'desc')
            ->limit(5)
            ->get();

        // Low stock alert
        $lowStockBooks = LibraryBook::forSale()
            ->where('stock_quantity', '<=', 5)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Out of stock
        $outOfStockBooks = LibraryBook::forSale()
            ->where('stock_quantity', 0)
            ->count();

        return view('tenant.admin.bookstore.index', compact(
            'totalBooks',
            'inStockBooks',
            'totalRevenue',
            'pendingOrders',
            'todayOrders',
            'todayRevenue',
            'recentOrders',
            'bestSellers',
            'lowStockBooks',
            'outOfStockBooks'
        ));
    }

    /**
     * Display inventory management
     */
    public function inventory(Request $request): View
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $stock_status = $request->input('stock_status');

        $books = LibraryBook::where('is_for_sale', true);

        if ($search) {
            $books->search($search);
        }

        if ($category) {
            $books->byCategory($category);
        }

        if ($stock_status === 'in_stock') {
            $books->where('stock_quantity', '>', 0);
        } elseif ($stock_status === 'out_of_stock') {
            $books->where('stock_quantity', 0);
        } elseif ($stock_status === 'low_stock') {
            $books->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5);
        }

        $books = $books->orderBy('created_at', 'desc')->paginate(20);

        $categories = LibraryBook::forSale()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('tenant.admin.bookstore.inventory', compact('books', 'categories', 'search', 'category', 'stock_status'));
    }

    /**
     * Update book stock
     */
    public function updateStock(Request $request, LibraryBook $book): RedirectResponse
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $book->update([
            'stock_quantity' => $request->stock_quantity,
        ]);

        return back()->with('success', 'Stock updated successfully!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(LibraryBook $book): RedirectResponse
    {
        $book->update([
            'is_featured' => !$book->is_featured,
        ]);

        $message = $book->is_featured ? 'Book added to featured books.' : 'Book removed from featured books.';
        
        return back()->with('success', $message);
    }

    /**
     * Display orders list
     */
    public function orders(Request $request): View
    {
        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');
        $search = $request->input('search');

        $orders = BookstoreOrder::with(['items.book', 'user']);

        if ($status) {
            $orders->where('status', $status);
        }

        if ($paymentStatus) {
            $orders->where('payment_status', $paymentStatus);
        }

        if ($search) {
            $orders->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $orders->orderBy('created_at', 'desc')->paginate(20);

        return view('tenant.admin.bookstore.orders', compact('orders', 'status', 'paymentStatus', 'search'));
    }

    /**
     * Display order details
     */
    public function orderShow(BookstoreOrder $order): View
    {
        $order->load(['items.book', 'user']);

        return view('tenant.admin.bookstore.order-show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, BookstoreOrder $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->admin_notes) {
            $updateData['admin_notes'] = $request->admin_notes;
        }

        // Set timestamps based on status
        if ($request->status === 'confirmed' && !$order->confirmed_at) {
            $updateData['confirmed_at'] = now();
        } elseif ($request->status === 'shipped' && !$order->shipped_at) {
            $updateData['shipped_at'] = now();
        } elseif ($request->status === 'delivered' && !$order->delivered_at) {
            $updateData['delivered_at'] = now();
        }

        $order->update($updateData);

        return back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, BookstoreOrder $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        return back()->with('success', 'Payment status updated successfully!');
    }

    /**
     * Cancel order and restore stock
     */
    public function cancelOrder(BookstoreOrder $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Order is already cancelled.');
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->items as $item) {
                $book = $item->book;
                if ($book) {
                    $book->increment('stock_quantity', $item->quantity);
                    $book->decrement('sold_count', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            return back()->with('success', 'Order cancelled and stock restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }
}
