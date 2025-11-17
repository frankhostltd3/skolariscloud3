<?php

namespace App\Http\Controllers\Tenant\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Pamphlet;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        $newBooks = Book::where('is_published', true)->latest()->limit(6)->get();
        $newPamphlets = Pamphlet::where('is_published', true)->latest()->limit(6)->get();
        return view('tenant.storefront.home', compact('newBooks','newPamphlets'));
    }

    public function books(): View
    {
        $q = request('q');
        $query = Book::where('is_published', true);
        if ($q) {
            $query->where(function($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('author', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        $books = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        return view('tenant.storefront.books', compact('books','q'));
    }

    public function pamphlets(): View
    {
        $q = request('q');
        $query = Pamphlet::where('is_published', true);
        if ($q) {
            $query->where(function($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        $pamphlets = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        return view('tenant.storefront.pamphlets', compact('pamphlets','q'));
    }

    public function bookShow(Book $book): View
    {
        abort_if(!$book->is_published, 404);
        return view('tenant.storefront.book_show', compact('book'));
    }

    public function pamphletShow(Pamphlet $pamphlet): View
    {
        abort_if(!$pamphlet->is_published, 404);
        return view('tenant.storefront.pamphlet_show', compact('pamphlet'));
    }
}
