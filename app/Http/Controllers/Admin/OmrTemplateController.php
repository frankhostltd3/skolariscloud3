<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\ClassRoom;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OmrTemplateController extends Controller
{
    /**
     * Display template generator form
     */
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        // Get available classes
        $classes = ClassRoom::forSchool($schoolId)
            ->orderBy('name')
            ->get();

        return view('admin.attendance.omr-generator', compact('classes'));
    }

    /**
     * Generate OMR template PDF
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'date' => 'required|date',
            'title' => 'nullable|string|max:200',
            'include_photos' => 'nullable|boolean',
        ]);

        $schoolId = auth()->user()->school_id;

        // Get class with students
        $class = ClassRoom::with('students')
            ->forSchool($schoolId)
            ->findOrFail($validated['class_id']);

        $students = $class->students()->orderBy('name')->get();

        // Get school info
        $school = auth()->user()->school;

        $data = [
            'school' => $school,
            'class' => $class,
            'students' => $students,
            'date' => \Carbon\Carbon::parse($validated['date']),
            'title' => $validated['title'] ?? 'Attendance Sheet',
            'include_photos' => $validated['include_photos'] ?? false,
        ];

        $pdf = Pdf::loadView('admin.attendance.omr-template-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "attendance-sheet-{$class->name}-" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }
}
