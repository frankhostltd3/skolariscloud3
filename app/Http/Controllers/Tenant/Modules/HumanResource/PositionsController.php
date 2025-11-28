<?php
namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Http\Requests\PositionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PositionsController extends Controller
{

    // Export Excel template for import
    public function exportTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        // Define the required fields in order
        $fields = [
            ['title', 'code', 'department_id'],
        ];
        // Create a temporary file
        $filePath = storage_path('app/positions_import_template.xlsx');
        \Maatwebsite\Excel\Facades\Excel::store(new class($fields) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $fields;
            public function __construct($fields) { $this->fields = $fields; }
            public function array(): array { return $this->fields; }
        }, 'positions_import_template.xlsx');
        return response()->download($filePath, 'positions_import_template.xlsx', $headers)->deleteFileAfterSend(true);
    }

    // Export positions to Excel or PDF
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $positions = Position::with('department')->get();
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('tenant.modules.human_resource.positions.export_pdf', compact('positions'));
            return $pdf->download('positions.pdf');
        } else {
            // Excel export
            return Excel::download(new \App\Exports\PositionsExport, 'positions.xlsx');
        }
    }

    // Import positions from Excel or CSV
    public function import(Request $request, $format)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        try {
            Excel::import(new \App\Imports\PositionsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Positions imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function index(): View
    {
        $query = Position::query()->with('department');
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhereHas('department', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%");
                  });
            });
        }
        $positions = $query->get();
        return view('tenant.modules.human_resource.positions.index', compact('positions'));
    }

    public function create(): View
    {
        $this->authorize('create', Position::class);
        $departments = \App\Models\Department::all();
        return view('tenant.modules.human_resource.positions.create', compact('departments'));
    }

    public function store(PositionRequest $request): RedirectResponse
    {
        $this->authorize('create', Position::class);
        $position = Position::create($request->validated());
        return redirect()->route('tenant.modules.human-resource.positions.index')->with('success', 'Position created successfully.');
    }

    public function show(Position $position): View
    {
        $this->authorize('view', $position);
        return view('tenant.modules.human_resource.positions.show', compact('position'));
    }

    public function edit(Position $position): View
    {
        $this->authorize('update', $position);
        $departments = \App\Models\Department::all();
        return view('tenant.modules.human_resource.positions.edit', compact('position', 'departments'));
    }

    public function update(PositionRequest $request, Position $position): RedirectResponse
    {
        $this->authorize('update', $position);
        $position->update($request->validated());
        return redirect()->route('tenant.modules.human-resource.positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $this->authorize('delete', $position);
        $position->delete();
        return redirect()->route('tenant.modules.human-resource.positions.index')->with('success', 'Position deleted successfully.');
    }
}

