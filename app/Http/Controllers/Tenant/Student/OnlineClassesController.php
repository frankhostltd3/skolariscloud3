<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\VirtualClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OnlineClassesController extends Controller
{
    /**
     * Show upcoming online classes for the logged-in student.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get student record
        $student = Student::where('email', $user->email)->first();
        
        $query = VirtualClass::with(['class', 'subject', 'teacher'])
            ->whereNotNull('meeting_url')
            ->where('scheduled_at', '>=', now())
            ->whereIn('status', ['scheduled', 'live'])
            ->orderBy('scheduled_at');

        // Filter by student's class
        if ($student && $student->class_id) {
            $query->where('class_id', $student->class_id);
            
            // If student has a stream, filter by that stream or null stream (whole class)
            if ($student->class_stream_id) {
                $query->where(function($q) use ($student) {
                    $q->where('class_stream_id', $student->class_stream_id)
                      ->orWhereNull('class_stream_id');
                });
            }
        }

        // Filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->integer('subject_id'));
        }
        if ($request->filled('platform')) {
            $platform = $request->get('platform');
            $query->where('platform', $platform);
        }
        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date('date'));
        }

        $classes = $query->paginate(15)->appends($request->query());

        // Get subjects for filter
        $subjects = $student && $student->class_id 
            ? Subject::orderBy('name')->get()
            : collect();

        return view('tenant.student.online.index', [
            'classes' => $classes,
            'student' => $student,
            'filters' => $request->only(['subject_id', 'platform', 'date']),
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show recorded classes available to the student.
     */
    public function recordings(Request $request)
    {
        $user = Auth::user();
        
        // Get student record
        $student = Student::where('email', $user->email)->first();

        $query = VirtualClass::with(['class', 'subject', 'teacher'])
            ->whereNotNull('recording_url')
            ->where('status', 'completed')
            ->orderByDesc('scheduled_at');

        // Filter by student's class
        if ($student && $student->class_id) {
            $query->where('class_id', $student->class_id);
            
            // If student has a stream, filter by that stream or null stream (whole class)
            if ($student->class_stream_id) {
                $query->where(function($q) use ($student) {
                    $q->where('class_stream_id', $student->class_stream_id)
                      ->orWhereNull('class_stream_id');
                });
            }
        }

        // Filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->integer('subject_id'));
        }
        if ($request->filled('platform')) {
            $platform = $request->get('platform');
            $query->where('platform', $platform);
        }
        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date('date'));
        }

        $recordings = $query->paginate(15)->appends($request->query());

        // Get subjects for filter
        $subjects = $student && $student->class_id 
            ? Subject::orderBy('name')->get()
            : collect();

        return view('tenant.student.online.recordings', [
            'recordings' => $recordings,
            'student' => $student,
            'filters' => $request->only(['subject_id', 'platform', 'date']),
            'subjects' => $subjects,
        ]);
    }
    
    /**
     * Join a specific online class
     */
    public function join($id)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();
        
        $class = VirtualClass::findOrFail($id);
        
        // Check if student is in the class
        if ($student && $class->class_id !== $student->class_id) {
            abort(403, 'You are not enrolled in this class.');
        }
        
        // Check if class can be joined
        if (!$class->can_join) {
            return back()->with('error', 'This class cannot be joined at this time.');
        }
        
        if (!$class->meeting_url) {
            return back()->with('error', 'Meeting link is not available.');
        }
        
        // Record attendance/join time
        $class->recordAttendance($user->id);
        
        // Redirect to meeting URL
        return redirect()->away($class->meeting_url);
    }
    
    /**
     * View a recording
     */
    public function viewRecording($id)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();
        
        $class = VirtualClass::findOrFail($id);
        
        // Check if student is in the class
        if ($student && $class->class_id !== $student->class_id) {
            abort(403, 'You are not enrolled in this class.');
        }
        
        if (!$class->recording_url) {
            return back()->with('error', 'Recording is not available for this class.');
        }
        
        // Redirect to recording URL
        return redirect()->away($class->recording_url);
    }
}