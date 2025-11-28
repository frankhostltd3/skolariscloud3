<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class AcademicSettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            // Academic Year Settings
            'current_academic_year' => setting('current_academic_year', '2024-2025'),
            'academic_year_start' => setting('academic_year_start', date('Y-09-01')),
            'academic_year_end' => setting('academic_year_end', date('Y-06-30', strtotime('+1 year'))),
            'semester_system' => setting('semester_system', 'semester'),

            // Grading System
            'grading_scale' => setting('grading_scale', 'percentage'),
            'passing_grade' => setting('passing_grade', '60'),
            'grade_a_min' => setting('grade_a_min', '90'),
            'grade_a_max' => setting('grade_a_max', '100'),
            'grade_a_gpa' => setting('grade_a_gpa', '4.0'),
            'grade_b_min' => setting('grade_b_min', '80'),
            'grade_b_max' => setting('grade_b_max', '89'),
            'grade_b_gpa' => setting('grade_b_gpa', '3.0'),
            'grade_c_min' => setting('grade_c_min', '70'),
            'grade_c_max' => setting('grade_c_max', '79'),
            'grade_c_gpa' => setting('grade_c_gpa', '2.0'),
            'grade_d_min' => setting('grade_d_min', '60'),
            'grade_d_max' => setting('grade_d_max', '69'),
            'grade_d_gpa' => setting('grade_d_gpa', '1.0'),
            'grade_f_min' => setting('grade_f_min', '0'),
            'grade_f_max' => setting('grade_f_max', '59'),
            'grade_f_gpa' => setting('grade_f_gpa', '0.0'),

            // Assessment Configuration
            'assessment_configuration' => json_decode(setting('assessment_configuration', '[]'), true),

            // Attendance Settings
            'attendance_marking' => setting('attendance_marking', 'automatic'),
            'minimum_attendance' => setting('minimum_attendance', '75'),
            'late_arrival_grace' => setting('late_arrival_grace', '15'),
            'attendance_notifications' => setting('attendance_notifications', 'enabled'),
        ];

        return view('settings.academic', compact('settings'));
    }

    public function update(Request $request)
    {
        $formType = $request->input('form_type');

        switch ($formType) {
            case 'academic_year':
                return $this->updateAcademicYear($request);
            case 'grading':
                return $this->updateGrading($request);
            case 'assessment_configuration':
                return $this->updateAssessmentConfiguration($request);
            case 'attendance':
                return $this->updateAttendance($request);
            default:
                return redirect()->back()->with('error', 'Invalid form submission.');
        }
    }

    private function updateAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'current_academic_year' => 'required|string|max:20',
            'academic_year_start' => 'required|date',
            'academic_year_end' => 'required|date|after:academic_year_start',
            'semester_system' => 'required|in:semester,trimester,quarter,annual',
        ]);

        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        Cache::forget('settings');

        return redirect()->route('tenant.settings.admin.academic')
            ->with('status', 'Academic year settings updated successfully.');
    }

    private function updateGrading(Request $request)
    {
        $validated = $request->validate([
            'grading_scale' => 'required|in:percentage,gpa_4,gpa_5,letter',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'grade_a_min' => 'required|numeric|min:0|max:100',
            'grade_a_max' => 'required|numeric|min:0|max:100',
            'grade_a_gpa' => 'required|numeric|min:0|max:5',
            'grade_b_min' => 'required|numeric|min:0|max:100',
            'grade_b_max' => 'required|numeric|min:0|max:100',
            'grade_b_gpa' => 'required|numeric|min:0|max:5',
            'grade_c_min' => 'required|numeric|min:0|max:100',
            'grade_c_max' => 'required|numeric|min:0|max:100',
            'grade_c_gpa' => 'required|numeric|min:0|max:5',
            'grade_d_min' => 'required|numeric|min:0|max:100',
            'grade_d_max' => 'required|numeric|min:0|max:100',
            'grade_d_gpa' => 'required|numeric|min:0|max:5',
            'grade_f_min' => 'required|numeric|min:0|max:100',
            'grade_f_max' => 'required|numeric|min:0|max:100',
            'grade_f_gpa' => 'required|numeric|min:0|max:5',
        ]);

        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        Cache::forget('settings');

        return redirect()->route('tenant.settings.admin.academic')
            ->with('status', 'Grading system updated successfully.');
    }

    private function updateAssessmentConfiguration(Request $request)
    {
        $validated = $request->validate([
            'assessments' => 'required|array',
            'assessments.*.name' => 'required|string|max:50',
            'assessments.*.code' => 'required|string|max:10',
            'assessments.*.weight' => 'required|numeric|min:0|max:100',
        ]);

        // Validate total weight equals 100 (optional, but good practice)
        // For now, we'll just save it as is, allowing flexibility.

        setting(['assessment_configuration' => json_encode($validated['assessments'])]);

        Cache::forget('settings');

        return redirect()->route('tenant.settings.admin.academic')
            ->with('status', 'Assessment configuration updated successfully.');
    }

    private function updateAttendance(Request $request)
    {
        $validated = $request->validate([
            'attendance_marking' => 'required|in:automatic,manual,biometric',
            'minimum_attendance' => 'required|numeric|min:0|max:100',
            'late_arrival_grace' => 'required|numeric|min:0|max:60',
            'attendance_notifications' => 'required|in:enabled,disabled',
        ]);

        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        Cache::forget('settings');

        return redirect()->route('tenant.settings.admin.academic')
            ->with('status', 'Attendance settings updated successfully.');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Cache::forget('settings');

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully.',
        ]);
    }
}
