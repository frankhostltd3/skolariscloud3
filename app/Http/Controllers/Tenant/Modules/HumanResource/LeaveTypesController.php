<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Http\Requests\LeaveTypeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveTypesController extends Controller
{
    public function index(): View
    {
        $query = LeaveType::query();
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        $leaveTypes = $query->get();
        return view('tenant.modules.human_resource.leave_types.index', compact('leaveTypes'));
    }

    public function create(): View
    {
        $this->authorize('create', LeaveType::class);
        return view('tenant.modules.human_resource.leave_types.create');
    }

    public function store(LeaveTypeRequest $request): RedirectResponse
    {
        $this->authorize('create', LeaveType::class);
        $leaveType = LeaveType::create($request->validated());
        return redirect()->route('tenant.modules.human_resources.leave-types.index')->with('success', 'Leave type created successfully.');
    }

    public function show(LeaveType $leaveType): View
    {
        $this->authorize('view', $leaveType);
        return view('tenant.modules.human_resource.leave_types.show', compact('leaveType'));
    }

    public function edit(LeaveType $leaveType): View
    {
        $this->authorize('update', $leaveType);
        return view('tenant.modules.human_resource.leave_types.edit', compact('leaveType'));
    }

    public function update(LeaveTypeRequest $request, LeaveType $leaveType): RedirectResponse
    {
        $this->authorize('update', $leaveType);
        $leaveType->update($request->validated());
        return redirect()->route('tenant.modules.human_resources.leave-types.index')->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        $this->authorize('delete', $leaveType);
        $leaveType->delete();
        return redirect()->route('tenant.modules.human_resources.leave-types.index')->with('success', 'Leave type deleted successfully.');
    }

    // Export Excel template for import
    public function exportTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Define the required fields in order
        $fields = [
            ['name', 'code', 'default_days', 'requires_approval', 'description'],
        ];

        // Create Excel file directly for download
        return Excel::download(new class($fields) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $fields;
            public function __construct($fields) { $this->fields = $fields; }
            public function array(): array { return $this->fields; }
        }, 'leave_types_import_template.xlsx');
    }

    // Export SQL template for import
    public function exportSqlTemplate(): \Symfony\Component\HttpFoundation\Response
    {
        $sqlTemplate = "-- Leave Types SQL Import Template
-- Copy and modify this template to import leave types
-- Only INSERT INTO leave_types statements will be processed

INSERT INTO leave_types (name, code, default_days, requires_approval, description) VALUES
('Annual Leave', 'AL', 25, 1, 'Standard annual leave allowance'),
('Sick Leave', 'SL', 10, 1, 'Medical leave for illness'),
('Maternity Leave', 'ML', 90, 0, 'Maternity leave for new mothers'),
('Paternity Leave', 'PL', 5, 0, 'Paternity leave for new fathers'),
('Personal Leave', 'PSL', 3, 1, 'Personal time off'),
('Compassionate Leave', 'CL', 5, 0, 'Leave for family emergencies');

-- You can also use individual INSERT statements:
-- INSERT INTO leave_types (name, code, default_days, requires_approval, description) VALUES ('Emergency Leave', 'EL', 2, 1, 'Emergency situations');

-- Notes:
-- - requires_approval: 1 = requires approval, 0 = does not require approval
-- - default_days: integer between 0-365
-- - code: unique identifier, max 10 characters
-- - name: leave type name, max 255 characters
-- - description: optional description, max 1000 characters
";

        return response($sqlTemplate, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="leave_types_import_template.sql"'
        ]);
    }

    // Export leave types to Excel or PDF
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $leaveTypes = LeaveType::all();
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('tenant.modules.human_resource.leave_types.export_pdf', compact('leaveTypes'));
            return $pdf->download('leave_types.pdf');
        } else {
            // Excel export
            return Excel::download(new \App\Exports\LeaveTypesExport, 'leave_types.xlsx');
        }
    }

    // Import leave types from Excel or CSV
    public function import(Request $request, $format)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,sql',
        ]);

        try {
            if ($format === 'sql') {
                return $this->importSQL($request->file('file'));
            } else {
                Excel::import(new \App\Imports\LeaveTypesImport, $request->file('file'));
                return redirect()->back()->with('success', 'Leave types imported successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // Import from SQL file
    private function importSQL($file)
    {
        $content = file_get_contents($file->getRealPath());

        // Parse SQL INSERT statements
        $sqlParser = new \App\Services\SqlImportParser();
        $data = $sqlParser->parseLeaveTypesInsert($content);

        if (empty($data)) {
            throw new \Exception('No valid INSERT statements found for leave_types table.');
        }

        $imported = 0;
        $errors = [];

        foreach ($data as $index => $leaveTypeData) {
            try {
                // Validate the data
                $validatedData = validator($leaveTypeData, [
                    'name' => 'required|string|max:255',
                    'code' => 'required|string|max:10|unique:leave_types,code',
                    'default_days' => 'required|integer|min:0|max:365',
                    'requires_approval' => 'boolean',
                    'description' => 'nullable|string|max:1000',
                ])->validate();

                LeaveType::create($validatedData);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "SQL import completed. Imported: {$imported} records.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (and " . (count($errors) - 5) . " more errors)";
            }
        }

        return redirect()->back()->with('success', $message);
    }
}
