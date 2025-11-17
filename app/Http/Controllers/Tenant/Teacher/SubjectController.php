<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\Subject;

class SubjectController extends Controller
{
    /**
     * Show a list of subjects the teacher is assigned to teach.
     */
    public function index()
    {
        $teacher = Auth::user();

        // Get subjects the teacher is assigned to teach
        $subjects = Subject::whereHas('classes', function ($q) use ($teacher) {
            $q->where('class_subjects.teacher_id', $teacher->id);
        })
        ->with(['classes' => function ($q) use ($teacher) {
            $q->where('class_subjects.teacher_id', $teacher->id)
              ->withCount('students');
        }])
        ->withCount(['classes' => function ($q) use ($teacher) {
            $q->where('class_subjects.teacher_id', $teacher->id);
        }])
        ->get();

        return view('tenant.teacher.subjects.index', compact('subjects'));
    }
}