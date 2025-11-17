<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\VirtualClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VirtualClassController extends Controller
{
    /**
     * Display all virtual classes for student
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        $studentId = $student->id;
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('school_class_id');

        $query = VirtualClass::whereIn('class_id', $classIds)
            ->with(['class', 'subject', 'teacher']);

        // Filter by status
        $filter = $request->get('filter', 'all');
        
        switch ($filter) {
            case 'upcoming':
                $query->where('status', 'scheduled')
                    ->where('scheduled_at', '>=', Carbon::now());
                break;
                
            case 'ongoing':
                $query->where('status', 'ongoing');
                break;
                
            case 'completed':
                $query->where('status', 'completed');
                break;
                
            case 'missed':
                // Completed classes that student didn't attend
                $query->where('status', 'completed')
                    ->whereDoesntHave('attendances', function ($q) use ($studentId) {
                        $q->where('student_id', $studentId)
                          ->whereIn('status', ['present', 'late']);
                    });
                break;
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $classes = $query->latest('scheduled_at')->paginate(15);

        // Add attendance status to each class
        $classes->getCollection()->transform(function ($class) use ($studentId) {
            $class->student_attendance = $class->attendances()
                ->where('student_id', $studentId)
                ->first();
            return $class;
        });

        // Calculate stats
        $stats = [
            'total' => VirtualClass::whereIn('class_id', $classIds)->count(),
            'upcoming' => VirtualClass::whereIn('class_id', $classIds)
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>=', Carbon::now())
                ->count(),
            'attended' => VirtualClass::whereIn('class_id', $classIds)
                ->whereHas('attendances', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId)
                      ->whereIn('status', ['present', 'late']);
                })
                ->count(),
            'missed' => VirtualClass::whereIn('class_id', $classIds)
                ->where('status', 'completed')
                ->whereDoesntHave('attendances', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId)
                      ->whereIn('status', ['present', 'late']);
                })
                ->count(),
        ];

        return view('tenant.student.classroom.virtual.index', compact('classes', 'stats', 'filter'));
    }

    /**
     * Show a specific virtual class
     */
    public function show($id)
    {
        $student = Auth::user();
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('school_class_id');

        $class = VirtualClass::whereIn('class_id', $classIds)
            ->with(['class', 'subject', 'teacher'])
            ->findOrFail($id);

        // Get student's attendance record if exists
        $attendance = $class->attendances()
            ->where('student_id', $student->id)
            ->first();

        // Check if class is joinable (scheduled or ongoing)
        $isJoinable = in_array($class->status, ['scheduled', 'ongoing']) && $class->meeting_url;

        // Check if class is starting soon (within 15 minutes)
        $isStartingSoon = $class->scheduled_at->diffInMinutes(Carbon::now(), false) >= -15 
                       && $class->scheduled_at->diffInMinutes(Carbon::now(), false) <= 0;

        return view('tenant.student.classroom.virtual.show', compact(
            'class',
            'attendance',
            'isJoinable',
            'isStartingSoon'
        ));
    }

    /**
     * Join a virtual class
     */
    public function join($id)
    {
        $student = Auth::user();
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('school_class_id');

        $class = VirtualClass::whereIn('class_id', $classIds)
            ->findOrFail($id);

        // Check if class is joinable
        if (!in_array($class->status, ['scheduled', 'ongoing'])) {
            return back()->with('error', 'This class is not currently available to join.');
        }

        if (!$class->meeting_url) {
            return back()->with('error', 'Meeting link is not available yet.');
        }

        // Check if class is too early to join (more than 15 minutes before start)
        if ($class->scheduled_at->diffInMinutes(Carbon::now(), false) < -15) {
            return back()->with('error', 'The class hasn\'t started yet. Please join closer to the scheduled time.');
        }

        // Redirect to meeting URL
        return redirect()->away($class->meeting_url);
    }

    /**
     * View class recording
     */
    public function recording($id)
    {
        $student = Auth::user();
        
        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('school_class_id');

        $class = VirtualClass::whereIn('class_id', $classIds)
            ->findOrFail($id);

        if (!$class->recording_url) {
            return back()->with('error', 'Recording is not available for this class.');
        }

        // Redirect to recording URL
        return redirect()->away($class->recording_url);
    }

    /**
     * Show student's attendance history
     */
    public function attendance()
    {
        $student = Auth::user();
        
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('school_class_id');

        $attendances = \App\Models\VirtualClassAttendance::where('student_id', $student->id)
            ->whereHas('virtualClass', function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds);
            })
            ->with(['virtualClass.class', 'virtualClass.subject', 'virtualClass.teacher'])
            ->latest('created_at')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_classes' => VirtualClass::whereIn('class_id', $classIds)
                ->where('status', 'completed')
                ->count(),
            'present' => \App\Models\VirtualClassAttendance::where('student_id', $student->id)
                ->whereHas('virtualClass', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->where('status', 'present')
                ->count(),
            'late' => \App\Models\VirtualClassAttendance::where('student_id', $student->id)
                ->whereHas('virtualClass', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->where('status', 'late')
                ->count(),
            'absent' => \App\Models\VirtualClassAttendance::where('student_id', $student->id)
                ->whereHas('virtualClass', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->where('status', 'absent')
                ->count(),
        ];

        // Calculate attendance rate
        $totalRecorded = $stats['present'] + $stats['late'] + $stats['absent'];
        $attendanceRate = $totalRecorded > 0 
            ? round((($stats['present'] + $stats['late']) / $totalRecorded) * 100, 1) 
            : 0;

        $stats['attendance_rate'] = $attendanceRate;

        return view('tenant.student.classroom.virtual.attendance', compact('attendances', 'stats'));
    }

    /**
     * Get upcoming classes for today
     */
    public function today()
    {
        $student = Auth::user();
        
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('school_class_id');

        $classes = VirtualClass::whereIn('class_id', $classIds)
            ->whereDate('scheduled_at', Carbon::today())
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->with(['class', 'subject', 'teacher'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('tenant.student.classroom.virtual.today', compact('classes'));
    }
}
