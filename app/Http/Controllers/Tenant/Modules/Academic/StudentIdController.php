<?php

namespace App\Http\Controllers\Tenant\Modules\Academic;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentIdSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentIdController extends Controller
{
    public function index(): View
    {
        $students = Student::orderBy('name')->get();
        $templates = StudentIdSetting::getActive();

        return view('tenant.modules.academic.student_ids.index', compact('students', 'templates'));
    }

    public function generate(Request $request): View
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'template_id' => 'required|exists:student_id_settings,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $template = StudentIdSetting::findOrFail($request->template_id);

        // Generate QR code data
        $qrData = $this->generateQrData($student);

        // Generate SVG content
        $svgContent = $this->generateSvg($student, $template);

        return view('tenant.modules.academic.student_ids.generate', compact('student', 'template', 'qrData', 'svgContent'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'template_id' => 'required|exists:student_id_settings,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $template = StudentIdSetting::findOrFail($request->template_id);

        // Generate QR code data
        $qrData = $this->generateQrData($student);

        // Generate SVG content
        $svgContent = $this->generateSvg($student, $template);

        return view('tenant.modules.academic.student_ids.preview', compact('student', 'template', 'qrData', 'svgContent'));
    }

    public function downloadSvg(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'template_id' => 'required|exists:student_id_settings,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $template = StudentIdSetting::findOrFail($request->template_id);

        $svgContent = $this->generateSvg($student, $template);

        $filename = 'student_id_' . $student->id . '_' . now()->format('Y-m-d') . '.svg';

        return response($svgContent)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadPng(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'template_id' => 'required|exists:student_id_settings,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $template = StudentIdSetting::findOrFail($request->template_id);

        $svgContent = $this->generateSvg($student, $template);
        $filename = 'student_id_' . $student->id . '_' . now()->format('Y-m-d') . '.png';

        return response($svgContent)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateQrData(Student $student): string
    {
        $data = [
            'id' => $student->id,
            'name' => $student->name,
            'admission_no' => $student->admission_no,
            'email' => $student->email,
            'dob' => $student->dob?->format('Y-m-d'),
            'type' => 'student',
            'generated_at' => now()->toISOString(),
        ];

        return json_encode($data);
    }

    private function generateSvg(Student $student, StudentIdSetting $template): string
    {
        $width = $template->card_width * 3.779527559; // Convert mm to pixels (96 DPI)
        $height = $template->card_height * 3.779527559;

        $qrData = $this->generateQrData($student);
        $qrCodeSvg = QrCode::format('svg')->size($template->qr_code_size)->generate($qrData);

        $svg = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';

        // Background
        $svg .= '<rect width="100%" height="100%" fill="' . $template->background_color . '" rx="' . ($template->layout_settings['border_radius'] ?? 8) . '" ry="' . ($template->layout_settings['border_radius'] ?? 8) . '" stroke="' . ($template->layout_settings['border_color'] ?? '#e5e7eb') . '" stroke-width="' . ($template->layout_settings['border_width'] ?? 2) . '"/>';

        $margin = 15;
        
        // School Logo as watermark (centered, semi-transparent, behind everything)
        $logoSize = 80;
        $logoUrl = school_logo();
        
        if ($logoUrl) {
            try {
                $logoPath = school_logo_path();
                if ($logoPath && file_exists($logoPath)) {
                    $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                    $logoX = ($width - $logoSize) / 2;
                    $logoY = ($height - $logoSize) / 2 - 10; // Slightly above center
                    
                    $svg .= '<g opacity="0.15">';
                    
                    if ($extension === 'svg') {
                        $logoSvgContent = file_get_contents($logoPath);
                        $logoSvgContent = preg_replace('/<\?xml.*?\?>/i', '', $logoSvgContent);
                        $logoSvgContent = preg_replace('/<svg[^>]*>/i', '', $logoSvgContent);
                        $logoSvgContent = str_replace('</svg>', '', $logoSvgContent);
                        
                        $scale = $logoSize / 300;
                        $svg .= '<g transform="translate(' . $logoX . ',' . $logoY . ') scale(' . $scale . ')">';
                        $svg .= $logoSvgContent;
                        $svg .= '</g>';
                    } else {
                        $imageData = base64_encode(file_get_contents($logoPath));
                        $mimeType = mime_content_type($logoPath);
                        $svg .= '<image x="' . $logoX . '" y="' . $logoY . '" width="' . $logoSize . '" height="' . $logoSize . '" preserveAspectRatio="xMidYMid meet" xlink:href="data:' . $mimeType . ';base64,' . $imageData . '"/>';
                    }
                    
                    $svg .= '</g>';
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to embed logo in student ID card', ['error' => $e->getMessage()]);
            }
        }

        // Header text (centered at top, on top of logo)
        $headerY = 18;
        $svg .= '<text x="50%" y="' . $headerY . '" font-family="' . $template->font_family . '" font-size="' . ($template->layout_settings['header_font_size'] ?? 16) . '" font-weight="' . ($template->layout_settings['header_font_weight'] ?? 'bold') . '" fill="' . $template->header_color . '" text-anchor="middle">' . htmlspecialchars($template->header_text) . '</text>';

        // Content starts below header
        $contentStartY = $headerY + 12;
        
        // Photo and Info Section
        $photoSize = $template->include_photo ? ($template->photo_size * 0.8) : 0; // Reduced by 20%
        $photoX = $margin;
        $photoY = $contentStartY;

        // Student Photo (placeholder)
        if ($template->include_photo) {
            $svg .= '<rect x="' . $photoX . '" y="' . $photoY . '" width="' . $photoSize . '" height="' . $photoSize . '" fill="#f3f4f6" stroke="#d1d5db" stroke-width="1" rx="3"/>';
            $svg .= '<text x="' . ($photoX + $photoSize/2) . '" y="' . ($photoY + $photoSize/2 + 3) . '" font-family="Arial" font-size="8" fill="#6b7280" text-anchor="middle">Photo</text>';
        }

        // Student Information - on the right side of photo
        $infoX = $photoX + $photoSize + 10;
        $infoY = $photoY + 10;
        $infoMaxWidth = $width - $infoX - $margin;
        $lineHeight = ($template->layout_settings['field_spacing'] ?? 5) + ($template->font_size * 0.9);
        $infoFontSize = $template->font_size * 0.9; // Slightly smaller

        foreach ($template->fields_to_display as $field) {
            $value = $this->getFieldValue($student, $field);
            if ($value) {
                $label = ucfirst(str_replace('_', ' ', $field));
                $svg .= '<text x="' . $infoX . '" y="' . $infoY . '" font-family="' . $template->font_family . '" font-size="' . $infoFontSize . '" fill="' . $template->text_color . '"><tspan font-weight="600">' . htmlspecialchars($label) . ':</tspan> ' . htmlspecialchars($value) . '</text>';
                $infoY += $lineHeight;
            }
        }

        // Bottom section with QR, dates, and signatures
        $bottomY = max($photoY + $photoSize + 8, $infoY + 5);
        
        // Left side: Dates and Authority Signature
        $leftColumnX = $margin;
        $leftColumnY = $bottomY;
        $leftFontSize = 8;
        
        // Issue Date
        $issueDate = now()->format('d/m/Y');
        $svg .= '<text x="' . $leftColumnX . '" y="' . $leftColumnY . '" font-family="' . $template->font_family . '" font-size="' . $leftFontSize . '" fill="' . $template->text_color . '"><tspan font-weight="600">Issued:</tspan> ' . $issueDate . '</text>';
        $leftColumnY += 10;
        
        // Expiry Date (1 year from now)
        $expiryDate = now()->addYear()->format('d/m/Y');
        $svg .= '<text x="' . $leftColumnX . '" y="' . $leftColumnY . '" font-family="' . $template->font_family . '" font-size="' . $leftFontSize . '" fill="' . $template->text_color . '"><tspan font-weight="600">Expires:</tspan> ' . $expiryDate . '</text>';
        $leftColumnY += 12;
        
        // Authority Signature Line
        $sigWidth = 70;
        $svg .= '<line x1="' . $leftColumnX . '" y1="' . $leftColumnY . '" x2="' . ($leftColumnX + $sigWidth) . '" y2="' . $leftColumnY . '" stroke="' . $template->text_color . '" stroke-width="0.5"/>';
        $svg .= '<text x="' . ($leftColumnX + $sigWidth/2) . '" y="' . ($leftColumnY + 8) . '" font-family="' . $template->font_family . '" font-size="6" fill="' . $template->text_color . '" text-anchor="middle">Authorized Signature</text>';

        // Center: QR Code
        $qrSize = $template->qr_code_size * 0.7; // Reduced by 30%
        if ($template->include_qr_code) {
            $qrCodeSvgSmall = QrCode::format('svg')->size($qrSize)->generate($qrData);
            $qrX = ($width - $qrSize) / 2;
            $qrY = $bottomY - 5;
            
            $svg .= '<g transform="translate(' . $qrX . ',' . $qrY . ')">';
            $svg .= $qrCodeSvgSmall;
            $svg .= '</g>';
        }

        // Right side: Student/Guardian Signature
        $rightColumnX = $width - $margin - $sigWidth;
        $rightColumnY = $bottomY + 27;
        
        $svg .= '<line x1="' . $rightColumnX . '" y1="' . $rightColumnY . '" x2="' . ($rightColumnX + $sigWidth) . '" y2="' . $rightColumnY . '" stroke="' . $template->text_color . '" stroke-width="0.5"/>';
        $svg .= '<text x="' . ($rightColumnX + $sigWidth/2) . '" y="' . ($rightColumnY + 8) . '" font-family="' . $template->font_family . '" font-size="6" fill="' . $template->text_color . '" text-anchor="middle">Student Signature</text>';

        $svg .= '</svg>';

        return $svg;
    }

    private function getFieldValue(Student $student, string $field): ?string
    {
        return match ($field) {
            'name' => $student->name,
            'admission_no', 'admission_number' => $student->admission_no,
            'email' => $student->email,
            'dob', 'date_of_birth' => $student->dob?->format('M d, Y'),
            default => null,
        };
    }
}
