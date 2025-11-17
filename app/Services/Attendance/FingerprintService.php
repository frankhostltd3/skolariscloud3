<?php

namespace App\Services\Attendance;

use App\Models\BiometricTemplate;
use App\Models\AttendanceSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for fingerprint scanner integration
 * Supports multiple biometric devices: ZKTeco, Morpho, Suprema, etc.
 */
class FingerprintService
{
    protected $settings;
    protected $deviceIp;
    protected $devicePort;
    protected $timeout;

    public function __construct(AttendanceSetting $settings)
    {
        $this->settings = $settings;
        $this->deviceIp = $settings->fingerprint_device_ip;
        $this->devicePort = $settings->fingerprint_device_port ?? 4370;
        $this->timeout = $settings->fingerprint_timeout ?? 30;
    }

    /**
     * Connect to fingerprint device
     */
    public function connect(): bool
    {
        try {
            $deviceType = $this->settings->fingerprint_device_type;

            switch ($deviceType) {
                case 'zkteco':
                    return $this->connectZKTeco();
                case 'morpho':
                    return $this->connectMorpho();
                case 'suprema':
                    return $this->connectSuprema();
                default:
                    return $this->connectGeneric();
            }
        } catch (\Exception $e) {
            Log::error('Fingerprint device connection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enroll a new fingerprint
     */
    public function enroll(string $userType, int $userId, int $fingerPosition): ?array
    {
        try {
            // Request device to start enrollment
            $response = Http::timeout($this->timeout)
                ->post("http://{$this->deviceIp}:{$this->devicePort}/enroll", [
                    'user_type' => $userType,
                    'user_id' => $userId,
                    'finger_position' => $fingerPosition,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'template' => $data['template'] ?? null,
                    'quality' => $data['quality_score'] ?? 0,
                    'device_id' => $data['device_id'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Fingerprint enrollment failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify fingerprint against stored template
     */
    public function verify(string $templateData, string $scannedData): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("http://{$this->deviceIp}:{$this->devicePort}/verify", [
                    'template' => $templateData,
                    'scanned' => $scannedData,
                    'threshold' => $this->settings->fingerprint_threshold,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'matched' => $data['matched'] ?? false,
                    'score' => $data['score'] ?? 0,
                    'confidence' => $data['confidence'] ?? 0,
                ];
            }

            return ['matched' => false, 'score' => 0, 'confidence' => 0];
        } catch (\Exception $e) {
            Log::error('Fingerprint verification failed: ' . $e->getMessage());
            return ['matched' => false, 'score' => 0, 'confidence' => 0];
        }
    }

    /**
     * Identify user from fingerprint scan (1:N matching)
     */
    public function identify(string $scannedData): ?BiometricTemplate
    {
        try {
            // Get all active fingerprint templates
            $templates = BiometricTemplate::active()
                ->byType('fingerprint')
                ->get();

            $bestMatch = null;
            $highestScore = 0;

            foreach ($templates as $template) {
                $result = $this->verify($template->template_data, $scannedData);

                if ($result['matched'] && $result['score'] > $highestScore) {
                    $highestScore = $result['score'];
                    $bestMatch = $template;
                }
            }

            return $bestMatch;
        } catch (\Exception $e) {
            Log::error('Fingerprint identification failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ZKTeco device connection
     */
    protected function connectZKTeco(): bool
    {
        // ZKTeco SDK integration logic
        // This would use the ZKTECO SDK library
        return false; // Placeholder
    }

    /**
     * Morpho device connection
     */
    protected function connectMorpho(): bool
    {
        // Morpho SDK integration logic
        return false; // Placeholder
    }

    /**
     * Suprema device connection
     */
    protected function connectSuprema(): bool
    {
        // Suprema SDK integration logic
        return false; // Placeholder
    }

    /**
     * Generic HTTP-based biometric device
     */
    protected function connectGeneric(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("http://{$this->deviceIp}:{$this->devicePort}/status");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get device status
     */
    public function getDeviceStatus(): array
    {
        try {
            $response = Http::timeout(5)
                ->get("http://{$this->deviceIp}:{$this->devicePort}/status");

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'offline', 'message' => 'Device not responding'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
