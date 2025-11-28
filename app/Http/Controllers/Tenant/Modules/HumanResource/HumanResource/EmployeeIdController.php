<?php

namespace App\Http\Controllers\Tenant\Modules\HumanResource;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeIdSetting;
use App\Services\QrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class EmployeeIdController extends Controller
{
    public function __construct(private QrCodeGenerator $qrCodes)
    {
    }

    public function index(): View
    {
        $employees = Employee::with(['department', 'position'])
            ->where('employment_status', 'active')
            ->get();

        $templates = EmployeeIdSetting::getActive();

        return view('tenant.modules.human_resource.employee_ids.index', compact('employees', 'templates'));
    }

    public function generate(Request $request): View
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'required|exists:employee_id_settings,id',
        ]);

        $employee = Employee::with(['department', 'position', 'salaryScale'])
            ->findOrFail($request->employee_id);

        $template = EmployeeIdSetting::findOrFail($request->template_id);

        // Generate QR code data
        $qrData = $this->generateQrData($employee);

        // Generate SVG content
        $svgContent = $this->generateSvg($employee, $template);

        return view('tenant.modules.human_resource.employee_ids.generate', compact('employee', 'template', 'qrData', 'svgContent'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'required|exists:employee_id_settings,id',
        ]);

        $employee = Employee::with(['department', 'position', 'salaryScale'])
            ->findOrFail($request->employee_id);

        $template = EmployeeIdSetting::findOrFail($request->template_id);

        // Generate QR code data
        $qrData = $this->generateQrData($employee);

        // Generate SVG content
        $svgContent = $this->generateSvg($employee, $template);

        return view('tenant.modules.human_resource.employee_ids.preview', compact('employee', 'template', 'qrData', 'svgContent'));
    }

    public function downloadSvg(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'required|exists:employee_id_settings,id',
        ]);

        $employee = Employee::with(['department', 'position', 'salaryScale'])
            ->findOrFail($request->employee_id);

        $template = EmployeeIdSetting::findOrFail($request->template_id);

        $svgContent = $this->generateSvg($employee, $template);

        $filename = 'employee_id_' . $employee->id . '_' . now()->format('Y-m-d') . '.svg';

        return response($svgContent)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadPng(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'template_id' => 'required|exists:employee_id_settings,id',
        ]);

        $employee = Employee::with(['department', 'position', 'salaryScale'])
            ->findOrFail($request->employee_id);

        $template = EmployeeIdSetting::findOrFail($request->template_id);

        // For PNG generation, we'll use a library or convert from SVG
        // For now, return a placeholder response
        $filename = 'employee_id_' . $employee->id . '_' . now()->format('Y-m-d') . '.png';

        // This would need additional setup with a library like Intervention Image
        // For now, we'll return the SVG as PNG (browsers can handle SVG)
        $svgContent = $this->generateSvg($employee, $template);

        return response($svgContent)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateQrData(Employee $employee): string
    {
        $data = [
            'id' => $employee->id,
            'employee_number' => $employee->employee_number,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'national_id' => $employee->national_id,
            'department' => $employee->department?->name,
            'position' => $employee->position?->name,
            'employee_type' => $employee->employee_type,
            'hire_date' => $employee->hire_date?->format('Y-m-d'),
            'birth_date' => $employee->birth_date?->format('Y-m-d'),
            'employment_status' => $employee->employment_status,
            'salary_scale' => $employee->salaryScale?->name,
            'issued_at' => now()->format('Y-m-d'),
            'expires_at' => now()->addYear()->format('Y-m-d'),
            'school' => tenant('name'),
            'generated_at' => now()->toISOString(),
        ];

        return json_encode($data);
    }

    private function generateSvg(Employee $employee, EmployeeIdSetting $template): string
    {
        $width = $template->card_width * 3.779527559; // Convert mm to pixels (96 DPI)
        $height = $template->card_height * 3.779527559;

        $qrData = $this->generateQrData($employee);
        $qrCodeSvg = $this->qrCodes->svg($qrData, (int) $template->qr_code_size);

        $svg = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg width="' . $width . '" height="' . $height . '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">';

        // Background
        $svg .= '<rect width="100%" height="100%" fill="' . $template->background_color . '" rx="' . ($template->layout_settings['border_radius'] ?? 8) . '" ry="' . ($template->layout_settings['border_radius'] ?? 8) . '" stroke="' . ($template->layout_settings['border_color'] ?? '#e5e7eb') . '" stroke-width="' . ($template->layout_settings['border_width'] ?? 2) . '"/>';

        $margin = 15;
        
        // School Logo as watermark (centered, semi-transparent, behind everything)
        $logoSize = 80;
        $logoUrl = function_exists('school_logo') ? \school_logo() : null;
        
        if ($logoUrl) {
            try {
                $logoPath = function_exists('school_logo_path') ? \school_logo_path() : null;
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
                \Log::warning('Failed to embed logo in employee ID card', ['error' => $e->getMessage()]);
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

        // Employee Photo
        if ($template->include_photo) {
            $photoEmbedded = false;
            
            // Try to embed actual employee photo
            if ($employee->photo_path) {
                try {
                    $photoFullPath = storage_path('app/public/' . $employee->photo_path);
                    if (file_exists($photoFullPath)) {
                        $photoData = base64_encode(file_get_contents($photoFullPath));
                        $photoMimeType = mime_content_type($photoFullPath);
                        
                        // Add clipping path for rounded photo
                        $clipId = 'photo-clip-' . $employee->id;
                        $svg .= '<defs><clipPath id="' . $clipId . '"><rect x="' . $photoX . '" y="' . $photoY . '" width="' . $photoSize . '" height="' . $photoSize . '" rx="3"/></clipPath></defs>';
                        
                        // Photo with border
                        $svg .= '<rect x="' . $photoX . '" y="' . $photoY . '" width="' . $photoSize . '" height="' . $photoSize . '" fill="none" stroke="#d1d5db" stroke-width="1" rx="3"/>';
                        $svg .= '<image x="' . $photoX . '" y="' . $photoY . '" width="' . $photoSize . '" height="' . $photoSize . '" preserveAspectRatio="xMidYMid slice" clip-path="url(#' . $clipId . ')" xlink:href="data:' . $photoMimeType . ';base64,' . $photoData . '"/>';
                        
                        $photoEmbedded = true;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to embed employee photo in ID card', ['error' => $e->getMessage()]);
                }
            }
            
            // Fallback to placeholder with initials
            if (!$photoEmbedded) {
                $initials = strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1));
                $svg .= '<rect x="' . $photoX . '" y="' . $photoY . '" width="' . $photoSize . '" height="' . $photoSize . '" fill="#e5e7eb" stroke="#d1d5db" stroke-width="1" rx="3"/>';
                $svg .= '<text x="' . ($photoX + $photoSize/2) . '" y="' . ($photoY + $photoSize/2 + 8) . '" font-family="Arial" font-size="' . ($photoSize * 0.35) . '" font-weight="600" fill="#6b7280" text-anchor="middle">' . $initials . '</text>';
            }
        }

        // Employee Information - on the right side of photo
        $infoX = $photoX + $photoSize + 10;
        $infoY = $photoY + 10;
        $infoMaxWidth = $width - $infoX - $margin;
        $lineHeight = ($template->layout_settings['field_spacing'] ?? 5) + ($template->font_size * 0.9);
        $infoFontSize = $template->font_size * 0.9; // Slightly smaller

        foreach ($template->fields_to_display as $field) {
            $value = $this->getFieldValue($employee, $field);
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
            $qrCodeSvgSmall = $this->qrCodes->svg($qrData, (int) $qrSize);
            $qrX = ($width - $qrSize) / 2;
            $qrY = $bottomY - 8;

           // $qrX = ($width - $qrSize) / 2;
            //$qrY = $bottomY - 5;
            
            $svg .= '<g transform="translate(' . $qrX . ',' . $qrY . ')">';
            $svg .= $qrCodeSvgSmall;
            $svg .= '</g>';
        }

        // Right side: Employee Signature
        $rightColumnX = $width - $margin - $sigWidth;
        $rightColumnY = $bottomY + 27;
        
        $svg .= '<line x1="' . $rightColumnX . '" y1="' . $rightColumnY . '" x2="' . ($rightColumnX + $sigWidth) . '" y2="' . $rightColumnY . '" stroke="' . $template->text_color . '" stroke-width="0.5"/>';
        $svg .= '<text x="' . ($rightColumnX + $sigWidth/2) . '" y="' . ($rightColumnY + 8) . '" font-family="' . $template->font_family . '" font-size="6" fill="' . $template->text_color . '" text-anchor="middle">Employee Signature</text>';

        $svg .= '</svg>';

        return $svg;
    }

    private function getFieldValue(Employee $employee, string $field): ?string
    {
        return match ($field) {
            'name', 'full_name' => $employee->first_name . ' ' . $employee->last_name,
            'employee_number' => $employee->employee_number ?? 'N/A',
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'department' => $employee->department?->name,
            'position' => $employee->position?->name,
            'hire_date' => $employee->hire_date?->format('M d, Y'),
            'birth_date' => $employee->birth_date?->format('M d, Y'),
            'employment_status' => ucfirst($employee->employment_status),
            default => null,
        };
    }
}
