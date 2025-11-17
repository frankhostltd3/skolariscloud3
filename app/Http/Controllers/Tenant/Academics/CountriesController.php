<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view countries|manage countries'])->only(['index','show']);
        $this->middleware(['permission:manage countries'])->except(['index','show']);
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $items = Country::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")
                   ->orWhere('code', 'like', "%$q%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.academics.countries.index', compact('items','q'));
    }

    public function create(): View
    {
        return view('tenant.academics.countries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:3','unique:countries,code'],
            'name' => ['required','string','max:190'],
            'region' => ['nullable','string','max:190'],
            'name_translations' => ['nullable'],
        ]);
        if (isset($data['name_translations']) && is_string($data['name_translations'])) {
            $decoded = json_decode($data['name_translations'], true);
            $data['name_translations'] = is_array($decoded) ? $decoded : null;
        }
        Country::create($data);
        return redirect()->route('tenant.academics.countries.index')->with('status', __('Country created.'));
    }

    public function show(Country $country): View
    {
        return view('tenant.academics.countries.show', compact('country'));
    }

    public function edit(Country $country): View
    {
        return view('tenant.academics.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required','string','max:3','unique:countries,code,'.$country->id],
            'name' => ['required','string','max:190'],
            'region' => ['nullable','string','max:190'],
            'name_translations' => ['nullable'],
        ]);
        if (isset($data['name_translations']) && is_string($data['name_translations'])) {
            $decoded = json_decode($data['name_translations'], true);
            $data['name_translations'] = is_array($decoded) ? $decoded : null;
        }
        $country->update($data);
        return redirect()->route('tenant.academics.countries.index')->with('status', __('Country updated.'));
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        return redirect()->route('tenant.academics.countries.index')->with('status', __('Country deleted.'));
    }
}
