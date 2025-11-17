<?php

namespace App\Services\Attendance;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
// use Intervention\Image\Facades\Image; // TODO: Install intervention/image package

/**
 * Service for optical mark recognition (OMR) and document scanning
 * Processes bubble sheets and scanned attendance forms
 *
 * Requires: composer require intervention/image
 */
class OpticalScannerService
{
    protected $sensitivity;
    protected $templatePath;

    public function __construct(int $sensitivity = 70, ?string $templatePath = null)
    {
        $this->sensitivity = $sensitivity;
        $this->templatePath = $templatePath;
    }

    /**
     * Process a scanned attendance sheet
     */
    public function processSheet(string $imagePath): array
    {
        // TODO: Install intervention/image before using this method
        // composer require intervention/image
        throw new \Exception('Optical scanning requires intervention/image package. Run: composer require intervention/image');

        try {
            // $image = Image::make($imagePath);

            // Convert to grayscale for better processing
            $image->greyscale();

            // Enhance contrast
            $image->contrast(30);

            // Detect marked bubbles/checkboxes
            $marks = $this->detectMarks($image);

            // Parse marks into attendance data
            return $this->parseMarks($marks);
        } catch (\Exception $e) {
            Log::error('Optical scanning failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Detect marked areas in the image
     */
    protected function detectMarks($image): array
    {
        $marks = [];
        $width = $image->width();
        $height = $image->height();

        // Define grid based on template
        // This is a simplified example - actual implementation would use template matching
        $rows = 50; // Number of students
        $cols = 4;  // Present, Absent, Late, Excused

        $cellWidth = $width / $cols;
        $cellHeight = $height / $rows;

        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $x = $col * $cellWidth + ($cellWidth / 2);
                $y = $row * $cellHeight + ($cellHeight / 2);

                // Check if area is marked (dark)
                $pixel = $image->pickColor($x, $y, 'int');
                $brightness = ($pixel[0] + $pixel[1] + $pixel[2]) / 3;

                // If brightness is below threshold, it's marked
                if ($brightness < $this->sensitivity) {
                    $marks[] = [
                        'row' => $row,
                        'col' => $col,
                        'brightness' => $brightness,
                    ];
                }
            }
        }

        return $marks;
    }

    /**
     * Parse detected marks into attendance records
     */
    protected function parseMarks(array $marks): array
    {
        $attendance = [];
        $statusMap = ['present', 'absent', 'late', 'excused'];

        foreach ($marks as $mark) {
            $studentIndex = $mark['row'];
            $status = $statusMap[$mark['col']] ?? 'present';

            $attendance[$studentIndex] = [
                'status' => $status,
                'confidence' => 100 - $mark['brightness'],
            ];
        }

        return $attendance;
    }

    /**
     * Generate a blank attendance sheet template
     */
    public function generateTemplate(array $students, string $date, string $className): string
    {
        $html = view('attendance.optical-template', [
            'students' => $students,
            'date' => $date,
            'class' => $className,
        ])->render();

        // Convert HTML to PDF
        // Requires: composer require barryvdh/laravel-dompdf
        $pdf = \PDF::loadHTML($html);

        $filename = "attendance-sheet-{$className}-" . date('Y-m-d') . ".pdf";
        $path = storage_path("app/templates/{$filename}");

        $pdf->save($path);

        return $path;
    }

    /**
     * Validate scanned image quality
     */
    public function validateImage(string $imagePath): array
    {
        // TODO: Install intervention/image before using this method
        throw new \Exception('Optical scanning requires intervention/image package. Run: composer require intervention/image');

        try {
            // $image = Image::make($imagePath);

            $validation = [
                'valid' => true,
                'errors' => [],
                'warnings' => [],
            ];

            // Check minimum resolution
            if ($image->width() < 1200 || $image->height() < 1600) {
                $validation['errors'][] = 'Image resolution too low. Minimum 1200x1600 required.';
                $validation['valid'] = false;
            }

            // Check if image is too dark or too bright
            $histogram = $image->histogram();
            $avgBrightness = array_sum($histogram) / count($histogram);

            if ($avgBrightness < 30) {
                $validation['warnings'][] = 'Image is too dark. Results may be inaccurate.';
            } elseif ($avgBrightness > 225) {
                $validation['warnings'][] = 'Image is too bright. Results may be inaccurate.';
            }

            // Check aspect ratio
            $ratio = $image->width() / $image->height();
            if ($ratio < 0.7 || $ratio > 0.9) {
                $validation['warnings'][] = 'Image aspect ratio unusual. Ensure proper scanning.';
            }

            return $validation;
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'errors' => ['Failed to validate image: ' . $e->getMessage()],
                'warnings' => [],
            ];
        }
    }

    /**
     * Batch process multiple scanned sheets
     */
    public function batchProcess(array $imagePaths): array
    {
        $results = [];

        foreach ($imagePaths as $path) {
            $results[] = [
                'path' => $path,
                'data' => $this->processSheet($path),
                'validation' => $this->validateImage($path),
            ];
        }

        return $results;
    }
}
