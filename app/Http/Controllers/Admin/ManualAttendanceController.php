<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Student;
use App\Services\Attendance\AttendanceRecordingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualAttendanceController extends Controller
{
    protected $recordingService;

    public function __construct()
    {
        $this->recordingService = new AttendanceRecordingService(auth()->user()->school_id ?? 1);
    }

    /**
     * Display manual roll call interface
     */
    public function mark($attendanceId)
    {
        $attendance = Attendance::with(['class.students', 'subject'])
            ->findOrFail($attendanceId);

        // Get existing records
        $existingRecords = AttendanceRecord::where('attendance_id', $attendanceId)
            ->pluck('status', 'student_id')
            ->toArray();

        $students = $attendance->class->students()
            ->orderBy('name')
            ->get()
            ->map(function($student) use ($existingRecords) {
                $student->attendance_status = $existingRecords[$student->id] ?? null;
                return $student;
            });

        return view('admin.attendance.manual-mark', compact('attendance', 'students'));
    }

    /**
     * Save manual attendance records
     */
    public function saveManual(Request $request, $attendanceId)
    {
        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.student_id' => 'required|integer|exists:students,id',
            'records.*.status' => 'required|in:present,absent,late,excused,sick,half_day',
            'records.*.notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;
            $errors = [];

            foreach ($validated['records'] as $record) {
                $result = $this->recordingService->record('manual', [
                    'user_type' => 'student',
                    'attendance_id' => $attendanceId,
                    'student_id' => $record['student_id'],
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                ]);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $errors[] = $result['message'];
                }
            }

            DB::commit();

            return redirect()
                ->route('tenant.modules.attendance.show', $attendanceId)
                ->with('success', "Attendance saved! {$successCount} records processed.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    /**
     * Bulk mark students (all present, all absent, etc.)
     */
    public function bulkMark(Request $request, $attendanceId)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:students,id',
            'status' => 'required|in:present,absent,late,excused,sick,half_day',
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;

            foreach ($validated['student_ids'] as $studentId) {
                $result = $this->recordingService->record('manual', [
                    'user_type' => 'student',
                    'attendance_id' => $attendanceId,
                    'student_id' => $studentId,
                    'status' => $validated['status'],
                ]);

                if ($result['success']) {
                    $successCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} students marked as {$validated['status']}",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Bulk marking failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
