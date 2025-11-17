<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAttendanceController extends Controller
{
    /**
     * Display a listing of staff attendance.
     */
    public function index(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $query = StaffAttendance::forSchool($school->id)
            ->with(['staff', 'approver'])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by staff member
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $attendances = $query->paginate(perPage());

        // Get all staff members
        $staff = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['teacher', 'admin', 'accountant', 'librarian']);
            })
            ->orderBy('name')
            ->get();

        return view('admin.staff-attendance.index', compact('attendances', 'staff', 'school'));
    }

    /**
     * Show the form for creating new staff attendance.
     */
    public function create(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $staff = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['teacher', 'admin', 'accountant', 'librarian']);
            })
            ->orderBy('name')
            ->get();

        return view('admin.staff-attendance.create', compact('staff', 'school'));
    }

    /**
     * Store newly created staff attendance.
     */
    public function store(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day,on_leave,sick_leave,official_duty',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'leave_reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['school_id'] = $school->id;

        // Calculate hours worked if check_in and check_out are provided
        if (!empty($validated['check_in']) && !empty($validated['check_out'])) {
            $checkIn = \Carbon\Carbon::parse($validated['check_in']);
            $checkOut = \Carbon\Carbon::parse($validated['check_out']);
            $validated['hours_worked'] = $checkOut->diffInMinutes($checkIn) / 60;
        }

        StaffAttendance::create($validated);

        return redirect()->route('admin.staff-attendance.index')
            ->with('success', 'Staff attendance recorded successfully.');
    }

    /**
     * Display the specified staff attendance.
     */
    public function show(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = StaffAttendance::forSchool($school->id)
            ->with(['staff', 'approver'])
            ->findOrFail($id);

        return view('admin.staff-attendance.show', compact('attendance', 'school'));
    }

    /**
     * Show the form for editing staff attendance.
     */
    public function edit(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = StaffAttendance::forSchool($school->id)->findOrFail($id);

        $staff = User::where('school_id', $school->id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['teacher', 'admin', 'accountant', 'librarian']);
            })
            ->orderBy('name')
            ->get();

        return view('admin.staff-attendance.edit', compact('attendance', 'staff', 'school'));
    }

    /**
     * Update the specified staff attendance.
     */
    public function update(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = StaffAttendance::forSchool($school->id)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,half_day,on_leave,sick_leave,official_duty',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'leave_reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate hours worked if check_in and check_out are provided
        if (!empty($validated['check_in']) && !empty($validated['check_out'])) {
            $checkIn = \Carbon\Carbon::parse($validated['check_in']);
            $checkOut = \Carbon\Carbon::parse($validated['check_out']);
            $validated['hours_worked'] = $checkOut->diffInMinutes($checkIn) / 60;
        }

        $attendance->update($validated);

        return redirect()->route('admin.staff-attendance.index')
            ->with('success', 'Staff attendance updated successfully.');
    }

    /**
     * Approve staff leave/absence.
     */
    public function approve(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = StaffAttendance::forSchool($school->id)->findOrFail($id);

        $attendance->update([
            'approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Staff attendance approved successfully.');
    }

    /**
     * Bulk mark attendance for today.
     */
    public function bulkMark(Request $request)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'records' => 'required|array',
            'records.*.staff_id' => 'required|exists:users,id',
            'records.*.status' => 'required|in:present,absent,late,half_day,on_leave,sick_leave,official_duty',
            'records.*.check_in' => 'nullable|date_format:H:i',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['records'] as $recordData) {
                StaffAttendance::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'staff_id' => $recordData['staff_id'],
                        'attendance_date' => $validated['attendance_date'],
                    ],
                    [
                        'status' => $recordData['status'],
                        'check_in' => $recordData['check_in'] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.staff-attendance.index')
                ->with('success', 'Staff attendance marked successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark attendance: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified staff attendance.
     */
    public function destroy(Request $request, $id)
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $attendance = StaffAttendance::forSchool($school->id)->findOrFail($id);
        $attendance->delete();

        return redirect()->route('admin.staff-attendance.index')
            ->with('success', 'Staff attendance deleted successfully.');
    }
}
