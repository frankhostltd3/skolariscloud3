<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\ExaminationBody;
use App\Models\GradingBand;
use App\Models\GradingScheme;
use App\Rules\GradingBandNoOverlap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingSchemesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view grading systems|manage grading systems'])->only(['index','show']);
        $this->middleware(['permission:manage grading systems'])->except(['index','show']);
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $items = GradingScheme::query()
            ->when($q !== '', fn($qb) => $qb->where('name','like',"%$q%"))
            ->withCount('bands')
            ->orderByDesc('is_current')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.academics.grading_schemes.index', compact('items','q'));
    }

    public function create(): View
    {
        $bodies = ExaminationBody::orderBy('name')->get(['id','name','code']);
        return view('tenant.academics.grading_schemes.create', compact('bodies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:190'],
            'country' => ['nullable','string','max:120'],
            'examination_body_id' => ['nullable','exists:examination_bodies,id'],
            'is_current' => ['sometimes','boolean'],
        ]);
        $scheme = GradingScheme::create([
            'name' => $data['name'],
            'country' => $data['country'] ?? null,
            'examination_body_id' => $data['examination_body_id'] ?? null,
            'is_current' => (bool) ($data['is_current'] ?? false),
        ]);
        if ($scheme->is_current) {
            GradingScheme::where('id','<>',$scheme->id)->update(['is_current' => false]);
        }
        return redirect()->route('tenant.academics.grading_schemes.edit', $scheme)->with('status', __('Grading scheme created.'));
    }

    public function show(GradingScheme $grading_scheme): View
    {
        $grading_scheme->load('bands');
        return view('tenant.academics.grading_schemes.show', ['item' => $grading_scheme]);
    }

    public function edit(GradingScheme $grading_scheme): View
    {
        $grading_scheme->load('bands');
        $bodies = ExaminationBody::orderBy('name')->get(['id','name','code']);
        return view('tenant.academics.grading_schemes.edit', ['item' => $grading_scheme, 'bodies' => $bodies]);
    }

    public function update(Request $request, GradingScheme $grading_scheme): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:190'],
            'country' => ['nullable','string','max:120'],
            'examination_body_id' => ['nullable','exists:examination_bodies,id'],
            'is_current' => ['sometimes','boolean'],
        ]);
        $grading_scheme->update([
            'name' => $data['name'],
            'country' => $data['country'] ?? null,
            'examination_body_id' => $data['examination_body_id'] ?? null,
            'is_current' => (bool) ($data['is_current'] ?? false),
        ]);
        if ($grading_scheme->is_current) {
            GradingScheme::where('id','<>',$grading_scheme->id)->update(['is_current' => false]);
        }
        return back()->with('status', __('Band updated.'));
    }

    public function createFromTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template' => ['required','string','in:uk-a-level,uk-gcse,us-gpa,kenya-kcse,nigeria-waec,south-africa-nsc,india-cbse,australia-atar'],
            'name' => ['nullable','string','max:190'],
            'country' => ['nullable','string','max:120'],
            'examination_body_id' => ['nullable','exists:examination_bodies,id'],
            'is_current' => ['sometimes','boolean'],
        ]);

        $templates = $this->getGradingTemplates();
        $template = $templates[$data['template']] ?? null;

        if (!$template) {
            return back()->withErrors(__('Invalid template selected.'));
        }

        // Check if a scheme with this name already exists
        $schemeName = $data['name'] ?? $template['name'];
        if (GradingScheme::where('name', $schemeName)->exists()) {
            return back()->withErrors(__('A grading scheme with this name already exists.'));
        }

        $scheme = GradingScheme::create([
            'name' => $schemeName,
            'country' => $data['country'] ?? $template['country'],
            'examination_body_id' => $data['examination_body_id'] ?? $template['examination_body_id'],
            'is_current' => (bool) ($data['is_current'] ?? false),
        ]);

        // Create bands from template
        foreach ($template['bands'] as $bandData) {
            $scheme->bands()->create($bandData);
        }

        if ($scheme->is_current) {
            GradingScheme::where('id','<>',$scheme->id)->update(['is_current' => false]);
        }

        return redirect()->route('tenant.academics.grading_schemes.edit', $scheme)->with('status', __('Grading scheme created from template.'));
    }

    public function destroy(GradingScheme $grading_scheme): RedirectResponse
    {
        $grading_scheme->delete();
        return redirect()->route('tenant.academics.grading_schemes.index')->with('status', __('Grading scheme deleted.'));
    }

    public function deleteBand(GradingScheme $grading_scheme, GradingBand $band): RedirectResponse
    {
        $this->authorize('update', $grading_scheme);

        if ($band->grading_scheme_id !== $grading_scheme->id) {
            return back()->with('error', __('Band does not belong to this scheme.'));
        }
        $band->delete();
        return back()->with('status', __('Band deleted.'));
    }

    public function updateBand(Request $request, GradingScheme $grading_scheme, GradingBand $band): RedirectResponse
    {
        if ($band->grading_scheme_id !== $grading_scheme->id) {
            return back()->with('error', __('Band does not belong to this scheme.'));
        }
        $data = $request->validate([
            'code' => ['nullable','string','max:20'],
            'label' => ['required','string','max:190'],
            'min_score' => ['required','integer','min:0','max:10000', new GradingBandNoOverlap($grading_scheme->id, $band->id)],
            'max_score' => ['required','integer','min:0','max:10000','gte:min_score'],
            'order' => ['nullable','integer','min:0','max:255'],
            'awards' => ['nullable'],
        ]);

        // Check for unique code within the scheme (excluding this band)
        if (!empty($data['code'])) {
            $codeExists = $grading_scheme->bands()
                ->where('id', '!=', $band->id)
                ->where('code', $data['code'])
                ->exists();

            if ($codeExists) {
                return back()->withErrors(__('Band code must be unique within the grading scheme.'))->withInput();
            }
        }

        $awards = null;
        if (isset($data['awards']) && is_string($data['awards'])) {
            $decoded = json_decode($data['awards'], true);
            $awards = is_array($decoded) ? $decoded : null;
        }

        $band->update([
            'code' => $data['code'] ?? null,
            'label' => $data['label'],
            'min_score' => $data['min_score'],
            'max_score' => $data['max_score'],
            'order' => $data['order'] ?? 0,
            'awards' => $awards,
        ]);
        return back()->with('status', __('Band updated.'));
    }

    private function getGradingTemplates(): array
    {
        return [
            'uk-a-level' => [
                'name' => 'UK A-Level Grades',
                'country' => 'United Kingdom',
                'examination_body_id' => null, // Will be set based on available bodies
                'bands' => [
                    ['code' => 'A*', 'label' => 'A Star', 'min_score' => 85, 'max_score' => 100, 'order' => 1],
                    ['code' => 'A', 'label' => 'A Grade', 'min_score' => 80, 'max_score' => 84, 'order' => 2],
                    ['code' => 'B', 'label' => 'B Grade', 'min_score' => 70, 'max_score' => 79, 'order' => 3],
                    ['code' => 'C', 'label' => 'C Grade', 'min_score' => 60, 'max_score' => 69, 'order' => 4],
                    ['code' => 'D', 'label' => 'D Grade', 'min_score' => 50, 'max_score' => 59, 'order' => 5],
                    ['code' => 'E', 'label' => 'E Grade', 'min_score' => 40, 'max_score' => 49, 'order' => 6],
                    ['code' => 'U', 'label' => 'Ungraded', 'min_score' => 0, 'max_score' => 39, 'order' => 7],
                ],
            ],
            'uk-gcse' => [
                'name' => 'UK GCSE Grades',
                'country' => 'United Kingdom',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => '9', 'label' => 'Grade 9 (highest)', 'min_score' => 95, 'max_score' => 100, 'order' => 1],
                    ['code' => '8', 'label' => 'Grade 8', 'min_score' => 87, 'max_score' => 94, 'order' => 2],
                    ['code' => '7', 'label' => 'Grade 7', 'min_score' => 80, 'max_score' => 86, 'order' => 3],
                    ['code' => '6', 'label' => 'Grade 6', 'min_score' => 70, 'max_score' => 79, 'order' => 4],
                    ['code' => '5', 'label' => 'Grade 5', 'min_score' => 60, 'max_score' => 69, 'order' => 5],
                    ['code' => '4', 'label' => 'Grade 4', 'min_score' => 50, 'max_score' => 59, 'order' => 6],
                    ['code' => '3', 'label' => 'Grade 3', 'min_score' => 35, 'max_score' => 49, 'order' => 7],
                    ['code' => '2', 'label' => 'Grade 2', 'min_score' => 25, 'max_score' => 34, 'order' => 8],
                    ['code' => '1', 'label' => 'Grade 1', 'min_score' => 15, 'max_score' => 24, 'order' => 9],
                    ['code' => 'U', 'label' => 'Ungraded', 'min_score' => 0, 'max_score' => 14, 'order' => 10],
                ],
            ],
            'us-gpa' => [
                'name' => 'US GPA Scale (4.0)',
                'country' => 'United States',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => 'A', 'label' => 'Excellent (4.0)', 'min_score' => 90, 'max_score' => 100, 'order' => 1],
                    ['code' => 'B', 'label' => 'Good (3.0-3.9)', 'min_score' => 80, 'max_score' => 89, 'order' => 2],
                    ['code' => 'C', 'label' => 'Average (2.0-2.9)', 'min_score' => 70, 'max_score' => 79, 'order' => 3],
                    ['code' => 'D', 'label' => 'Below Average (1.0-1.9)', 'min_score' => 60, 'max_score' => 69, 'order' => 4],
                    ['code' => 'F', 'label' => 'Fail (0.0)', 'min_score' => 0, 'max_score' => 59, 'order' => 5],
                ],
            ],
            'kenya-kcse' => [
                'name' => 'Kenya KCSE Grades',
                'country' => 'Kenya',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => 'A', 'label' => 'Excellent', 'min_score' => 81, 'max_score' => 100, 'order' => 1],
                    ['code' => 'A-', 'label' => 'Very Good', 'min_score' => 75, 'max_score' => 80, 'order' => 2],
                    ['code' => 'B+', 'label' => 'Good', 'min_score' => 70, 'max_score' => 74, 'order' => 3],
                    ['code' => 'B', 'label' => 'Good', 'min_score' => 65, 'max_score' => 69, 'order' => 4],
                    ['code' => 'B-', 'label' => 'Above Average', 'min_score' => 60, 'max_score' => 64, 'order' => 5],
                    ['code' => 'C+', 'label' => 'Average', 'min_score' => 55, 'max_score' => 59, 'order' => 6],
                    ['code' => 'C', 'label' => 'Average', 'min_score' => 50, 'max_score' => 54, 'order' => 7],
                    ['code' => 'C-', 'label' => 'Below Average', 'min_score' => 45, 'max_score' => 49, 'order' => 8],
                    ['code' => 'D+', 'label' => 'Poor', 'min_score' => 40, 'max_score' => 44, 'order' => 9],
                    ['code' => 'D', 'label' => 'Poor', 'min_score' => 35, 'max_score' => 39, 'order' => 10],
                    ['code' => 'D-', 'label' => 'Very Poor', 'min_score' => 30, 'max_score' => 34, 'order' => 11],
                    ['code' => 'E', 'label' => 'Fail', 'min_score' => 0, 'max_score' => 29, 'order' => 12],
                ],
            ],
            'nigeria-waec' => [
                'name' => 'Nigeria WAEC Grades',
                'country' => 'Nigeria',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => 'A1', 'label' => 'Excellent', 'min_score' => 75, 'max_score' => 100, 'order' => 1],
                    ['code' => 'B2', 'label' => 'Very Good', 'min_score' => 70, 'max_score' => 74, 'order' => 2],
                    ['code' => 'B3', 'label' => 'Good', 'min_score' => 65, 'max_score' => 69, 'order' => 3],
                    ['code' => 'C4', 'label' => 'Credit', 'min_score' => 60, 'max_score' => 64, 'order' => 4],
                    ['code' => 'C5', 'label' => 'Credit', 'min_score' => 55, 'max_score' => 59, 'order' => 5],
                    ['code' => 'C6', 'label' => 'Credit', 'min_score' => 50, 'max_score' => 54, 'order' => 6],
                    ['code' => 'D7', 'label' => 'Pass', 'min_score' => 45, 'max_score' => 49, 'order' => 7],
                    ['code' => 'E8', 'label' => 'Pass', 'min_score' => 40, 'max_score' => 44, 'order' => 8],
                    ['code' => 'F9', 'label' => 'Fail', 'min_score' => 0, 'max_score' => 39, 'order' => 9],
                ],
            ],
            'south-africa-nsc' => [
                'name' => 'South Africa NSC',
                'country' => 'South Africa',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => '7', 'label' => 'Outstanding Achievement', 'min_score' => 80, 'max_score' => 100, 'order' => 1],
                    ['code' => '6', 'label' => 'Meritorious Achievement', 'min_score' => 70, 'max_score' => 79, 'order' => 2],
                    ['code' => '5', 'label' => 'Substantial Achievement', 'min_score' => 60, 'max_score' => 69, 'order' => 3],
                    ['code' => '4', 'label' => 'Adequate Achievement', 'min_score' => 50, 'max_score' => 59, 'order' => 4],
                    ['code' => '3', 'label' => 'Moderate Achievement', 'min_score' => 40, 'max_score' => 49, 'order' => 5],
                    ['code' => '2', 'label' => 'Elementary Achievement', 'min_score' => 30, 'max_score' => 39, 'order' => 6],
                    ['code' => '1', 'label' => 'Not Achieved', 'min_score' => 0, 'max_score' => 29, 'order' => 7],
                ],
            ],
            'india-cbse' => [
                'name' => 'India CBSE Grading',
                'country' => 'India',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => 'A1', 'label' => 'Excellent (91-100%)', 'min_score' => 91, 'max_score' => 100, 'order' => 1],
                    ['code' => 'A2', 'label' => 'Very Good (81-90%)', 'min_score' => 81, 'max_score' => 90, 'order' => 2],
                    ['code' => 'B1', 'label' => 'Good (71-80%)', 'min_score' => 71, 'max_score' => 80, 'order' => 3],
                    ['code' => 'B2', 'label' => 'Fair (61-70%)', 'min_score' => 61, 'max_score' => 70, 'order' => 4],
                    ['code' => 'C1', 'label' => 'Average (51-60%)', 'min_score' => 51, 'max_score' => 60, 'order' => 5],
                    ['code' => 'C2', 'label' => 'Satisfactory (41-50%)', 'min_score' => 41, 'max_score' => 50, 'order' => 6],
                    ['code' => 'D', 'label' => 'Pass (33-40%)', 'min_score' => 33, 'max_score' => 40, 'order' => 7],
                    ['code' => 'E1', 'label' => 'Needs Improvement (21-32%)', 'min_score' => 21, 'max_score' => 32, 'order' => 8],
                    ['code' => 'E2', 'label' => 'Fail (0-20%)', 'min_score' => 0, 'max_score' => 20, 'order' => 9],
                ],
            ],
            'australia-atar' => [
                'name' => 'Australia ATAR',
                'country' => 'Australia',
                'examination_body_id' => null,
                'bands' => [
                    ['code' => '99.95', 'label' => 'Perfect Score', 'min_score' => 99, 'max_score' => 100, 'order' => 1],
                    ['code' => '99', 'label' => 'Exceptional', 'min_score' => 95, 'max_score' => 98, 'order' => 2],
                    ['code' => '90', 'label' => 'Outstanding', 'min_score' => 85, 'max_score' => 94, 'order' => 3],
                    ['code' => '80', 'label' => 'Excellent', 'min_score' => 75, 'max_score' => 84, 'order' => 4],
                    ['code' => '70', 'label' => 'Very Good', 'min_score' => 65, 'max_score' => 74, 'order' => 5],
                    ['code' => '60', 'label' => 'Good', 'min_score' => 55, 'max_score' => 64, 'order' => 6],
                    ['code' => '50', 'label' => 'Satisfactory', 'min_score' => 45, 'max_score' => 54, 'order' => 7],
                    ['code' => '40', 'label' => 'Limited', 'min_score' => 35, 'max_score' => 44, 'order' => 8],
                    ['code' => '30', 'label' => 'Very Limited', 'min_score' => 25, 'max_score' => 34, 'order' => 9],
                    ['code' => '20', 'label' => 'Elementary', 'min_score' => 15, 'max_score' => 24, 'order' => 10],
                    ['code' => '10', 'label' => 'Not Achieved', 'min_score' => 0, 'max_score' => 14, 'order' => 11],
                ],
            ],
        ];
    }

    public function export(GradingScheme $grading_scheme)
    {
        $this->authorize('view', $grading_scheme);

        $data = [
            'name' => $grading_scheme->name,
            'country' => $grading_scheme->country,
            'examination_body_code' => optional($grading_scheme->examinationBody)->code,
            'is_current' => $grading_scheme->is_current,
            'bands' => $grading_scheme->bands->map(function ($band) {
                return [
                    'code' => $band->code,
                    'label' => $band->label,
                    'min_score' => $band->min_score,
                    'max_score' => $band->max_score,
                    'order' => $band->order,
                    'awards' => $band->awards,
                ];
            })->toArray(),
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->user()->name ?? 'System',
        ];

        $filename = 'grading-scheme-' . str_slug($grading_scheme->name) . '-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportAll()
    {
        $this->authorize('viewAny', GradingScheme::class);

        $schemes = GradingScheme::with('bands')->get();

        $data = [
            'export_version' => '1.0',
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->user()->name ?? 'System',
            'grading_schemes' => $schemes->map(function ($scheme) {
                return [
                    'name' => $scheme->name,
                    'country' => $scheme->country,
                    'examination_body_code' => optional($scheme->examinationBody)->code,
                    'is_current' => $scheme->is_current,
                    'bands' => $scheme->bands->map(function ($band) {
                        return [
                            'code' => $band->code,
                            'label' => $band->label,
                            'min_score' => $band->min_score,
                            'max_score' => $band->max_score,
                            'order' => $band->order,
                            'awards' => $band->awards,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];

        $filename = 'grading-schemes-export-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // Band management methods
    public function storeBand(Request $request, GradingScheme $grading_scheme)
    {
        $this->authorize('update', $grading_scheme);

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:grading_bands,code,NULL,id,grading_scheme_id,' . $grading_scheme->id,
            'label' => 'required|string|max:100',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'order' => 'nullable|integer|min:0',
            'awards' => 'nullable|string|max:255',
        ]);

        // Auto-assign order if not provided
        if (!isset($validated['order'])) {
            $validated['order'] = GradingBand::where('grading_scheme_id', $grading_scheme->id)->max('order') + 1;
        }

        $band = $grading_scheme->bands()->create($validated);

        return redirect()->back()->with('success', __('Grading band added successfully.'));
    }

    public function destroyBand(Request $request, GradingScheme $grading_scheme, GradingBand $band)
    {
        $this->authorize('update', $grading_scheme);

        // Ensure the band belongs to the grading scheme
        if ($band->grading_scheme_id !== $grading_scheme->id) {
            abort(404);
        }

        $band->delete();

        return redirect()->back()->with('success', __('Grading band deleted successfully.'));
    }
}