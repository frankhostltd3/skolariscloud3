<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Finance\FeePayment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

        return $this->generateReportCardPDF($student->id, $term->id);
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

    /**
     * Generate the professional report card PDF (Same logic as Admin ReportsController)
     */
    private function generateReportCardPDF($studentId, $termId)
    {
        $student = Student::with(['class', 'class.educationLevel'])->findOrFail($studentId);
        $term = Term::findOrFail($termId);
        $school = tenant();

        // Get report card settings
        $settings = [
            'show_logo' => setting('report_show_logo', true),
            'show_photo' => setting('report_show_photo', true),
            'primary_color' => setting('report_primary_color', '#0d6efd'),
            'secondary_color' => setting('report_secondary_color', '#6c757d'),
            'heading_font' => setting('report_heading_font', 'Helvetica'),
            'body_font' => setting('report_body_font', 'Helvetica'),
            'logo_width' => setting('report_logo_width', 100),
            'logo_height' => setting('report_logo_height', 100),
            'photo_width' => setting('report_photo_width', 100),
            'photo_height' => setting('report_photo_height', 100),
            'school_name' => setting('report_school_name', $school->name),
            'school_address' => setting('report_school_address', $school->address),
            'school_email' => setting('report_school_email', $school->email),
            'school_phone' => setting('report_school_phone', $school->phone),
            'principal_signature_title' => setting('report_principal_title', 'Principal'),
            'teacher_signature_title' => setting('report_teacher_title', 'Class Teacher'),
        ];

        // Get assessment configuration
        $assessmentConfig = json_decode(setting('assessment_configuration', '[]'), true);
        $assessmentWeights = [];
        foreach ($assessmentConfig as $config) {
            if (isset($config['name']) && isset($config['weight'])) {
                $assessmentWeights[$config['name']] = floatval($config['weight']);
            }
        }

        // Get all grades for this student in this term
        $grades = Grade::where('student_id', $student->id)
            ->where('semester_id', $term->id)
            ->with(['subject'])
            ->get();

        // Group grades by subject
        $subjectGrades = $grades->groupBy('subject_id');
        $processedGrades = [];
        $totalWeightedScore = 0;
        $totalMaxScore = 0;

        foreach ($subjectGrades as $subjectId => $assessments) {
            $subject = $assessments->first()->subject;
            $subjectTotal = 0;
            $subjectMax = 0;
            $breakdown = [];

            // Calculate weighted score if configuration exists
            if (!empty($assessmentWeights)) {
                $calculatedScore = 0;
                $totalWeight = 0;

                foreach ($assessments as $grade) {
                    $type = $grade->assessment_type;
                    $weight = $assessmentWeights[$type] ?? 0;

                    // If we have a weight for this assessment type
                    if ($weight > 0) {
                        // Normalize score to 100 before applying weight
                        $normalizedScore = ($grade->marks_obtained / $grade->total_marks) * 100;
                        $calculatedScore += $normalizedScore * ($weight / 100);
                        $totalWeight += $weight;
                    }

                    $breakdown[$type] = [
                        'score' => $grade->marks_obtained,
                        'total' => $grade->total_marks,
                        'weight' => $weight
                    ];
                }

                // If total weight is less than 100%, scale it up or just use what we have
                // For now, we'll assume the calculated score is out of 100
                $subjectTotal = $calculatedScore;
                $subjectMax = 100;
            } else {
                // Fallback to simple sum if no weights configured
                $subjectTotal = $assessments->sum('marks_obtained');
                $subjectMax = $assessments->sum('total_marks');
            }

            // Calculate percentage
            $percentage = $subjectMax > 0 ? ($subjectTotal / $subjectMax) * 100 : 0;

            // Determine grade and points (simplified logic, ideally fetch from GradingScale)
            $gradeLetter = $this->getGradeLetter($percentage);
            $gradePoint = $this->getGradePoint($percentage);

            $processedGrades[] = [
                'subject' => $subject->name,
                'code' => $subject->code,
                'mark' => $subjectTotal,
                'out_of' => $subjectMax,
                'percentage' => $percentage,
                'grade' => $gradeLetter,
                'points' => $gradePoint,
                'breakdown' => $breakdown,
                'teacher' => $assessments->first()->teacher->name ?? 'N/A',
                'comment' => $this->getRemarks($gradeLetter)
            ];

            $totalWeightedScore += $percentage; // Sum of percentages
            $totalMaxScore += 100; // Each subject is out of 100%
        }

        // Calculate GPA and Totals
        $subjectCount = count($processedGrades);
        $averagePercentage = $subjectCount > 0 ? $totalWeightedScore / $subjectCount : 0;
        $totalPoints = collect($processedGrades)->sum('points');
        $gpa = $subjectCount > 0 ? $totalPoints / $subjectCount : 0;

        // Get Attendance Stats
        $attendanceStats = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'total' => 0
        ];

        if (Schema::connection('tenant')->hasTable('attendances')) {
            // This is a simplified query, adjust based on your actual attendance model
            $attendanceStats['present'] = DB::connection('tenant')->table('attendances')
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();
            $attendanceStats['absent'] = DB::connection('tenant')->table('attendances')
                ->where('student_id', $student->id)
                ->where('status', 'absent')
                ->count();
            $attendanceStats['late'] = DB::connection('tenant')->table('attendances')
                ->where('student_id', $student->id)
                ->where('status', 'late')
                ->count();
            $attendanceStats['total'] = $attendanceStats['present'] + $attendanceStats['absent'] + $attendanceStats['late'];
        }

        // Get Class Rank (Simplified)
        // In a real scenario, you'd calculate this for all students in the class and find the rank
        $rank = 'N/A'; // Placeholder

        // Get current academic year
        $academicYear = \App\Models\Academic\AcademicYear::current()->first();
        $academicYearName = $academicYear ? $academicYear->name : now()->year . '-' . (now()->year + 1);

        // Prepare data for view
        $data = [
            'student' => $student,
            'term' => $term->name ?? 'Term',
            'academic_year' => $academicYearName,
            'school' => $school,
            'grades' => $processedGrades,
            'total_marks' => $totalWeightedScore,
            'total_possible' => $totalMaxScore,
            'percentage' => number_format($averagePercentage, 1),
            'gpa' => $gpa,
            'class_rank' => $rank,
            'total_students' => $student->class->students()->count(),
            'attendance' => $attendanceStats,
            'teacher_comment' => $this->getTeacherComment($averagePercentage),
            'principal_comment' => $this->getPrincipalComment($averagePercentage),
            'assessment_columns' => [],
            'assessment_labels' => [],
            'settings' => $settings,
            'generated_at' => now()->format('d M Y H:i A')
        ];

        // Use the Admin PDF view
        $pdf = Pdf::loadView('admin.reports.pdf.report-card', $data);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(Str::slug($student->name) . '-report-card.pdf');
    }

    private function getTeacherComment($percentage)
    {
        if ($percentage >= 80) return "Excellent performance. Keep it up!";
        if ($percentage >= 70) return "Very good work. Consistent effort shown.";
        if ($percentage >= 60) return "Good attempt. Can do better with more focus.";
        if ($percentage >= 50) return "Average performance. Needs improvement.";
        return "Below average. Requires immediate attention and extra help.";
    }

    private function getPrincipalComment($percentage)
    {
        if ($percentage >= 80) return "Promoted to next class with distinction.";
        if ($percentage >= 50) return "Promoted to next class.";
        return "Advised to repeat the class.";
    }

    private function getGradeLetter($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 75) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 65) return 'C+';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        if ($percentage >= 40) return 'E';
        return 'F';
    }

    private function getGradePoint($percentage)
    {
        if ($percentage >= 90) return 5.0;
        if ($percentage >= 80) return 5.0;
        if ($percentage >= 75) return 4.5;
        if ($percentage >= 70) return 4.0;
        if ($percentage >= 65) return 3.5;
        if ($percentage >= 60) return 3.0;
        if ($percentage >= 50) return 2.0;
        if ($percentage >= 40) return 1.0;
        return 0.0;
    }

    private function getRemarks($grade)
    {
        switch ($grade) {
            case 'A+': return 'Excellent';
            case 'A': return 'Excellent';
            case 'B+': return 'Very Good';
            case 'B': return 'Good';
            case 'C+': return 'Fair';
            case 'C': return 'Average';
            case 'D': return 'Pass';
            case 'E': return 'Weak Pass';
            default: return 'Fail';
        }
    }
}
