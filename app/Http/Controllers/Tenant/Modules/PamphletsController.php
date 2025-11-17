<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Pamphlet;

class PamphletsController extends Controller
{
    public function index(): View
    {
        $q = request('q');
        $published = request('published');
        $query = Pamphlet::query();
        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%");
            });
        }
        if ($published !== null && $published !== '') {
            $query->where('is_published', (bool) $published);
        }
        $pamphlets = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        return view('tenant.modules.pamphlets.index', compact('pamphlets', 'q', 'published'));
    }

    public function create(): View
    {
        return view('tenant.modules.pamphlets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required','string','max:191','unique:pamphlets,sku'],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'is_published' => ['sometimes','boolean'],
            'is_featured' => ['sometimes','boolean'],
        ]);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        Pamphlet::create($data);
        return redirect()->route('tenant.modules.bookstore.pamphlets.index')
            ->with('status', 'Pamphlet created');
    }

    public function show(Pamphlet $pamphlet): View
    {
        return view('tenant.modules.pamphlets.show', ['id' => $pamphlet->id, 'pamphlet' => $pamphlet]);
    }

    public function edit(Pamphlet $pamphlet): View
    {
        return view('tenant.modules.pamphlets.edit', ['id' => $pamphlet->id, 'pamphlet' => $pamphlet]);
    }

    public function update(Request $request, Pamphlet $pamphlet): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required','string','max:191','unique:pamphlets,sku,' . $pamphlet->id],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'is_published' => ['sometimes','boolean'],
            'is_featured' => ['sometimes','boolean'],
        ]);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $pamphlet->update($data);
        return redirect()->route('tenant.modules.bookstore.pamphlets.show', $pamphlet->id)
            ->with('status', 'Pamphlet updated');
    }

    public function destroy(Pamphlet $pamphlet): RedirectResponse
    {
        $pamphlet->delete();
        return redirect()->route('tenant.modules.bookstore.pamphlets.index')
            ->with('status', 'Pamphlet deleted');
    }
}
