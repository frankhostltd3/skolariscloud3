<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Models\Academic\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PerformanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        $students = $parent ? $parent->students()
            ->with(['class', 'stream', 'account.grades.subject'])
            ->get() : collect([]);

        return view('tenant.parent.performance.index', compact('students'));
    }

    public function show($studentId)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent) {
            abort(403, 'Parent profile not found.');
        }

        $student = $parent->students()
            ->with(['class', 'stream', 'account.grades.subject'])
            ->findOrFail($studentId);

        // Get school for report card header
        $school = request()->attributes->get('currentSchool');

        return view('tenant.parent.performance.show', compact('student', 'school'));
    }

    /**
     * Download the student's report card as PDF.
     */
    public function downloadReport($studentId)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent) {
            abort(403, 'Parent profile not found.');
        }

        $student = $parent->students()
            ->with(['class', 'stream'])
            ->findOrFail($studentId);

        $school = request()->attributes->get('currentSchool');

        // Determine academic year and term using the same logic as admin reports
        $currentYear = AcademicYear::where('is_current', true)->first();
        $academicYear = $currentYear ? ($currentYear->name ?? $currentYear->year ?? date('Y')) : date('Y');
        $term = setting('current_term_number');

        // Reuse the admin report-card generator so parents see identical reports
        $adminReports = app(AdminReportsController::class);
        $reportData = (new \ReflectionClass($adminReports))
            ->getMethod('generateReportCardData')
            ->invoke($adminReports, $student, $school, $academicYear, $term);

        $pdf = Pdf::loadView('admin.reports.pdf.report-card', $reportData);

        $filename = 'Report_Card_' . str_replace(' ', '_', $student->name) . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Email the report card to the parent.
     */
    public function emailReport(Request $request, $studentId)
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent) {
            return back()->with('error', 'Parent profile not found.');
        }

        $student = $parent->students()
            ->with(['class', 'stream'])
            ->findOrFail($studentId);

        $school = request()->attributes->get('currentSchool');

        $currentYear = AcademicYear::where('is_current', true)->first();
        $academicYear = $currentYear ? ($currentYear->name ?? $currentYear->year ?? date('Y')) : date('Y');
        $term = setting('current_term_number');

        $adminReports = app(AdminReportsController::class);
        $reportData = (new \ReflectionClass($adminReports))
            ->getMethod('generateReportCardData')
            ->invoke($adminReports, $student, $school, $academicYear, $term);

        $pdf = Pdf::loadView('admin.reports.pdf.report-card', $reportData);

        $email = $request->input('email', $user->email);

        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($pdf, $student, $school, $email) {
                $message->to($email)
                    ->subject($student->name . ' - Report Card from ' . ($school->name ?? 'School'))
                    ->html('<p>Please find attached the report card for ' . $student->name . '.</p><p>Best regards,<br>' . ($school->name ?? 'School') . '</p>')
                    ->attachData($pdf->output(), 'Report_Card_' . str_replace(' ', '_', $student->name) . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });

            return back()->with('success', 'Report card sent successfully to ' . $email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
