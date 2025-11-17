<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryTransaction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibraryController extends Controller
{
    /**
     * Display library dashboard
     */
    public function index(): View
    {
        // Statistics
        $totalBooks = LibraryBook::count();
        $availableBooks = LibraryBook::available()->count();
        $borrowedBooks = LibraryTransaction::active()->count();
        $overdueBooks = LibraryTransaction::overdue()->count();
        
        // Recent activities
        $recentBorrows = LibraryTransaction::with(['user', 'book', 'issuedByStaff'])
            ->latest('borrowed_at')
            ->take(10)
            ->get();
        
        // Popular books
        $popularBooks = LibraryBook::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();
        
        // Category distribution
        $categoryStats = LibraryBook::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();
        
        // Overdue transactions
        $overdueTransactions = LibraryTransaction::overdue()
            ->with(['user', 'book'])
            ->latest('due_date')
            ->take(10)
            ->get();

        return view('tenant.modules.library.index', compact(
            'totalBooks',
            'availableBooks',
            'borrowedBooks',
            'overdueBooks',
            'recentBorrows',
            'popularBooks',
            'categoryStats',
            'overdueTransactions'
        ));
    }

    /**
     * Display books list
     */
    public function books(Request $request): View
    {
        $query = LibraryBook::query();

        // Search
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->byCategory($category);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->byStatus($status);
        }

        $books = $query->withCount('transactions')
            ->latest()
            ->paginate(20);

        $categories = LibraryBook::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('tenant.modules.library.books.index', compact('books', 'categories'));
    }

    /**
     * Show create book form
     */
    public function createBook(): View
    {
        return view('tenant.modules.library.books.create');
    }

    /**
     * Store new book
     */
    public function storeBook(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:library_books,isbn',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:200',
            'location' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
            'language' => 'nullable|string|max:100',
            'pages' => 'nullable|integer|min:1',
            // Bookstore fields
            'is_for_sale' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_featured' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['available_quantity'] = $validated['quantity'];
        $validated['status'] = 'available';
        $validated['is_for_sale'] = $request->has('is_for_sale');
        $validated['is_featured'] = $request->has('is_featured');

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('book-covers', 'public');
            $validated['cover_image_path'] = $path;
        }

        LibraryBook::create($validated);

        return redirect()->route('tenant.modules.library.books.index')
            ->with('success', 'Book added successfully!');
    }

    /**
     * Show edit book form
     */
    public function editBook(LibraryBook $book): View
    {
        return view('tenant.modules.library.books.edit', compact('book'));
    }

    /**
     * Update book
     */
    public function updateBook(Request $request, LibraryBook $book): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:library_books,isbn,' . $book->id,
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:200',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:available,maintenance,lost,damaged',
            'purchase_price' => 'nullable|numeric|min:0',
            'language' => 'nullable|string|max:100',
            'pages' => 'nullable|integer|min:1',
            // Bookstore fields
            'is_for_sale' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_featured' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_for_sale'] = $request->has('is_for_sale');
        $validated['is_featured'] = $request->has('is_featured');

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image_path && \Storage::disk('public')->exists($book->cover_image_path)) {
                \Storage::disk('public')->delete($book->cover_image_path);
            }
            
            $path = $request->file('cover_image')->store('book-covers', 'public');
            $validated['cover_image_path'] = $path;
        }

        $book->update($validated);

        return redirect()->route('tenant.modules.library.books.index')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Delete book
     */
    public function destroyBook(LibraryBook $book): RedirectResponse
    {
        // Check if book has active borrows
        if ($book->activeBorrows()->exists()) {
            return back()->with('error', 'Cannot delete book with active borrows!');
        }

        $book->delete();

        return redirect()->route('tenant.modules.library.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    /**
     * Show book details
     */
    public function showBook(LibraryBook $book): View
    {
        $book->load(['transactions' => function ($query) {
            $query->latest()->take(20);
        }]);

        return view('tenant.modules.library.books.show', compact('book'));
    }

    /**
     * Display transactions list
     */
    public function transactions(Request $request): View
    {
        $query = LibraryTransaction::with(['user', 'book', 'issuedByStaff']);

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search by user or book
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('borrowed_at')->paginate(20);

        return view('tenant.modules.library.transactions.index', compact('transactions'));
    }

    /**
     * Show borrow form
     */
    public function borrowForm(): View
    {
        $availableBooks = LibraryBook::available()->get();
        $users = User::orderBy('name')->get();

        return view('tenant.modules.library.transactions.borrow', compact('availableBooks', 'users'));
    }

    /**
     * Process borrow request
     */
    public function borrow(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'library_book_id' => 'required|exists:library_books,id',
            'due_days' => 'required|integer|min:1|max:90',
            'notes' => 'nullable|string',
        ]);

        $book = LibraryBook::findOrFail($validated['library_book_id']);

        if (!$book->isAvailable()) {
            return back()->with('error', 'Book is not available for borrowing!');
        }

        // Create transaction
        LibraryTransaction::create([
            'user_id' => $validated['user_id'],
            'library_book_id' => $validated['library_book_id'],
            'issued_by' => auth()->id(),
            'borrowed_at' => now(),
            'due_date' => now()->addDays($validated['due_days']),
            'status' => 'borrowed',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Decrease available quantity
        $book->decrement('available_quantity');

        return redirect()->route('tenant.modules.library.transactions.index')
            ->with('success', 'Book borrowed successfully!');
    }

    /**
     * Process return
     */
    public function returnBook(Request $request, LibraryTransaction $transaction): RedirectResponse
    {
        if ($transaction->status !== 'borrowed') {
            return back()->with('error', 'Book is not currently borrowed!');
        }

        $validated = $request->validate([
            'condition_notes' => 'nullable|string',
            'fine_amount' => 'nullable|numeric|min:0',
        ]);

        $transaction->update([
            'returned_at' => now(),
            'returned_to' => auth()->id(),
            'status' => 'returned',
            'condition_notes' => $validated['condition_notes'] ?? null,
            'fine_amount' => $validated['fine_amount'] ?? ($transaction->isOverdue() ? $transaction->calculateFine() : 0),
        ]);

        // Increase available quantity
        $transaction->book->increment('available_quantity');

        return back()->with('success', 'Book returned successfully!');
    }
}
