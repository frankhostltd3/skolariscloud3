<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\Enrollment;
use App\Models\Subject;

class SubjectsController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $classIds = Enrollment::where('student_id', $student->id)->pluck('class_id');

        // Subjects offered in classes where the student is enrolled
        $subjects = Subject::whereHas('classes', function ($q) use ($classIds) {
            $q->whereIn('class_id', $classIds);
        })
            ->with(['classes' => function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)->select('class_id');
            }])
            ->orderBy('name')
            ->get();

        return view('tenant.student.subjects.index', compact('subjects'));
    }
}