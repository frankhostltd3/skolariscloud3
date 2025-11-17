<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\Term;
use App\Models\GradingScheme;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $items = Term::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->orderByDesc('start_date')
            ->paginate(12)
            ->withQueryString();
        return view('tenant.academics.terms.index', compact('items', 'q'));
    }

    public function create(): View
    {
        $schemes = GradingScheme::orderByDesc('is_current')->orderBy('name')->get(['id','name']);
        return view('tenant.academics.terms.create', compact('schemes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'grading_scheme_id' => ['nullable','exists:grading_schemes,id'],
        ]);
        $data['is_current'] = (bool) ($data['is_current'] ?? false);

        if ($data['is_current']) {
            Term::query()->update(['is_current' => false]);
        }

        Term::create($data);

        return redirect()->route('tenant.academics.terms.index')->with('success', __('Term created.'));
    }

    public function show(Term $term): View
    {
        return view('tenant.academics.terms.show', ['item' => $term]);
    }

    public function edit(Term $term): View
    {
        $schemes = GradingScheme::orderByDesc('is_current')->orderBy('name')->get(['id','name']);
        return view('tenant.academics.terms.edit', ['item' => $term, 'schemes' => $schemes]);
    }

    public function update(Request $request, Term $term): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'grading_scheme_id' => ['nullable','exists:grading_schemes,id'],
        ]);
        $data['is_current'] = (bool) ($data['is_current'] ?? false);

        if ($data['is_current']) {
            Term::query()->where('id', '!=', $term->id)->update(['is_current' => false]);
        }

        $term->update($data);

        return redirect()->route('tenant.academics.terms.show', $term)->with('success', __('Term updated.'));
    }

    public function destroy(Term $term): RedirectResponse
    {
        $term->delete();
        return redirect()->route('tenant.academics.terms.index')->with('success', __('Term deleted.'));
    }
}
