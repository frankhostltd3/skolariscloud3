<?php

namespace Skolaris\AcademicReports\Services;

use Skolaris\AcademicReports\Models\AcademicReport;

class ReportCalculatorService
{
    public function calculate(AcademicReport $report)
    {
        $marks = $report->marks;
        $total = 0;
        $count = 0;

        foreach ($marks as $mark) {
            $score = $mark->score;
            $gradeInfo = $this->getGrade($score);
            
            $mark->update([
                'grade' => $gradeInfo['grade'],
                'remarks' => $gradeInfo['remark']
            ]);

            $total += $score;
            $count++;
        }

        $average = $count > 0 ? $total / $count : 0;

        $report->update([
            'total_marks' => $total,
            'average_score' => $average,
        ]);
        
        return $report;
    }

    private function getGrade($score)
    {
        $gradingSystem = config('skolaris_reports.grading_system');

        foreach ($gradingSystem as $grade => $details) {
            if ($score >= $details['min'] && $score <= $details['max']) {
                return ['grade' => $grade, 'remark' => $details['remark']];
            }
        }

        return ['grade' => 'N/A', 'remark' => 'N/A'];
    }
}
