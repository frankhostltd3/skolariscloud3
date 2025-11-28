<?php

namespace Skolaris\AcademicReports\Http\Controllers;

use Illuminate\Routing\Controller;
use Skolaris\AcademicReports\Models\AcademicReport;
use Skolaris\AcademicReports\Models\AcademicTerm;
use Skolaris\AcademicReports\Models\StudentFee;
use Illuminate\Http\Request;
use PDF; // Assumes barryvdh/laravel-dompdf is installed

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $termModel = config('skolaris_reports.term_model');
        
        // If user is a student, redirect to their report
        if ($user->hasRole('student') || $user->user_type == 'student') {
            // Get current term
            $term = $termModel::latest('start_date')->first();
            if (!$term) {
                return back()->with('error', 'No academic terms found.');
            }
            return redirect()->route('reports.show', ['studentId' => $user->id, 'termId' => $term->id]);
        }

        // If user is teacher or admin, show selection view
        $terms = $termModel::orderBy('start_date', 'desc')->get();
        
        return view('skolaris-reports::index', compact('terms'));
    }

    public function show($studentId, $termId)
    {
        $report = AcademicReport::with(['marks.subject', 'term', 'student'])
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->firstOrFail();

        $fee = StudentFee::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->first();

        return view('skolaris-reports::report-card', compact('report', 'fee'));
    }

    public function download($reportId)
    {
        $report = AcademicReport::with(['marks.subject', 'term', 'student'])->findOrFail($reportId);
        $fee = StudentFee::where('student_id', $report->student_id)
            ->where('term_id', $report->term_id)
            ->first();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('skolaris-reports::report-card', compact('report', 'fee'));
        
        return $pdf->download('report-card-'.$report->student->name.'.pdf');
    }

    public function classReport($className, $termId)
    {
        $reports = AcademicReport::with(['student', 'term'])
            ->where('class_name', $className)
            ->where('term_id', $termId)
            ->orderBy('average_score', 'desc')
            ->get();

        return view('skolaris-reports::class-summary', compact('reports', 'className'));
    }
}
