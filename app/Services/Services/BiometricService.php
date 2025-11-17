<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class BiometricService
{
    protected $deviceConnected = false;
    protected $deviceType = null;

    /**
     * Check biometric device status
     */
    public function checkDeviceStatus(string $type = 'fingerprint'): array
    {
        // In production, this would check actual hardware connection
        // For now, simulate device status
        $simulateConnection = config('skolaris.biometric_enabled', false);

        return [
            'connected' => $simulateConnection,
            'type' => $type,
            'device_name' => $simulateConnection ? $this->getDeviceName($type) : null,
            'message' => $simulateConnection 
                ? ucfirst($type) . ' device is connected and ready' 
                : ucfirst($type) . ' device not detected. Please check connection.',
        ];
    }

    /**
     * Verify biometric data and identify student
     */
    public function verify(string $biometricData, string $type = 'fingerprint'): array
    {
        try {
            // In production, this would:
            // 1. Connect to biometric device SDK
            // 2. Process the biometric template
            // 3. Match against database
            // 4. Return student ID if match found

            // For now, decode the simulated data
            // Format expected: "biometric_fingerprint_12345" or "biometric_iris_12345"
            if (!str_starts_with($biometricData, 'biometric_')) {
                return [
                    'success' => false,
                    'message' => 'Invalid biometric data format',
                ];
            }

            $parts = explode('_', $biometricData);
            if (count($parts) < 3) {
                return [
                    'success' => false,
                    'message' => 'Invalid biometric data structure',
                ];
            }

            $dataType = $parts[1]; // fingerprint or iris
            $studentId = (int)$parts[2];

            if ($dataType !== $type) {
                return [
                    'success' => false,
                    'message' => 'Biometric type mismatch',
                ];
            }

            // Verify student exists
            $student = User::find($studentId);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Student not found in database',
                ];
            }

            // Check if student has biometric data registered
            if (!$this->hasRegisteredBiometric($student, $type)) {
                return [
                    'success' => false,
                    'message' => 'No ' . $type . ' data registered for this student',
                ];
            }

            Log::info('Biometric verification successful', [
                'type' => $type,
                'student_id' => $studentId,
                'student_name' => $student->name,
            ]);

            return [
                'success' => true,
                'student_id' => $studentId,
                'message' => 'Biometric verified successfully',
                'confidence' => 98.5, // In production, this comes from the biometric device
            ];

        } catch (\Exception $e) {
            Log::error('Biometric verification failed', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Register biometric data for a student
     */
    public function register(int $studentId, string $biometricData, string $type = 'fingerprint'): array
    {
        try {
            $student = User::findOrFail($studentId);

            // In production, this would:
            // 1. Capture multiple samples
            // 2. Create biometric template
            // 3. Store in secure biometric database
            // 4. Link to student record

            // For now, store a reference in user metadata
            $biometricField = $type . '_registered';
            $student->update([
                'biometric_data' => json_encode([
                    $type => [
                        'registered' => true,
                        'registered_at' => now(),
                        'template_hash' => hash('sha256', $biometricData),
                    ]
                ])
            ]);

            Log::info('Biometric registration successful', [
                'type' => $type,
                'student_id' => $studentId,
            ]);

            return [
                'success' => true,
                'message' => ucfirst($type) . ' registered successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Biometric registration failed', [
                'type' => $type,
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if student has registered biometric data
     */
    protected function hasRegisteredBiometric(User $student, string $type): bool
    {
        // In production, check actual biometric database
        // For now, check user metadata
        if (!$student->biometric_data) {
            return false;
        }

        $data = json_decode($student->biometric_data, true);
        return isset($data[$type]['registered']) && $data[$type]['registered'] === true;
    }

    /**
     * Get device name based on type
     */
    protected function getDeviceName(string $type): string
    {
        $devices = [
            'fingerprint' => 'SecuGen Hamster Pro 20',
            'iris' => 'IriTech IriShield MK 2120U',
        ];

        return $devices[$type] ?? 'Unknown Device';
    }

    /**
     * Test device connection
     */
    public function testConnection(string $type = 'fingerprint'): array
    {
        try {
            // In production, this would ping the actual device
            $status = $this->checkDeviceStatus($type);

            return [
                'success' => $status['connected'],
                'message' => $status['message'],
                'device_info' => [
                    'type' => $type,
                    'name' => $status['device_name'],
                    'firmware_version' => '2.4.1', // Would come from device
                    'sdk_version' => '3.1.5', // Would come from SDK
                ],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get supported biometric types
     */
    public function getSupportedTypes(): array
    {
        return [
            [
                'type' => 'fingerprint',
                'name' => 'Fingerprint',
                'icon' => 'bi-fingerprint',
                'description' => 'Fast and reliable fingerprint scanning',
                'enabled' => config('skolaris.biometric_fingerprint_enabled', false),
            ],
            [
                'type' => 'iris',
                'name' => 'Iris Recognition',
                'icon' => 'bi-eye',
                'description' => 'High-security iris scanning',
                'enabled' => config('skolaris.biometric_iris_enabled', false),
            ],
        ];
    }
}
