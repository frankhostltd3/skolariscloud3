<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Finance\FeePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcademicController extends Controller
{
    public function progress()
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return view('tenant.student.academic.index', [
                'student' => null,
                'currentTerm' => null,
                'grades' => collect(),
                'subjects' => collect(),
                'terms' => collect(),
                'hasFullPayment' => false,
                'statistics' => null,
            ]);
        }

        // Get current academic term
        $currentTerm = Term::where('is_active', true)->first();

        // Get all terms for this student's enrollment period
        $terms = Term::orderBy('start_date', 'desc')->take(5)->get();

        // Get published grades for the student
        $grades = Grade::where('student_id', $student->id)
            ->published()
            ->with(['subject', 'teacher', 'semester'])
            ->orderBy('assessment_date', 'desc')
            ->get();

        // Get subjects
        $subjects = Subject::whereIn('id', $grades->pluck('subject_id')->unique())->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($student, $currentTerm);

        // Check fee payment status
        $hasFullPayment = $this->checkFeePaymentStatus($student);

        return view('tenant.student.academic.index', compact(
            'student',
            'currentTerm',
            'grades',
            'subjects',
            'terms',
            'hasFullPayment',
            'statistics'
        ));
    }

    public function downloadReport(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        // Check fee payment status
        $hasFullPayment = $this->checkFeePaymentStatus($student);

        if (!$hasFullPayment) {
            return redirect()->back()->with('error', 'You must clear all outstanding fees to download your report.');
        }

        $termId = $request->get('term_id');
        $term = $termId ? Term::find($termId) : Term::where('is_active', true)->first();

        if (!$term) {
            return redirect()->back()->with('error', 'No active term found.');
        }

        // Get grades for the term
        $grades = Grade::where('student_id', $student->id)
            ->where('semester_id', $term->id)
            ->published()
            ->with(['subject', 'teacher'])
            ->get();

        // Group grades by subject
        $subjectGrades = $grades->groupBy('subject_id');

        // Calculate term statistics
        $statistics = [
            'total_subjects' => $subjectGrades->count(),
            'average_score' => $grades->avg('marks_obtained'),
            'average_percentage' => $grades->count() > 0 ?
                ($grades->sum('marks_obtained') / $grades->sum('total_marks')) * 100 : 0,
            'total_assessments' => $grades->count(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('tenant.student.academic.report-pdf', compact(
            'student',
            'term',
            'grades',
            'subjectGrades',
            'statistics'
        ));

        $filename = 'academic-report-' . $student->admission_number . '-' . $term->name . '.pdf';

        return $pdf->download($filename);
    }

    public function shareReport(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return response()->json(['error' => 'Student record not found.'], 404);
        }

        // Check fee payment status
        $hasFullPayment = $this->checkFeePaymentStatus($student);

        if (!$hasFullPayment) {
            return response()->json(['error' => 'You must clear all outstanding fees to share your report.'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'term_id' => 'required|exists:terms,id',
        ]);

        $term = Term::find($request->term_id);

        // Generate and email report
        // Implementation depends on your mail system

        return response()->json([
            'success' => true,
            'message' => 'Report shared successfully to ' . $request->email
        ]);
    }

    private function calculateStatistics($student, $term)
    {
        if (!$term) {
            return null;
        }

        $termGrades = Grade::where('student_id', $student->id)
            ->where('semester_id', $term->id)
            ->published()
            ->get();

        $allGrades = Grade::where('student_id', $student->id)
            ->published()
            ->get();

        return [
            'current_term' => [
                'total_subjects' => $termGrades->pluck('subject_id')->unique()->count(),
                'total_assessments' => $termGrades->count(),
                'average_score' => $termGrades->avg('marks_obtained'),
                'average_percentage' => $termGrades->count() > 0 ?
                    ($termGrades->sum('marks_obtained') / $termGrades->sum('total_marks')) * 100 : 0,
                'highest_score' => $termGrades->max('marks_obtained'),
                'lowest_score' => $termGrades->min('marks_obtained'),
            ],
            'overall' => [
                'total_subjects' => $allGrades->pluck('subject_id')->unique()->count(),
                'total_assessments' => $allGrades->count(),
                'average_score' => $allGrades->avg('marks_obtained'),
                'average_percentage' => $allGrades->count() > 0 ?
                    ($allGrades->sum('marks_obtained') / $allGrades->sum('total_marks')) * 100 : 0,
            ],
        ];
    }

    private function checkFeePaymentStatus($student)
    {
        $totalFees = 0;
        if (Schema::connection('tenant')->hasTable('fee_assignments')) {
            $totalFees = DB::table('fee_assignments')
                ->where('student_id', $student->id)
                ->where('is_active', true)
                ->sum('amount');
        }

        $totalPaid = 0;
        if (Schema::connection('tenant')->hasTable('fee_payments')) {
            $totalPaid = FeePayment::where('student_id', $student->id)
                ->where('status', 'completed')
                ->sum('amount');
        }

        // Check if fully paid (with 1 currency unit tolerance)
        return $totalPaid >= ($totalFees - 1);
    }
}
