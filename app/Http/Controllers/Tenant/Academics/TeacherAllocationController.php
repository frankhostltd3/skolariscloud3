<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherAllocationController extends Controller
{
    /**
     * Show the teacher allocation form
     */
    public function index(): View
    {
        $teachers = Teacher::with(['classes', 'subjects'])->orderBy('first_name')->get();
        $classes = SchoolClass::with('streams')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('tenant.academics.allocations.teachers.index', compact('teachers', 'classes', 'subjects'));
    }

    /**
     * Show allocation form for specific teacher
     */
    public function show(Teacher $teacher): View
    {
        $teacher->load(['classes.streams', 'subjects', 'classesAsMainTeacher']);
        $classes = SchoolClass::with('streams')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        return view('tenant.academics.allocations.teachers.show', compact('teacher', 'classes', 'subjects'));
    }

    /**
     * Allocate classes to teacher
     */
    public function allocateClasses(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'classes' => ['required', 'array'],
            'classes.*' => ['exists:classes,id'],
            'is_class_teacher' => ['nullable', 'array'],
            'is_class_teacher.*' => ['boolean'],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $academicYear = $validated['academic_year'] ?? date('Y');
        $syncData = [];

        foreach ($validated['classes'] as $classId) {
            $syncData[$classId] = [
                'academic_year' => $academicYear,
                'is_class_teacher' => isset($validated['is_class_teacher'][$classId]) ? true : false,
            ];
        }

        // Sync classes (will remove old, add new)
        $teacher->classes()->syncWithoutDetaching($syncData);

        return redirect()->back()->with('success', __('Classes allocated to teacher successfully.'));
    }

    /**
     * Allocate subjects to teacher
     */
    public function allocateSubjects(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'subjects' => ['required', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'subject_classes' => ['nullable', 'array'],
            'subject_classes.*' => ['nullable', 'exists:classes,id'],
            'academic_year' => ['nullable', 'string', 'max:20'],
        ]);

        $academicYear = $validated['academic_year'] ?? date('Y');
        $syncData = [];

        foreach ($validated['subjects'] as $subjectId) {
            $classId = $validated['subject_classes'][$subjectId] ?? null;
            
            // Create unique key for subject+class combination
            $pivotKey = $subjectId . '-' . ($classId ?? 'all');
            
            $syncData[$subjectId] = [
                'class_id' => $classId,
                'academic_year' => $academicYear,
            ];
        }

        // Sync subjects
        $teacher->subjects()->sync($syncData);

        return redirect()->back()->with('success', __('Subjects allocated to teacher successfully.'));
    }

    /**
     * Remove class allocation
     */
    public function removeClassAllocation(Request $request, Teacher $teacher, SchoolClass $class): RedirectResponse
    {
        $teacher->classes()->detach($class->id);
        
        return redirect()->back()->with('success', __('Class removed from teacher allocation.'));
    }

    /**
     * Remove subject allocation
     */
    public function removeSubjectAllocation(Request $request, Teacher $teacher, Subject $subject): RedirectResponse
    {
        $teacher->subjects()->detach($subject->id);
        
        return redirect()->back()->with('success', __('Subject removed from teacher allocation.'));
    }

    /**
     * Set main class teacher
     */
    public function setMainClassTeacher(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
        ]);

        $class->update([
            'class_teacher_id' => $validated['teacher_id'],
        ]);

        return redirect()->back()->with('success', __('Main class teacher assigned successfully.'));
    }
}
