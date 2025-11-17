<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\BookstoreOrder;
use App\Models\BookstoreOrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class BookstoreController extends Controller
{
    /**
     * Display bookstore homepage
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $sortBy = $request->input('sort_by', 'newest');

        $books = LibraryBook::forSale();

        // Search
        if ($search) {
            $books->search($search);
        }

        // Filter by category
        if ($category) {
            $books->byCategory($category);
        }

        // Sorting
        $books = match($sortBy) {
            'price_low' => $books->orderBy('sale_price', 'asc'),
            'price_high' => $books->orderBy('sale_price', 'desc'),
            'popular' => $books->orderBy('sold_count', 'desc'),
            'title' => $books->orderBy('title', 'asc'),
            default => $books->orderBy('created_at', 'desc'),
        };

        $books = $books->paginate(12);

        // Get featured books
        $featuredBooks = LibraryBook::featured()->limit(6)->get();

        // Get categories for filter
        $categories = LibraryBook::forSale()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('tenant.bookstore.index', compact('books', 'featuredBooks', 'categories', 'search', 'category', 'sortBy'));
    }

    /**
     * Display book details
     */
    public function show(LibraryBook $book): View
    {
        // Ensure book is for sale
        if (!$book->is_for_sale) {
            abort(404);
        }

        // Get related books (same category)
        $relatedBooks = LibraryBook::forSale()
            ->where('id', '!=', $book->id)
            ->where('category', $book->category)
            ->limit(4)
            ->get();

        return view('tenant.bookstore.show', compact('book', 'relatedBooks'));
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request, LibraryBook $book): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $book->stock_quantity,
        ]);

        if (!$book->isInStock()) {
            return back()->with('error', 'This book is currently out of stock.');
        }

        $cart = Session::get('bookstore_cart', []);
        $bookId = $book->id;

        if (isset($cart[$bookId])) {
            $cart[$bookId]['quantity'] += $request->quantity;
        } else {
            $cart[$bookId] = [
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->final_price,
                'cover_image' => $book->cover_image_url,
                'quantity' => $request->quantity,
                'max_quantity' => $book->stock_quantity,
            ];
        }

        // Ensure quantity doesn't exceed stock
        if ($cart[$bookId]['quantity'] > $book->stock_quantity) {
            $cart[$bookId]['quantity'] = $book->stock_quantity;
        }

        Session::put('bookstore_cart', $cart);

        return back()->with('success', 'Book added to cart successfully!');
    }

    /**
     * View cart
     */
    public function cart(): View
    {
        $cart = Session::get('bookstore_cart', []);
        $cartTotal = $this->calculateCartTotal($cart);

        return view('tenant.bookstore.cart', compact('cart', 'cartTotal'));
    }

    /**
     * Update cart item
     */
    public function updateCart(Request $request): RedirectResponse
    {
        $request->validate([
            'book_id' => 'required|exists:library_books,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Session::get('bookstore_cart', []);
        $bookId = $request->book_id;

        if (isset($cart[$bookId])) {
            $book = LibraryBook::find($bookId);
            
            if ($request->quantity <= $book->stock_quantity) {
                $cart[$bookId]['quantity'] = $request->quantity;
                Session::put('bookstore_cart', $cart);
                return back()->with('success', 'Cart updated successfully!');
            } else {
                return back()->with('error', 'Quantity exceeds available stock.');
            }
        }

        return back()->with('error', 'Item not found in cart.');
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request): RedirectResponse
    {
        $cart = Session::get('bookstore_cart', []);
        $bookId = $request->book_id;

        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            Session::put('bookstore_cart', $cart);
            return back()->with('success', 'Item removed from cart.');
        }

        return back()->with('error', 'Item not found in cart.');
    }

    /**
     * Clear cart
     */
    public function clearCart(): RedirectResponse
    {
        Session::forget('bookstore_cart');
        return back()->with('success', 'Cart cleared successfully!');
    }

    /**
     * Show checkout form
     */
    public function checkout(): View
    {
        $cart = Session::get('bookstore_cart', []);
        
        if (empty($cart)) {
            return redirect()->route('tenant.bookstore.index')->with('error', 'Your cart is empty.');
        }

        $cartTotal = $this->calculateCartTotal($cart);

        return view('tenant.bookstore.checkout', compact('cart', 'cartTotal'));
    }

    /**
     * Process checkout
     */
    public function processCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money',
            'notes' => 'nullable|string',
        ]);

        $cart = Session::get('bookstore_cart', []);
        
        if (empty($cart)) {
            return redirect()->route('tenant.bookstore.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $cartTotal = $this->calculateCartTotal($cart);

            // Create order
            $order = BookstoreOrder::create([
                'order_number' => BookstoreOrder::generateOrderNumber(),
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'subtotal' => $cartTotal['subtotal'],
                'discount_amount' => $cartTotal['discount'],
                'tax_amount' => $cartTotal['tax'],
                'shipping_cost' => $cartTotal['shipping'],
                'total' => $cartTotal['total'],
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($cart as $item) {
                $book = LibraryBook::findOrFail($item['book_id']);

                // Check stock availability
                if ($book->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$book->title}");
                }

                // Create order item
                BookstoreOrderItem::create([
                    'bookstore_order_id' => $order->id,
                    'library_book_id' => $book->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $book->sale_price,
                    'discount_percentage' => $book->discount_percentage,
                    'discount_amount' => $book->discount_amount * $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'book_title' => $book->title,
                    'book_author' => $book->author,
                    'book_isbn' => $book->isbn,
                ]);

                // Update stock and sold count
                $book->decrement('stock_quantity', $item['quantity']);
                $book->increment('sold_count', $item['quantity']);
            }

            DB::commit();

            // Clear cart
            Session::forget('bookstore_cart');

            return redirect()->route('tenant.bookstore.order.success', $order)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }

    /**
     * Show order success page
     */
    public function orderSuccess(BookstoreOrder $order): View
    {
        return view('tenant.bookstore.order-success', compact('order'));
    }

    /**
     * Calculate cart total
     */
    private function calculateCartTotal(array $cart): array
    {
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0; // Can be calculated based on promo codes
        $tax = $subtotal * 0.05; // 5% tax (can be configured)
        $shipping = $subtotal > 50 ? 0 : 5; // Free shipping over $50
        $total = $subtotal - $discount + $tax + $shipping;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
        ];
    }
}
