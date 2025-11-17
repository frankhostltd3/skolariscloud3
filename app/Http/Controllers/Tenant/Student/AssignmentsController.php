<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\Academic\Enrollment;

class AssignmentsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return view('tenant.student.assignments.index', [
                'student' => null,
                'assignments' => collect(),
                'subjects' => collect(),
                'statistics' => null,
            ]);
        }

        // Get assignments for student's class
        $query = Assignment::with(['class', 'subject', 'teacher', 'submissions' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
        ->where('class_id', $student->class_id)
        ->where('published', true)
        ->latest('due_date');

        // Apply filters
        $status = $request->get('status');
        $subjectId = $request->get('subject_id');

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Get all assignments first for statistics
        $allAssignments = $query->get();

        // Apply status filter
        if ($status) {
            $allAssignments = $allAssignments->filter(function($assignment) use ($status, $student) {
                $submission = $assignment->submissions->first();
                
                switch ($status) {
                    case 'pending':
                        return !$submission && !$assignment->isOverdue();
                    case 'overdue':
                        return !$submission && $assignment->isOverdue();
                    case 'submitted':
                        return $submission && !$submission->isGraded();
                    case 'graded':
                        return $submission && $submission->isGraded();
                    default:
                        return true;
                }
            });
        }

        // Paginate manually
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $assignments = new \Illuminate\Pagination\LengthAwarePaginator(
            $allAssignments->forPage($currentPage, $perPage),
            $allAssignments->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get subjects for filter
        $subjects = \App\Models\Subject::whereIn('id', 
            Assignment::where('class_id', $student->class_id)->pluck('subject_id')->unique()
        )->orderBy('name')->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($student);

        return view('tenant.student.assignments.index', compact(
            'student',
            'assignments',
            'subjects',
            'statistics',
            'status',
            'subjectId'
        ));
    }

    public function show(Assignment $assignment)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            abort(404, 'Student record not found');
        }

        // Ensure the student belongs to the assignment class
        if ($assignment->class_id != $student->class_id) {
            abort(403, 'You do not have access to this assignment');
        }

        // Get submission if exists
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        // Load relationships
        $assignment->load(['class', 'subject', 'teacher']);

        // Get all submissions count for this assignment
        $totalSubmissions = AssignmentSubmission::where('assignment_id', $assignment->id)->count();

        // Calculate days remaining or overdue
        $daysRemaining = null;
        $daysOverdue = null;
        if ($assignment->due_date) {
            if ($assignment->due_date->isFuture()) {
                $daysRemaining = now()->diffInDays($assignment->due_date, false);
            } else {
                $daysOverdue = now()->diffInDays($assignment->due_date, false);
            }
        }

        return view('tenant.student.assignments.show', compact(
            'assignment',
            'submission',
            'student',
            'totalSubmissions',
            'daysRemaining',
            'daysOverdue'
        ));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        // Verify access
        if ($assignment->class_id != $student->class_id) {
            abort(403, 'You do not have access to this assignment');
        }

        // Check if assignment is still open
        if ($assignment->isOverdue() && !$assignment->allow_late_submission) {
            return redirect()->route('tenant.student.assignments.show', $assignment)
                ->with('error', 'This assignment is past due and no longer accepts submissions.');
        }

        // Validate input
        $data = $request->validate([
            'notes' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,zip,jpg,jpeg,png',
        ]);

        // Check for existing submission
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        // Check resubmission rules
        if ($existing) {
            if ($existing->isGraded()) {
                return redirect()->route('tenant.student.assignments.show', $assignment)
                    ->with('error', 'Cannot resubmit a graded assignment.');
            }
            
            if (!$assignment->allow_resubmission) {
                return redirect()->route('tenant.student.assignments.show', $assignment)
                    ->with('error', 'Resubmissions are not allowed for this assignment.');
            }
        }

        // Create or update submission
        $submission = $existing ?? new AssignmentSubmission([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
        ]);

        $submission->notes = $data['notes'] ?? $submission->notes;
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($submission->attachment_path) {
                Storage::disk('public')->delete($submission->attachment_path);
            }
            
            // Store new file
            $file = $request->file('attachment');
            $filename = time() . '_' . $student->id . '_' . $file->getClientOriginalName();
            $submission->attachment_path = $file->storeAs('assignment-submissions', $filename, 'public');
        }
        
        $submission->submitted_at = now();
        $submission->save();

        return redirect()->route('tenant.student.assignments.show', $assignment)
            ->with('success', $existing ? 'Assignment resubmitted successfully!' : 'Assignment submitted successfully!');
    }

    private function calculateStatistics($student)
    {
        $assignments = Assignment::where('class_id', $student->class_id)
            ->where('published', true)
            ->get();

        $submissions = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get();

        $totalAssignments = $assignments->count();
        $submitted = $submissions->count();
        $pending = $assignments->filter(function($assignment) use ($submissions) {
            return !$submissions->where('assignment_id', $assignment->id)->count() && !$assignment->isOverdue();
        })->count();
        $overdue = $assignments->filter(function($assignment) use ($submissions) {
            return !$submissions->where('assignment_id', $assignment->id)->count() && $assignment->isOverdue();
        })->count();
        $graded = $submissions->filter(function($submission) {
            return $submission->isGraded();
        })->count();

        $averageScore = 0;
        if ($graded > 0) {
            $totalMarks = $submissions->where('marks', '!=', null)->sum('marks');
            $totalPossible = $submissions->where('marks', '!=', null)->count() * 100; // Assuming max marks
            $averageScore = $totalPossible > 0 ? ($totalMarks / $totalPossible) * 100 : 0;
        }

        return [
            'total' => $totalAssignments,
            'submitted' => $submitted,
            'pending' => $pending,
            'overdue' => $overdue,
            'graded' => $graded,
            'average_score' => $averageScore,
        ];
    }
}