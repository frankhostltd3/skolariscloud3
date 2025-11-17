<?php

namespace App\Services\Attendance;

use App\Models\Student;
use App\Models\Employee as Staff;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\StaffAttendance;
use App\Models\AttendanceSetting;
use App\Models\BiometricTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Unified service for recording attendance from any method
 * Handles: Manual, QR/Barcode, Fingerprint, Optical scanning
 */
class AttendanceRecordingService
{
    protected $schoolId;
    protected $settings;
    protected $barcodeService;
    protected $fingerprintService;
    protected $opticalService;

    public function __construct(int $schoolId)
    {
        $this->schoolId = $schoolId;
        $this->settings = AttendanceSetting::getOrCreateForSchool($schoolId);
        $this->barcodeService = new BarcodeService();
        $this->fingerprintService = new FingerprintService($this->settings);
        $this->opticalService = new OpticalScannerService();
    }

    /**
     * Record attendance using any method
     *
     * @param string $method One of: manual, qr, barcode, fingerprint, optical
     * @param array $data Method-specific data
     * @return array Result with success status and message
     */
    public function record(string $method, array $data): array
    {
        // Validate method is enabled
        $userType = $data['user_type'] ?? 'student';

        if (!$this->settings->isMethodEnabled($method, $userType)) {
            return [
                'success' => false,
                'message' => "The {$method} method is not enabled for {$userType}s.",
            ];
        }

        // Route to appropriate handler
        return match($method) {
            'manual' => $this->recordManual($data),
            'qr', 'barcode' => $this->recordBarcode($data),
            'fingerprint' => $this->recordFingerprint($data),
            'optical' => $this->recordOptical($data),
            default => [
                'success' => false,
                'message' => 'Invalid attendance method.',
            ],
        };
    }

    /**
     * Record manual attendance entry
     */
    protected function recordManual(array $data): array
    {
        try {
            DB::beginTransaction();

            $userType = $data['user_type'] ?? 'student';

            if ($userType === 'student') {
                $record = AttendanceRecord::updateOrCreate(
                    [
                        'attendance_id' => $data['attendance_id'],
                        'student_id' => $data['student_id'],
                    ],
                    [
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? null,
                        'attendance_method' => 'manual',
                        'marked_by' => auth()->id(),
                        'marked_at' => now(),
                    ]
                );
            } else {
                $record = StaffAttendance::updateOrCreate(
                    [
                        'user_id' => $data['user_id'],
                        'date' => $data['date'] ?? today(),
                    ],
                    [
                        'status' => $data['status'],
                        'check_in_time' => $data['check_in_time'] ?? null,
                        'check_out_time' => $data['check_out_time'] ?? null,
                        'notes' => $data['notes'] ?? null,
                        'attendance_method' => 'manual',
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Attendance recorded successfully.',
                'record' => $record,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual attendance recording failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Record attendance via QR/Barcode scan
     */
    protected function recordBarcode(array $data): array
    {
        try {
            // Parse scanned code
            $parsed = $this->barcodeService->parseCode($data['code']);

            if (!$parsed) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired code.',
                ];
            }

            // Check for duplicate scan (within grace period)
            if ($this->isDuplicateScan($parsed['user_type'], $parsed['user_id'], 'qr')) {
                return [
                    'success' => false,
                    'message' => 'Already scanned within grace period.',
                ];
            }

            DB::beginTransaction();

            if ($parsed['user_type'] === 'student') {
                $record = AttendanceRecord::updateOrCreate(
                    [
                        'attendance_id' => $data['attendance_id'],
                        'student_id' => $parsed['user_id'],
                    ],
                    [
                        'status' => $data['status'] ?? 'present',
                        'attendance_method' => 'qr',
                        'scan_data' => json_encode(['code' => $data['code']]),
                        'marked_by' => auth()->id(),
                        'marked_at' => now(),
                    ]
                );
            } else {
                $record = StaffAttendance::updateOrCreate(
                    [
                        'user_id' => $parsed['user_id'],
                        'date' => today(),
                    ],
                    [
                        'status' => $data['status'] ?? 'present',
                        'check_in_time' => $data['check_in_time'] ?? now(),
                        'attendance_method' => 'qr',
                        'scan_data' => json_encode(['code' => $data['code']]),
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Attendance recorded via QR code.',
                'record' => $record,
                'user' => $parsed,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Barcode attendance recording failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Record attendance via fingerprint scan
     */
    protected function recordFingerprint(array $data): array
    {
        try {
            // Connect to device
            $deviceConfig = [
                'type' => $this->settings->fingerprint_device_type,
                'ip' => $this->settings->fingerprint_device_ip,
                'port' => $this->settings->fingerprint_device_port,
            ];

            $connected = $this->fingerprintService->connect($deviceConfig);

            if (!$connected['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to connect to fingerprint device.',
                ];
            }

            // Identify user from fingerprint
            $identified = $this->fingerprintService->identify(
                $data['fingerprint_template'],
                $data['user_type'] ?? 'student'
            );

            if (!$identified['success']) {
                return [
                    'success' => false,
                    'message' => 'Fingerprint not recognized.',
                ];
            }

            // Check quality score
            if ($identified['score'] < $this->settings->fingerprint_quality_threshold) {
                return [
                    'success' => false,
                    'message' => 'Fingerprint quality too low. Please try again.',
                ];
            }

            // Check for duplicate scan
            if ($this->isDuplicateScan($identified['user_type'], $identified['user_id'], 'fingerprint')) {
                return [
                    'success' => false,
                    'message' => 'Already scanned within grace period.',
                ];
            }

            DB::beginTransaction();

            if ($identified['user_type'] === 'student') {
                $record = AttendanceRecord::updateOrCreate(
                    [
                        'attendance_id' => $data['attendance_id'],
                        'student_id' => $identified['user_id'],
                    ],
                    [
                        'status' => $data['status'] ?? 'present',
                        'attendance_method' => 'fingerprint',
                        'device_id' => $deviceConfig['ip'],
                        'verification_score' => $identified['score'],
                        'scan_data' => json_encode(['finger_position' => $identified['finger_position']]),
                        'marked_by' => auth()->id(),
                        'marked_at' => now(),
                    ]
                );
            } else {
                $record = StaffAttendance::updateOrCreate(
                    [
                        'user_id' => $identified['user_id'],
                        'date' => today(),
                    ],
                    [
                        'status' => $data['status'] ?? 'present',
                        'check_in_time' => $data['check_in_time'] ?? now(),
                        'attendance_method' => 'fingerprint',
                        'device_id' => $deviceConfig['ip'],
                        'verification_score' => $identified['score'],
                        'scan_data' => json_encode(['finger_position' => $identified['finger_position']]),
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Attendance recorded via fingerprint.',
                'record' => $record,
                'user' => $identified,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fingerprint attendance recording failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Record attendance from scanned optical sheet
     */
    protected function recordOptical(array $data): array
    {
        try {
            // Validate image quality
            $validation = $this->opticalService->validateImage($data['image_path']);

            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Invalid image: ' . implode(', ', $validation['errors']),
                ];
            }

            // Process sheet
            $processed = $this->opticalService->processSheet($data['image_path']);

            if (empty($processed)) {
                return [
                    'success' => false,
                    'message' => 'No attendance data found in image.',
                ];
            }

            DB::beginTransaction();

            $records = [];
            $errors = [];

            foreach ($processed as $studentIndex => $attendance) {
                try {
                    // Map student index to actual student ID
                    $student = Student::where('school_id', $this->schoolId)
                        ->where('class_id', $data['class_id'])
                        ->orderBy('name')
                        ->skip($studentIndex)
                        ->first();

                    if (!$student) {
                        $errors[] = "Student at index {$studentIndex} not found.";
                        continue;
                    }

                    $record = AttendanceRecord::updateOrCreate(
                        [
                            'attendance_id' => $data['attendance_id'],
                            'student_id' => $student->id,
                        ],
                        [
                            'status' => $attendance['status'],
                            'attendance_method' => 'optical',
                            'verification_score' => $attendance['confidence'],
                            'scan_data' => json_encode(['student_index' => $studentIndex]),
                            'marked_by' => auth()->id(),
                            'marked_at' => now(),
                        ]
                    );

                    $records[] = $record;
                } catch (\Exception $e) {
                    $errors[] = "Failed to record student {$studentIndex}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = count($records) . ' attendance records saved.';
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' errors occurred.';
            }

            return [
                'success' => true,
                'message' => $message,
                'records' => $records,
                'errors' => $errors,
                'warnings' => $validation['warnings'],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Optical attendance recording failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to process optical sheet: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if scan is duplicate (within grace period)
     */
    protected function isDuplicateScan(string $userType, int $userId, string $method): bool
    {
        $gracePeriod = $this->settings->grace_period_minutes ?? 5;
        $cutoff = now()->subMinutes($gracePeriod);

        if ($userType === 'student') {
            return AttendanceRecord::where('student_id', $userId)
                ->where('attendance_method', $method)
                ->where('marked_at', '>=', $cutoff)
                ->exists();
        } else {
            return StaffAttendance::where('user_id', $userId)
                ->where('attendance_method', $method)
                ->whereDate('date', today())
                ->where('created_at', '>=', $cutoff)
                ->exists();
        }
    }

    /**
     * Get attendance statistics for a session
     */
    public function getStatistics(int $attendanceId): array
    {
        $total = AttendanceRecord::where('attendance_id', $attendanceId)->count();
        $byStatus = AttendanceRecord::where('attendance_id', $attendanceId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byMethod = AttendanceRecord::where('attendance_id', $attendanceId)
            ->select('attendance_method', DB::raw('count(*) as count'))
            ->groupBy('attendance_method')
            ->pluck('count', 'attendance_method')
            ->toArray();

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'by_method' => $byMethod,
            'present' => $byStatus['present'] ?? 0,
            'absent' => $byStatus['absent'] ?? 0,
            'late' => $byStatus['late'] ?? 0,
        ];
    }
}
