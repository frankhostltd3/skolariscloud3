<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportSettingsController extends Controller
{
    /**
     * Show the report settings form.
     */
    public function edit()
    {
        $assessmentOptions = $this->assessmentOptions();
        $storedSelection = setting('report_card_assessments');
        $selectedAssessments = is_string($storedSelection)
            ? json_decode($storedSelection, true)
            : $storedSelection;

        if (!is_array($selectedAssessments) || empty($selectedAssessments)) {
            $selectedAssessments = array_keys($assessmentOptions);
        }

        return view('admin.reports.settings', compact('assessmentOptions', 'selectedAssessments'));
    }

    /**
     * Update the report settings.
     */
    public function update(Request $request)
    {
        $assessmentOptions = $this->assessmentOptions();

        $request->validate([
            'report_card_show_logo' => 'nullable|boolean',
            'report_card_school_name' => 'nullable|string|max:255',
            'report_card_address' => 'nullable|string|max:500',
            'report_card_color_theme' => 'nullable|string|max:20',
            'report_card_template' => 'nullable|string|in:default,modern,classic',
            'report_card_signature_1' => 'nullable|string|max:100',
            'report_card_signature_2' => 'nullable|string|max:100',
            'report_card_signature_3' => 'nullable|string|max:100',
            'report_card_font_family' => 'nullable|string|in:Arial,Helvetica,Times New Roman,Courier,DejaVu Sans,Montserrat,Quicksand,Poppins,Raleway,Open Sans,Lato,Roboto,Merriweather,Playfair Display',
            'report_card_font_size' => 'nullable|integer|min:10|max:18',
            'report_card_heading_font_weight' => 'nullable|string|in:normal,bold',
            'report_card_logo_width' => 'nullable|integer|min:50|max:500',
            'report_card_logo_height' => 'nullable|integer|min:50|max:300',
            'report_card_photo_width' => 'nullable|integer|min:50|max:200',
            'report_card_photo_height' => 'nullable|integer|min:50|max:200',
            'report_card_assessments' => 'required|array|min:1',
            'report_card_assessments.*' => 'string|max:50',
        ]);

        $selectedAssessments = collect($request->input('report_card_assessments', []))
            ->map(fn ($code) => strtoupper($code))
            ->filter(fn ($code) => array_key_exists($code, $assessmentOptions))
            ->values()
            ->all();

        if (empty($selectedAssessments)) {
            $selectedAssessments = array_keys($assessmentOptions);
        }

        // Handle boolean checkbox
        $showLogo = $request->has('report_card_show_logo');

        setting([
            'report_card_show_logo' => $showLogo,
            'report_card_school_name' => $request->input('report_card_school_name'),
            'report_card_address' => $request->input('report_card_address'),
            'report_card_color_theme' => $request->input('report_card_color_theme', '#0066cc'),
            'report_card_template' => $request->input('report_card_template', 'default'),
            'report_card_signature_1' => $request->input('report_card_signature_1'),
            'report_card_signature_2' => $request->input('report_card_signature_2'),
            'report_card_signature_3' => $request->input('report_card_signature_3'),
            'report_card_font_family' => $request->input('report_card_font_family', 'Arial'),
            'report_card_font_size' => $request->input('report_card_font_size', 12),
            'report_card_heading_font_weight' => $request->input('report_card_heading_font_weight', 'bold'),
            'report_card_logo_width' => $request->input('report_card_logo_width', 200),
            'report_card_logo_height' => $request->input('report_card_logo_height', 100),
            'report_card_photo_width' => $request->input('report_card_photo_width', 80),
            'report_card_photo_height' => $request->input('report_card_photo_height', 80),
            'report_card_assessments' => json_encode($selectedAssessments),
        ]);

        return redirect()->route('admin.reports.settings.edit')
            ->with('success', 'Report card settings updated successfully.');
    }

    /**
     * Build assessment option list from academic settings fallback to defaults.
     */
    protected function assessmentOptions(): array
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
}
