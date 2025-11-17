<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Academic\ClassRoom;
use App\Models\Subject;
use App\Models\Grade;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradesController extends Controller
{
    /**
     * Show grades entered by the authenticated teacher with filters.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();

        // Filters
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $studentQ = trim((string)$request->query('q'));
        $assessmentType = $request->query('assessment_type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Grade::query()
            ->where('teacher_id', $teacher->id)
            ->with(['student', 'subject', 'class'])
            ->latest('assessment_date');

        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        if ($assessmentType) {
            $query->where('assessment_type', $assessmentType);
        }
        if ($studentQ !== '') {
            $query->whereHas('student', function ($q) use ($studentQ) {
                $q->where('name', 'like', "%{$studentQ}%")
                    ->orWhere('email', 'like', "%{$studentQ}%");
            });
        }
        if ($startDate && $endDate) {
            $query->whereBetween('assessment_date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $query->whereDate('assessment_date', '>=', \Carbon\Carbon::parse($startDate)->toDateString());
        } elseif ($endDate) {
            $query->whereDate('assessment_date', '<=', \Carbon\Carbon::parse($endDate)->toDateString());
        }

        $grades = $query->paginate(20)->appends($request->query());

        // For filter dropdowns: only classes/subjects the teacher is associated with
        $classes = ClassRoom::where('class_teacher_id', $teacher->id)
            ->orWhereHas('subjects', function ($q) use ($teacher) {
                $q->where('class_subjects.teacher_id', $teacher->id);
            })
            ->orderBy('name')
            ->get();

        $subjects = Subject::whereHas('classes', function ($q) use ($teacher) {
                $q->where('class_subjects.teacher_id', $teacher->id);
            })
            ->orderBy('name')
            ->get();

        // Subject distribution (counts by subject for this teacher and current filters except student name)
        $distQuery = Grade::query()->where('teacher_id', $teacher->id);
        if ($classId) $distQuery->where('class_id', $classId);
        if ($subjectId) $distQuery->where('subject_id', $subjectId);
        if ($assessmentType) $distQuery->where('assessment_type', $assessmentType);
        if ($startDate && $endDate) {
            $distQuery->whereBetween('assessment_date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $distQuery->whereDate('assessment_date', '>=', \Carbon\Carbon::parse($startDate)->toDateString());
        } elseif ($endDate) {
            $distQuery->whereDate('assessment_date', '<=', \Carbon\Carbon::parse($endDate)->toDateString());
        }
        $subjectCounts = $distQuery->selectRaw('subject_id, COUNT(*) as cnt')->groupBy('subject_id')->pluck('cnt', 'subject_id');
        $labelsById = $subjects->keyBy('id')->map->name->toArray();
        $ids = array_keys($subjectCounts->toArray());
        $chartLabels = array_map(function ($id) use ($labelsById) {
            return $labelsById[$id] ?? ('Subject ' . $id);
        }, $ids);
        $chartCounts = array_values($subjectCounts->toArray());

        // Assessment type distribution
        $typeQuery = Grade::query()->where('teacher_id', $teacher->id);
        if ($classId) $typeQuery->where('class_id', $classId);
        if ($subjectId) $typeQuery->where('subject_id', $subjectId);
        if ($assessmentType) $typeQuery->where('assessment_type', $assessmentType);
        if ($startDate && $endDate) {
            $typeQuery->whereBetween('assessment_date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $typeQuery->whereDate('assessment_date', '>=', \Carbon\Carbon::parse($startDate)->toDateString());
        } elseif ($endDate) {
            $typeQuery->whereDate('assessment_date', '<=', \Carbon\Carbon::parse($endDate)->toDateString());
        }
        $typeCounts = $typeQuery->selectRaw('assessment_type, COUNT(*) as cnt')->groupBy('assessment_type')->pluck('cnt', 'assessment_type')->toArray();
        $typeLabels = array_map('ucfirst', array_keys($typeCounts));
        $typeValues = array_values($typeCounts);

        return view('tenant.teacher.grades.index', compact('grades', 'classes', 'subjects', 'classId', 'subjectId', 'studentQ', 'assessmentType', 'startDate', 'endDate', 'chartLabels', 'chartCounts', 'typeLabels', 'typeValues'));
    }

    /**
     * Export filtered grades as CSV for the authenticated teacher.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $teacher = Auth::user();

        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $studentQ = trim((string)$request->query('q'));
        $assessmentType = $request->query('assessment_type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Grade::query()
            ->where('teacher_id', $teacher->id)
            ->with(['student', 'subject', 'class'])
            ->latest('assessment_date');

        if ($classId) $query->where('class_id', $classId);
        if ($subjectId) $query->where('subject_id', $subjectId);
        if ($assessmentType) $query->where('assessment_type', $assessmentType);
        if ($studentQ !== '') {
            $query->whereHas('student', function ($q) use ($studentQ) {
                $q->where('name', 'like', "%{$studentQ}%")
                    ->orWhere('email', 'like', "%{$studentQ}%");
            });
        }
        if ($startDate && $endDate) {
            $query->whereBetween('assessment_date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $query->whereDate('assessment_date', '>=', \Carbon\Carbon::parse($startDate)->toDateString());
        } elseif ($endDate) {
            $query->whereDate('assessment_date', '<=', \Carbon\Carbon::parse($endDate)->toDateString());
        }

        $filename = 'my-grades-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Headers
            fputcsv($handle, ['Student', 'Class', 'Subject', 'Assessment', 'Type', 'Marks', 'Total', 'Percentage', 'Date']);
            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $g) {
                    fputcsv($handle, [
                        optional($g->student)->name,
                        optional($g->class)->name,
                        optional($g->subject)->name,
                        $g->assessment_name,
                        ucfirst((string) $g->assessment_type),
                        $g->marks_obtained,
                        $g->total_marks,
                        method_exists($g, 'getAttribute') ? ($g->percentage ?? ($g->total_marks ? round(($g->marks_obtained / $g->total_marks) * 100, 2) : null)) : null,
                        optional($g->assessment_date)->format('Y-m-d'),
                    ]);
                }
            });
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Teacher-focused new grade flow: pick class/subject/student then go to grade create.
     */
    public function create(Request $request)
    {
        $teacher = Auth::user();
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $studentId = $request->query('student_id');
        $action = $request->query('action');

        // If we have all required parameters and action is enter_grade, show the grade entry form
        if ($action === 'enter_grade' && $classId && $subjectId && $studentId) {
            $class = ClassRoom::find($classId);
            $subject = Subject::find($subjectId);
            $student = \App\Models\User::find($studentId);

            if (!$class || !$subject || !$student) {
                return redirect()->route('tenant.teacher.grades.create')
                    ->with('error', 'Invalid class, subject, or student selected.');
            }

            // Verify teacher has access to this class/subject combination
            $hasAccess = $class->class_teacher_id === $teacher->id ||
                        $class->subjects()->wherePivot('teacher_id', $teacher->id)->where('subjects.id', $subjectId)->exists();

            if (!$hasAccess) {
                return redirect()->route('tenant.teacher.grades.create')
                    ->with('error', 'You do not have permission to enter grades for this class/subject combination.');
            }

            return view('tenant.teacher.grades.create', compact('class', 'subject', 'student'));
        }

        $classes = ClassRoom::where('class_teacher_id', $teacher->id)
            ->orWhereHas('subjects', function ($q) use ($teacher) {
                $q->where('class_subjects.teacher_id', $teacher->id);
            })
            ->orderBy('name')
            ->get();

        $subjects = collect();
        if ($classId) {
            $class = ClassRoom::find($classId);
            if ($class) {
                $subjects = $class->subjects()->wherePivot('teacher_id', $teacher->id)->orderBy('name')->get();
            }
        } else {
            $subjects = Subject::whereHas('classes', function ($q) use ($teacher) {
                $q->where('class_subjects.teacher_id', $teacher->id);
            })
                ->orderBy('name')->get();
        }

        $students = collect();
        if ($classId) {
            $class = ClassRoom::find($classId);
            if ($class) {
                // students() returns User models already
                $students = $class->students()->get();
            }
        }

        return view('tenant.teacher.grades.new', compact('classes', 'subjects', 'students', 'classId', 'subjectId'));
    }

    /**
     * Store a new grade entered by the teacher.
     */
    public function store(Request $request)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_name' => 'required|string|max:255',
            'assessment_type' => 'required|in:quiz,test,exam,assignment,project,homework,participation,other',
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0.01',
            'assessment_date' => 'required|date',
            'term' => 'nullable|string|max:100',
            'comments' => 'nullable|string|max:1000',
        ]);

        // Verify teacher has access to this class/subject combination
        $class = ClassRoom::find($validated['class_id']);
        $hasAccess = $class->class_teacher_id === $teacher->id ||
                    $class->subjects()->wherePivot('teacher_id', $teacher->id)->where('subjects.id', $validated['subject_id'])->exists();

        if (!$hasAccess) {
            return redirect()->route('tenant.teacher.grades.create')
                ->with('error', 'You do not have permission to enter grades for this class/subject combination.');
        }

        // Verify the student is in the selected class
        $studentInClass = $class->students()->where('users.id', $validated['student_id'])->exists();
        if (!$studentInClass) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'The selected student is not in the specified class.');
        }

        // Validate marks
        if ($validated['marks_obtained'] > $validated['total_marks']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Marks obtained cannot be greater than total marks.');
        }

        // Calculate percentage
        $percentage = ($validated['marks_obtained'] / $validated['total_marks']) * 100;

        Grade::create([
            'student_id' => $validated['student_id'],
            'class_id' => $validated['class_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $teacher->id,
            'assessment_name' => $validated['assessment_name'],
            'assessment_type' => $validated['assessment_type'],
            'marks_obtained' => $validated['marks_obtained'],
            'total_marks' => $validated['total_marks'],
            'assessment_date' => $validated['assessment_date'],
            'term' => $validated['term'],
            'remarks' => $validated['comments'],
            'entered_by' => $teacher->id,
        ]);

        return redirect()->route('tenant.teacher.grades.index')
            ->with('success', 'Grade saved successfully!');
    }
}