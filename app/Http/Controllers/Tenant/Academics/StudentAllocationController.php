<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassStream;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentAllocationController extends Controller
{
    /**
     * Show student allocation dashboard
     */
    public function index(): View
    {
        $students = Student::with(['class', 'stream', 'subjects'])->paginate(20);
        $classes = SchoolClass::with('streams')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('tenant.academics.allocations.students.index', compact('students', 'classes', 'subjects'));
    }

    /**
     * Show allocation form for specific student
     */
    public function show(Student $student): View
    {
        $student->load(['class', 'stream', 'subjects']);
        $classes = SchoolClass::with('streams')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('tenant.academics.allocations.students.show', compact('student', 'classes', 'subjects'));
    }

    /**
     * Allocate or promote student to class and stream
     */
    public function allocateClass(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'class_stream_id' => ['nullable', 'exists:class_streams,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'reason' => ['nullable', 'string', 'max:255'], // admission, promotion, transfer
        ]);

        // Verify stream belongs to class if provided
        if (!empty($validated['class_stream_id'])) {
            $stream = ClassStream::find($validated['class_stream_id']);
            if ($stream && $stream->class_id != $validated['class_id']) {
                return redirect()->back()->withErrors(['class_stream_id' => 'Stream does not belong to selected class.']);
            }
        }

        $student->update([
            'class_id' => $validated['class_id'],
            'class_stream_id' => $validated['class_stream_id'] ?? null,
            'roll_number' => $validated['roll_number'] ?? null,
        ]);

        $action = $validated['reason'] ?? 'allocation';
        return redirect()->back()->with('success', __('Student ' . $action . ' successful.'));
    }

    /**
     * Allocate subjects to student
     */
    public function allocateSubjects(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'subjects' => ['required', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'core_subjects' => ['nullable', 'array'],
            'core_subjects.*' => ['boolean'],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $academicYear = $validated['academic_year'] ?? date('Y');
        $syncData = [];

        foreach ($validated['subjects'] as $subjectId) {
            $syncData[$subjectId] = [
                'academic_year' => $academicYear,
                'is_core' => isset($validated['core_subjects'][$subjectId]) ? true : false,
                'status' => 'active',
            ];
        }

        // Sync subjects (replaces all current assignments)
        $student->subjects()->sync($syncData);

        return redirect()->back()->with('success', __('Subjects allocated to student successfully.'));
    }

    /**
     * Bulk allocate students to class
     */
    public function bulkAllocateClass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'class_stream_id' => ['nullable', 'exists:class_streams,id'],
        ]);

        // Verify stream belongs to class
        if (!empty($validated['class_stream_id'])) {
            $stream = ClassStream::find($validated['class_stream_id']);
            if ($stream && $stream->class_id != $validated['class_id']) {
                return redirect()->back()->withErrors(['class_stream_id' => 'Stream does not belong to selected class.']);
            }
        }

        Student::whereIn('id', $validated['student_ids'])->update([
            'class_id' => $validated['class_id'],
            'class_stream_id' => $validated['class_stream_id'] ?? null,
        ]);

        $count = count($validated['student_ids']);
        return redirect()->back()->with('success', __(':count students allocated to class successfully.', ['count' => $count]));
    }

    /**
     * Bulk allocate subjects to students
     */
    public function bulkAllocateSubjects(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'subjects' => ['required', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $academicYear = $validated['academic_year'] ?? date('Y');
        $syncData = [];

        foreach ($validated['subjects'] as $subjectId) {
            $syncData[$subjectId] = [
                'academic_year' => $academicYear,
                'is_core' => true,
                'status' => 'active',
            ];
        }

        foreach ($validated['student_ids'] as $studentId) {
            $student = Student::find($studentId);
            if ($student) {
                $student->subjects()->syncWithoutDetaching($syncData);
            }
        }

        $count = count($validated['student_ids']);
        return redirect()->back()->with('success', __('Subjects allocated to :count students successfully.', ['count' => $count]));
    }

    /**
     * Remove subject allocation
     */
    public function removeSubjectAllocation(Request $request, Student $student, Subject $subject): RedirectResponse
    {
        $student->subjects()->detach($subject->id);
        
        return redirect()->back()->with('success', __('Subject removed from student allocation.'));
    }

    /**
     * Promote students to next class
     */
    public function promoteStudents(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_class_id' => ['required', 'exists:classes,id'],
            'to_class_id' => ['required', 'exists:classes,id'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'promote_all' => ['nullable', 'boolean'],
        ]);

        if ($validated['promote_all'] ?? false) {
            // Promote all students from the class
            Student::where('class_id', $validated['from_class_id'])
                ->update([
                    'class_id' => $validated['to_class_id'],
                    'class_stream_id' => null, // Reset stream on promotion
                ]);
            
            $count = Student::where('class_id', $validated['to_class_id'])->count();
        } else {
            // Promote selected students
            Student::whereIn('id', $validated['student_ids'] ?? [])
                ->update([
                    'class_id' => $validated['to_class_id'],
                    'class_stream_id' => null,
                ]);
            
            $count = count($validated['student_ids'] ?? []);
        }

        return redirect()->back()->with('success', __(':count students promoted successfully.', ['count' => $count]));
    }
}
