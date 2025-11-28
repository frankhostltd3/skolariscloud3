<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\SalaryScale;
use App\Http\Requests\SalaryScaleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaryScalesController extends Controller
{

    // Export Excel template for import
    public function exportTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        // Define the required fields in order
        $fields = [
            ['name', 'grade', 'min_amount', 'max_amount', 'notes'],
        ];
        // Create a temporary file
        $filePath = storage_path('app/salary_scales_import_template.xlsx');
        \Maatwebsite\Excel\Facades\Excel::store(new class($fields) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $fields;
            public function __construct($fields) { $this->fields = $fields; }
            public function array(): array { return $this->fields; }
        }, 'salary_scales_import_template.xlsx');
        return response()->download($filePath, 'salary_scales_import_template.xlsx', $headers)->deleteFileAfterSend(true);
    }

    // Export salary scales to Excel or PDF
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $salaryScales = SalaryScale::all();
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('tenant.modules.human_resource.salary_scales.export_pdf', compact('salaryScales'));
            return $pdf->download('salary_scales.pdf');
        } else {
            // Excel export
            return Excel::download(new \App\Exports\SalaryScalesExport, 'salary_scales.xlsx');
        }
    }

    // Import salary scales from Excel or CSV
    public function import(Request $request, $format)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        try {
            Excel::import(new \App\Imports\SalaryScalesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Salary scales imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function index(): View
    {
        $query = SalaryScale::query();
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('grade', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%");
            });
        }
        $salaryScales = $query->get();
        return view('tenant.modules.human_resource.salary_scales.index', compact('salaryScales'));
    }

    public function create(): View
    {
        $this->authorize('create', SalaryScale::class);
        $positions = \App\Models\Position::all();
        return view('tenant.modules.human_resource.salary_scales.create', compact('positions'));
    }

    public function store(SalaryScaleRequest $request): RedirectResponse
    {
        $this->authorize('create', SalaryScale::class);
        $salaryScale = SalaryScale::create($request->validated());
        return redirect()->route('tenant.modules.human-resource.salary-scales.index')->with('success', 'Salary scale created successfully.');
    }

    public function show(SalaryScale $salaryScale): View
    {
        $this->authorize('view', $salaryScale);
        return view('tenant.modules.human_resource.salary_scales.show', compact('salaryScale'));
    }

    public function edit(SalaryScale $salaryScale): View
    {
        $this->authorize('update', $salaryScale);
        $positions = \App\Models\Position::all();
        return view('tenant.modules.human_resource.salary_scales.edit', compact('salaryScale', 'positions'));
    }

    public function update(SalaryScaleRequest $request, SalaryScale $salaryScale): RedirectResponse
    {
        $this->authorize('update', $salaryScale);
        $salaryScale->update($request->validated());
        return redirect()->route('tenant.modules.human-resource.salary-scales.index')->with('success', 'Salary scale updated successfully.');
    }

    public function destroy(SalaryScale $salaryScale): RedirectResponse
    {
        $this->authorize('delete', $salaryScale);
        $salaryScale->delete();
        return redirect()->route('tenant.modules.human-resource.salary-scales.index')->with('success', 'Salary scale deleted successfully.');
    }
}
