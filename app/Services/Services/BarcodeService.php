<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BarcodeService
{
    /**
     * Decode barcode and identify student
     */
    public function decode(string $barcode): array
    {
        try {
            // Validate barcode format
            if (empty($barcode)) {
                return [
                    'success' => false,
                    'message' => 'Barcode data is empty',
                ];
            }

            // Parse barcode - multiple formats supported:
            // 1. Direct student ID: "STU-12345"
            // 2. QR code JSON: {"type":"student_id","id":12345}
            // 3. Simple numeric: "12345"
            
            $studentId = null;

            // Try JSON format (QR code)
            if (str_starts_with($barcode, '{')) {
                $data = json_decode($barcode, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($data['id'])) {
                    $studentId = (int)$data['id'];
                }
            }
            // Try STU-xxxxx format
            elseif (str_starts_with($barcode, 'STU-')) {
                $studentId = (int)str_replace('STU-', '', $barcode);
            }
            // Try direct numeric
            elseif (is_numeric($barcode)) {
                $studentId = (int)$barcode;
            }
            // Try UUID or admission number
            else {
                $student = User::where('admission_number', $barcode)
                    ->orWhere('student_id', $barcode)
                    ->first();
                
                if ($student) {
                    $studentId = $student->id;
                }
            }

            if (!$studentId) {
                return [
                    'success' => false,
                    'message' => 'Invalid barcode format or student not found',
                ];
            }

            // Verify student exists
            $student = User::find($studentId);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Student ID ' . $studentId . ' not found in database',
                ];
            }

            // Verify student has active status
            if (isset($student->status) && $student->status !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Student account is not active',
                ];
            }

            Log::info('Barcode scan successful', [
                'barcode' => $barcode,
                'student_id' => $studentId,
                'student_name' => $student->name,
            ]);

            return [
                'success' => true,
                'student_id' => $studentId,
                'message' => 'Barcode decoded successfully',
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'admission_number' => $student->admission_number ?? null,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Barcode decode failed', [
                'barcode' => $barcode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Barcode processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate barcode for a student
     */
    public function generate(int $studentId, string $format = 'qrcode'): array
    {
        try {
            $student = User::findOrFail($studentId);

            $barcodeData = null;

            switch ($format) {
                case 'qrcode':
                    // QR code with JSON data
                    $barcodeData = json_encode([
                        'type' => 'student_id',
                        'id' => $student->id,
                        'name' => $student->name,
                        'admission_number' => $student->admission_number ?? null,
                        'generated_at' => now()->toIso8601String(),
                    ]);
                    break;

                case 'barcode':
                    // Simple barcode with STU- prefix
                    $barcodeData = 'STU-' . str_pad($student->id, 6, '0', STR_PAD_LEFT);
                    break;

                case 'numeric':
                    // Plain numeric ID
                    $barcodeData = (string)$student->id;
                    break;

                default:
                    throw new \InvalidArgumentException('Unsupported barcode format');
            }

            Log::info('Barcode generated', [
                'student_id' => $studentId,
                'format' => $format,
            ]);

            return [
                'success' => true,
                'barcode_data' => $barcodeData,
                'format' => $format,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Barcode generation failed', [
                'student_id' => $studentId,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Barcode generation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate QR code image URL
     */
    public function generateQRCodeUrl(string $data, int $size = 200): string
    {
        // Using Google Charts API (free, no API key needed)
        // In production, consider using a package like SimpleSoftwareIO/simple-qrcode
        $encodedData = urlencode($data);
        return "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$encodedData}&choe=UTF-8";
    }

    /**
     * Validate barcode format
     */
    public function validateFormat(string $barcode): bool
    {
        // Check if barcode matches any supported format
        if (empty($barcode)) {
            return false;
        }

        // JSON format
        if (str_starts_with($barcode, '{')) {
            $data = json_decode($barcode, true);
            return json_last_error() === JSON_ERROR_NONE && isset($data['id']);
        }

        // STU- format
        if (str_starts_with($barcode, 'STU-')) {
            $id = str_replace('STU-', '', $barcode);
            return is_numeric($id);
        }

        // Numeric format
        if (is_numeric($barcode)) {
            return true;
        }

        // Admission number or UUID format (at least 5 chars, alphanumeric)
        if (strlen($barcode) >= 5 && preg_match('/^[A-Za-z0-9-]+$/', $barcode)) {
            return true;
        }

        return false;
    }

    /**
     * Bulk generate barcodes for multiple students
     */
    public function bulkGenerate(array $studentIds, string $format = 'qrcode'): array
    {
        $results = [];
        $successful = 0;
        $failed = 0;

        foreach ($studentIds as $studentId) {
            $result = $this->generate($studentId, $format);
            $results[] = $result;
            
            if ($result['success']) {
                $successful++;
            } else {
                $failed++;
            }
        }

        return [
            'total' => count($studentIds),
            'successful' => $successful,
            'failed' => $failed,
            'results' => $results,
        ];
    }

    /**
     * Get supported barcode formats
     */
    public function getSupportedFormats(): array
    {
        return [
            [
                'format' => 'qrcode',
                'name' => 'QR Code',
                'description' => 'Two-dimensional QR code with encrypted data',
                'icon' => 'bi-qr-code',
                'recommended' => true,
            ],
            [
                'format' => 'barcode',
                'name' => 'Linear Barcode',
                'description' => 'Standard 1D barcode (Code 128)',
                'icon' => 'bi-upc-scan',
                'recommended' => false,
            ],
            [
                'format' => 'numeric',
                'name' => 'Numeric Code',
                'description' => 'Simple numeric student ID',
                'icon' => 'bi-123',
                'recommended' => false,
            ],
        ];
    }

    /**
     * Test barcode scanner
     */
    public function testScanner(string $testBarcode = 'STU-000001'): array
    {
        try {
            $isValid = $this->validateFormat($testBarcode);
            
            if (!$isValid) {
                return [
                    'success' => false,
                    'message' => 'Test barcode has invalid format',
                ];
            }

            $result = $this->decode($testBarcode);

            return [
                'success' => true,
                'message' => 'Scanner test completed',
                'test_barcode' => $testBarcode,
                'decode_result' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Scanner test failed: ' . $e->getMessage(),
            ];
        }
    }
}
