<?php

namespace App\Services\Attendance;

use App\Models\Student;
use App\Models\Employee;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Service for generating and managing QR codes and barcodes for attendance
 *
 * Uses BaconQrCode (already installed via Laravel Fortify)
 */
class BarcodeService
{
    /**
     * Generate a unique code for a user
     */
    public function generateCode(string $userType, int $userId, ?string $prefix = null): string
    {
        $prefix = $prefix ?? config('app.name', 'SCHOOL');
        $timestamp = now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$userType}-{$userId}-{$timestamp}-{$random}";
    }

    /**
     * Generate QR code image using BaconQrCode
     */
    public function generateQR(string $code, int $size = 200): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($code);
    }

    /**
     * Generate barcode image (Code 128)
     * Note: For now returns QR code. For true barcode, install picqer/php-barcode-generator
     */
    public function generateBarcode(string $code, int $width = 2, int $height = 50): string
    {
        // For simple implementation, use QR code as fallback
        return $this->generateQR($code, 200);
    }

    /**
     * Validate and parse a scanned code
     */
    public function parseCode(string $code): ?array
    {
        $parts = explode('-', $code);

        if (count($parts) < 3) {
            return null;
        }

        return [
            'prefix' => $parts[0] ?? null,
            'user_type' => $parts[1] ?? null,
            'user_id' => (int)($parts[2] ?? 0),
            'timestamp' => $parts[3] ?? null,
            'random' => $parts[4] ?? null,
            'full_code' => $code,
        ];
    }

    /**
     * Generate student QR/Barcode
     */
    public function generateForStudent(Student $student, string $format = 'qr', int $size = 200)
    {
        $code = $this->generateCode('STU', $student->id, $student->school->code ?? null);

        if ($format === 'barcode') {
            return [
                'code' => $code,
                'image' => $this->generateBarcode($code),
                'format' => 'barcode',
            ];
        }

        return [
            'code' => $code,
            'image' => $this->generateQR($code, $size),
            'format' => 'qr',
        ];
    }

    /**
     * Generate staff QR/Barcode
     */
    public function generateForStaff(Employee $employee, string $format = 'qr', int $size = 200)
    {
        $code = $this->generateCode('EMP', $employee->id, $employee->school->code ?? null);

        if ($format === 'barcode') {
            return [
                'code' => $code,
                'image' => $this->generateBarcode($code),
                'format' => 'barcode',
            ];
        }

        return [
            'code' => $code,
            'image' => $this->generateQR($code, $size),
            'format' => 'qr',
        ];
    }

    /**
     * Verify a code belongs to a user
     */
    public function verifyCode(string $code, string $userType, int $userId): bool
    {
        $parsed = $this->parseCode($code);

        if (!$parsed) {
            return false;
        }

        return $parsed['user_type'] === $userType && $parsed['user_id'] === $userId;
    }
}
