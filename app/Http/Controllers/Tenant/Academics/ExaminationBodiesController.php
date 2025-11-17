<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\ExaminationBody;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExaminationBodiesController extends Controller
{
    public function __construct()
    {
        // View access for index/show, manage for modifications and setCurrent
        $this->middleware('permission:view examination bodies|manage examination bodies')->only(['index','show']);
        $this->middleware('permission:manage examination bodies')->except(['index','show']);
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $items = ExaminationBody::query()
            ->when($q !== '', fn($qb) => $qb->where('name','like',"%$q%")
                ->orWhere('code','like',"%$q%")
                ->orWhere('country','like',"%$q%"))
            ->orderByDesc('is_current')
            ->orderBy('name')
            ->paginate(15)->withQueryString();
        return view('tenant.academics.examination_bodies.index', compact('items','q'));
    }

    public function create(): View
    {
        return view('tenant.academics.examination_bodies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:20','unique:examination_bodies,code'],
            'country' => ['nullable','string','max:120'],
            'name_translations' => ['nullable'],
        ]);
        if (is_string($data['name_translations'] ?? null)) {
            $decoded = json_decode($data['name_translations'], true);
            $data['name_translations'] = is_array($decoded) ? $decoded : null;
        }
        ExaminationBody::create($data);
        return redirect()->route('tenant.academics.examination_bodies.index')->with('success', __('Examination body created.'));
    }

    public function show(ExaminationBody $examination_body): View
    {
        return view('tenant.academics.examination_bodies.show', ['item' => $examination_body]);
    }

    public function edit(ExaminationBody $examination_body): View
    {
        return view('tenant.academics.examination_bodies.edit', ['item' => $examination_body]);
    }

    public function update(Request $request, ExaminationBody $examination_body): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:20','unique:examination_bodies,code,'.$examination_body->id],
            'country' => ['nullable','string','max:120'],
            'name_translations' => ['nullable'],
        ]);
        if (is_string($data['name_translations'] ?? null)) {
            $decoded = json_decode($data['name_translations'], true);
            $data['name_translations'] = is_array($decoded) ? $decoded : null;
        }
        $examination_body->update($data);
        return redirect()->route('tenant.academics.examination_bodies.show', $examination_body)->with('success', __('Examination body updated.'));
    }

    public function destroy(ExaminationBody $examination_body): RedirectResponse
    {
        $examination_body->delete();
        return redirect()->route('tenant.academics.examination_bodies.index')->with('success', __('Examination body deleted.'));
    }

    public function setCurrent(ExaminationBody $examination_body): RedirectResponse
    {
        ExaminationBody::where('is_current', true)->update(['is_current' => false]);
        $examination_body->update(['is_current' => true]);
        return back()->with('success', __('Current examination body set.'));
    }
}
