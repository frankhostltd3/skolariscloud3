<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Book;

class BooksController extends Controller
{
    public function index(): View
    {
        $q = request('q');
        $published = request('published');
        $query = Book::query();
        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('author', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        if ($published !== null && $published !== '') {
            $query->where('is_published', (bool) $published);
        }
        $books = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        return view('tenant.modules.books.index', compact('books', 'q', 'published'));
    }

    public function create(): View
    {
        return view('tenant.modules.books.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required','string','max:191','unique:books,sku'],
            'title' => ['required','string','max:255'],
            'author' => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'is_published' => ['sometimes','boolean'],
            'is_featured' => ['sometimes','boolean'],
        ]);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        Book::create($data);
        return redirect()->route('tenant.modules.bookstore.books.index')
            ->with('status', 'Book created');
    }

    public function show(Book $book): View
    {
        return view('tenant.modules.books.show', ['id' => $book->id, 'book' => $book]);
    }

    public function edit(Book $book): View
    {
        return view('tenant.modules.books.edit', ['id' => $book->id, 'book' => $book]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required','string','max:191','unique:books,sku,' . $book->id],
            'title' => ['required','string','max:255'],
            'author' => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'is_published' => ['sometimes','boolean'],
            'is_featured' => ['sometimes','boolean'],
        ]);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $book->update($data);
        return redirect()->route('tenant.modules.bookstore.books.show', $book->id)
            ->with('status', 'Book updated');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();
        return redirect()->route('tenant.modules.bookstore.books.index')
            ->with('status', 'Book deleted');
    }
}
