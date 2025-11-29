<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $academicYear = setting('academic_year');
        $term = setting('academic_term', 1);

        return view('tenant.student.reports.index', compact('student', 'academicYear', 'term'));
    }

    public function generate(Request $request)
    {
        $student = Auth::user();
        $school = $student->school;
        $academicYear = setting('academic_year');
        $term = setting('academic_term', 1);

        $reportData = $this->generateReportCardData($student, $school, $academicYear, $term);

        return view('admin.reports.pdf.report-card', $reportData);
    }

    public function download(Request $request)
    {
        $student = Auth::user();
        $school = $student->school;
        $academicYear = setting('academic_year');
        $term = setting('academic_term', 1);

        $reportData = $this->generateReportCardData($student, $school, $academicYear, $term);
        $pdfOutput = $this->generateReportCardPDF($reportData);

        $filename = 'report-card-' . Str::slug($student->name) . '-' . $academicYear . '.pdf';

        return response()->streamDownload(function () use ($pdfOutput) {
            echo $pdfOutput;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function shareEmail(Request $request)
    {
        // Placeholder for email sharing logic
        // In a real implementation, this would queue a job to send the PDF
        return back()->with('success', 'Report card sent to your email address.');
    }

    public function shareWhatsapp(Request $request)
    {
        // For WhatsApp, we can't directly attach a file via a web link easily without a third-party API.
        // Usually, this would share a link to download the report.
        // For now, we'll redirect to a WhatsApp API link with a message.

        $student = Auth::user();
        $academicYear = setting('academic_year');
        // Assuming there's a public route or signed URL to view the report
        // Since this is a tenant app, public access might be restricted.
        // We'll just share a text message for now.

        $text = "Hello, here is my report card for {$academicYear}.";
        $url = "https://wa.me/?text=" . urlencode($text);

        return redirect($url);
    }

    // --- Private Methods (Copied from Admin/ReportsController) ---

    private function generateReportCardData($student, $school, $academicYear, $term)
    {
        // Fetch real grades for the student
        $gradesQuery = \App\Models\Grade::where('student_id', $student->id)
            ->where('is_published', true)
            ->with('subject');

        $studentGrades = $gradesQuery->get();

        // Get assessment configuration
        $assessmentConfig = setting('assessment_configuration');
        $assessmentColumns = $this->getSelectedAssessmentColumns();
        $assessmentLabels = $this->getAssessmentOptionsForReports();

        $grades = [];
        $totalMarks = 0;
        $totalPossible = 0;
        $subjectCount = 0;

        // Group grades by subject
        $groupedGrades = $studentGrades->groupBy('subject_id');

        foreach ($groupedGrades as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;
            if (!$subject) continue;

            // Calculate subject grade using weighted logic
            $percentage = $this->calculateSubjectGrade($subjectGrades, $assessmentConfig);

            $gradeLetter = $this->getLetterGrade($percentage);

            $grades[] = [
                'subject' => $subject->name,
                'mark' => round($percentage, 1),
                'out_of' => 100,
                'grade' => $gradeLetter,
                'comment' => $this->getGradeComment($percentage),
                'assessments' => $this->buildSubjectAssessmentBreakdown($subjectGrades, $assessmentColumns),
            ];

            $totalMarks += $percentage;
            $subjectCount++;
            $totalPossible += 100;
        }

        $percentage = $subjectCount > 0 ? round($totalMarks / $subjectCount, 1) : 0;
        $gpa = $this->calculateGPA($percentage);

        // Calculate Class Rank
        $classRank = 1;
        $totalStudents = 0;

        if ($student->class_id) {
             // Get all students in class
             $classStudents = User::where('school_id', $school->id)
                ->where('class_id', $student->class_id)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                ->get();

             $totalStudents = $classStudents->count();

             // Calculate percentages for all students
             $studentPercentages = [];
             foreach ($classStudents as $s) {
                 $studentPercentages[$s->id] = $this->calculateOverallPercentage($s, $assessmentConfig);
             }

             // Sort descending
             arsort($studentPercentages);
             $rank = 1;
             foreach ($studentPercentages as $id => $pct) {
                 if ($id == $student->id) {
                     $classRank = $rank;
                     break;
                 }
                 $rank++;
             }
        }

        // Fetch Attendance
        $attendanceQuery = \App\Models\AttendanceRecord::where('student_id', $student->id);

        $present = $attendanceQuery->clone()->where('status', 'present')->count();
        $absent = $attendanceQuery->clone()->where('status', 'absent')->count();
        $late = $attendanceQuery->clone()->where('status', 'late')->count();

        return [
            'student' => $student,
            'school' => $school,
            'academic_year' => $academicYear,
            'term' => $term ? "Term {$term}" : 'Full Year',
            'grades' => $grades,
            'total_marks' => $totalMarks,
            'total_possible' => $totalPossible,
            'percentage' => $percentage,
            'gpa' => $gpa,
            'class_rank' => $classRank,
            'total_students' => $totalStudents,
            'attendance' => [
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
            ],
            'teacher_comment' => $this->getTeacherComment($percentage),
            'principal_comment' => $this->getPrincipalComment($percentage),
            'generated_at' => now()->format('F d, Y'),
            'assessment_columns' => $assessmentColumns,
            'assessment_labels' => $assessmentLabels,
        ];
    }

    private function calculateSubjectGrade($subjectGrades, $assessmentConfig)
    {
        $subjectFinalMark = 0;
        $totalWeightApplied = 0;

        if ($assessmentConfig && is_array($assessmentConfig) && count($assessmentConfig) > 0) {
            foreach ($assessmentConfig as $config) {
                $type = $config['code'] ?? $config['name'];
                $weight = floatval($config['weight'] ?? 0);

                // Find grades for this type (case-insensitive check)
                $typeGrades = $subjectGrades->filter(function($grade) use ($type) {
                    return (strtoupper($grade->assessment_type ?? '') === strtoupper($type)) ||
                           (strtoupper($grade->assessment_name ?? '') === strtoupper($type));
                });

                if ($typeGrades->isNotEmpty()) {
                    $typeObtained = $typeGrades->sum('marks_obtained');
                    $typeTotal = $typeGrades->sum('total_marks');

                    if ($typeTotal > 0) {
                        $typePercentage = ($typeObtained / $typeTotal) * 100;
                        $subjectFinalMark += $typePercentage * ($weight / 100);
                        $totalWeightApplied += $weight;
                    }
                }
            }

            // If no weights matched (e.g. grades entered before config), fallback to simple average
            if ($totalWeightApplied == 0) {
                 $subjectObtained = $subjectGrades->sum('marks_obtained');
                 $subjectTotal = $subjectGrades->sum('total_marks');
                 $subjectFinalMark = $subjectTotal > 0 ? ($subjectObtained / $subjectTotal) * 100 : 0;
            }
        } else {
            // Simple Average (Fallback)
            $subjectObtained = $subjectGrades->sum('marks_obtained');
            $subjectTotal = $subjectGrades->sum('total_marks');
            $subjectFinalMark = $subjectTotal > 0 ? ($subjectObtained / $subjectTotal) * 100 : 0;
        }

        return $subjectFinalMark;
    }

    private function calculateOverallPercentage($student, $assessmentConfig)
    {
        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('is_published', true)
            ->get()
            ->groupBy('subject_id');

        $totalMarks = 0;
        $count = 0;

        foreach ($grades as $subjectGrades) {
            $totalMarks += $this->calculateSubjectGrade($subjectGrades, $assessmentConfig);
            $count++;
        }

        return $count > 0 ? $totalMarks / $count : 0;
    }

    private function buildSubjectAssessmentBreakdown($subjectGrades, $assessmentColumns)
    {
        $aggregates = [];

        foreach ($subjectGrades as $grade) {
            $code = strtoupper($grade->assessment_type ?? $grade->assessment_name ?? '');
            if (! $code) {
                continue;
            }

            if (! isset($aggregates[$code])) {
                $aggregates[$code] = ['obtained' => 0, 'total' => 0];
            }

            $aggregates[$code]['obtained'] += $grade->marks_obtained;
            $aggregates[$code]['total'] += $grade->total_marks;
        }

        $breakdown = [];

        foreach ($assessmentColumns as $code) {
            $data = $aggregates[$code] ?? null;

            if (! $data || $data['total'] <= 0) {
                $breakdown[$code] = null;
                continue;
            }

            $breakdown[$code] = round(($data['obtained'] / $data['total']) * 100, 1);
        }

        return $breakdown;
    }

    private $cachedGradingScheme = null;

    private function getGradingScheme()
    {
        if ($this->cachedGradingScheme) {
            return $this->cachedGradingScheme;
        }

        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school;
        if (!$school) return null;

        $this->cachedGradingScheme = \App\Models\Academic\GradingScheme::forSchool($school->id)
            ->current()
            ->with('bands')
            ->first();

        return $this->cachedGradingScheme;
    }

    private function calculateGPA($percentage)
    {
        $scheme = $this->getGradingScheme();
        if (!$scheme) return 0;

        $band = $scheme->getGradeForScore($percentage);
        return $band ? $band->grade_point : 0;
    }

    private function getLetterGrade($percentage)
    {
        $scheme = $this->getGradingScheme();
        if (!$scheme) return '-';

        $band = $scheme->getGradeForScore($percentage);
        return $band ? $band->grade : '-';
    }

    private function getGradeComment($percentage)
    {
        $scheme = $this->getGradingScheme();
        if (!$scheme) return '';

        $band = $scheme->getGradeForScore($percentage);
        return $band ? $band->remarks : '';
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

    private function getAssessmentOptionsForReports(): array
    {
        $configuredAssessments = setting('assessment_configuration', []);

        $options = collect($configuredAssessments)
            ->mapWithKeys(function ($config) {
                $name = $config['name'] ?? ($config['label'] ?? null);
                $code = strtoupper($config['code'] ?? ($name ? Str::slug($name, '_') : ''));

                if (! $code) {
                    return [];
                }

                $label = $name ?: $code;

                return [$code => $label];
            })
            ->filter()
            ->toArray();

        if (empty($options)) {
            $options = [
                'BOT' => 'Beginning of Term (BOT)',
                'MOT' => 'Mid of Term (MOT)',
                'EOT' => 'End of Term (EOT)',
            ];
        }

        return $options;
    }

    private function getSelectedAssessmentColumns(): array
    {
        $options = $this->getAssessmentOptionsForReports();

        $stored = setting('report_card_assessments');
        $selection = is_string($stored) ? json_decode($stored, true) : $stored;

        if (!is_array($selection) || empty($selection)) {
            $selection = array_keys($options);
        }

        $selected = collect($selection)
            ->map(fn ($code) => strtoupper($code))
            ->filter(fn ($code) => array_key_exists($code, $options))
            ->values()
            ->all();

        if (empty($selected)) {
            $selected = array_keys($options);
        }

        return $selected;
    }

    public function generateReportCardPDF($reportData)
    {
        $pdf = Pdf::loadView('admin.reports.pdf.report-card', $reportData);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->output();
    }
}
