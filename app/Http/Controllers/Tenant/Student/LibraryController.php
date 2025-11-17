<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $availability = $request->get('availability', '');

        $books = LibraryBook::query()
            ->search($search)
            ->byCategory($category)
            ->when($availability === 'available', function ($query) {
                $query->available();
            })
            ->when($availability === 'borrowed', function ($query) {
                $query->where('available_quantity', '<', DB::raw('quantity'));
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->appends($request->query());

        // Get categories for filter
        $categories = LibraryBook::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        // Get user's active borrows
        $user = Auth::user();
        $myBorrows = LibraryTransaction::forUser($user->id)
            ->active()
            ->with('book')
            ->get();

        // Calculate statistics
        $totalBooks = LibraryBook::count();
        $availableBooks = LibraryBook::available()->count();
        $myActiveBorrows = $myBorrows->count();
        $overdueBooks = LibraryTransaction::forUser($user->id)->overdue()->count();

        return view('tenant.student.library.index', [
            'books' => $books,
            'categories' => $categories,
            'search' => $search,
            'category' => $category,
            'availability' => $availability,
            'myBorrows' => $myBorrows,
            'statistics' => [
                'total' => $totalBooks,
                'available' => $availableBooks,
                'my_borrows' => $myActiveBorrows,
                'overdue' => $overdueBooks,
            ],
        ]);
    }

    public function show(LibraryBook $library)
    {
        $library->load(['transactions' => function ($query) {
            $query->latest()->limit(5);
        }]);

        $user = Auth::user();
        
        // Check if user has an active borrow for this book
        $myActiveBorrow = LibraryTransaction::forUser($user->id)
            ->forBook($library->id)
            ->active()
            ->first();

        return view('tenant.student.library.show', [
            'book' => $library,
            'myActiveBorrow' => $myActiveBorrow,
        ]);
    }

    public function myBorrows(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status', 'active');

        $transactions = LibraryTransaction::forUser($user->id)
            ->with('book')
            ->when($status === 'active', function ($query) {
                $query->active();
            })
            ->when($status === 'returned', function ($query) {
                $query->where('status', 'returned');
            })
            ->when($status === 'overdue', function ($query) {
                $query->overdue();
            })
            ->orderByDesc('borrowed_at')
            ->paginate(15)
            ->appends($request->query());

        // Calculate statistics
        $activeBorrows = LibraryTransaction::forUser($user->id)->active()->count();
        $overdueCount = LibraryTransaction::forUser($user->id)->overdue()->count();
        $returnedCount = LibraryTransaction::forUser($user->id)->where('status', 'returned')->count();
        $totalFines = LibraryTransaction::forUser($user->id)->sum('fine_amount');

        return view('tenant.student.library.my-borrows', [
            'transactions' => $transactions,
            'status' => $status,
            'statistics' => [
                'active' => $activeBorrows,
                'overdue' => $overdueCount,
                'returned' => $returnedCount,
                'total_fines' => $totalFines,
            ],
        ]);
    }

    public function borrow(Request $request, LibraryBook $library)
    {
        $user = $request->user();

        // Check if user already has active borrow for this book
        $hasActive = LibraryTransaction::forUser($user->id)
            ->forBook($library->id)
            ->active()
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'You already have this book borrowed.');
        }

        // Check if book is available
        if (!$library->isAvailable()) {
            return back()->with('error', 'This book is not available for borrowing.');
        }

        // Check if user has too many overdue books
        $overdueCount = LibraryTransaction::forUser($user->id)->overdue()->count();
        if ($overdueCount >= 3) {
            return back()->with('error', 'You have too many overdue books. Please return them first.');
        }

        // Create transaction and update book availability
        DB::transaction(function () use ($library, $user) {
            // Refresh to avoid stale counts
            $library->refresh();
            
            if (!$library->isAvailable()) {
                abort(409, 'Book no longer available');
            }

            // Decrement available quantity
            $library->decrement('available_quantity');

            // Create library transaction
            LibraryTransaction::create([
                'user_id' => $user->id,
                'library_book_id' => $library->id,
                'borrowed_at' => now(),
                'due_date' => now()->addDays(14), // 14 days borrowing period
                'status' => 'borrowed',
            ]);
        });

        return back()->with('success', 'Book borrowed successfully! Please return it by ' . now()->addDays(14)->format('M d, Y'));
    }

    public function requestExtension(LibraryTransaction $transaction)
    {
        $user = Auth::user();

        // Verify ownership
        if ($transaction->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Check if already overdue
        if ($transaction->isOverdue()) {
            return back()->with('error', 'Cannot extend an overdue book. Please return it first.');
        }

        // Check renewal limit
        if ($transaction->renewal_count >= 2) {
            return back()->with('error', 'Maximum renewals (2) reached for this book.');
        }

        // Extend due date by 7 days
        $transaction->update([
            'due_date' => $transaction->due_date->addDays(7),
            'renewal_count' => $transaction->renewal_count + 1,
        ]);

        return back()->with('success', 'Due date extended by 7 days. New due date: ' . $transaction->due_date->format('M d, Y'));
    }
}