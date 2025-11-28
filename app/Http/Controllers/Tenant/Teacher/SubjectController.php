<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Academic\Subject;

class SubjectController extends Controller
{
    /**
     * Show a list of subjects the teacher is assigned to teach.
     */
    public function index()
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $classSubjectTable = null;

        foreach (['class_subjects', 'class_subject'] as $candidate) {
            if (tenant_table_exists($candidate, $connection)) {
                $classSubjectTable = $candidate;
                break;
            }
        }

        if (! $classSubjectTable) {
            return view('tenant.teacher.subjects.index', ['subjects' => collect()]);
        }

        // Get subjects the teacher is assigned to teach
        $subjects = Subject::whereHas('classes', function ($query) use ($teacher, $classSubjectTable) {
            $query->where($classSubjectTable . '.teacher_id', $teacher->id);
        })
        ->with(['classes' => function ($query) use ($teacher, $hasEnrollments, $hasStudentTable, $classSubjectTable) {
            $query->where($classSubjectTable . '.teacher_id', $teacher->id);

            if ($hasEnrollments) {
                $query->withCount([
                    'students as students_count' => function ($studentQuery) {
                        $studentQuery->where(function ($subQuery) {
                            $subQuery->where('enrollments.status', 'active')
                                ->orWhereNull('enrollments.status');
                        });
                    },
                ]);
            } elseif ($hasStudentTable) {
                $query->withCount(['students as students_count']);
            } else {
                $query->withCount([
                    'students as students_count' => function ($studentQuery) {
                        $studentQuery->whereRaw('1 = 0');
                    },
                ]);
            }
        }])
        ->withCount(['classes' => function ($query) use ($teacher, $classSubjectTable) {
            $query->where($classSubjectTable . '.teacher_id', $teacher->id);
        }])
        ->get();

        if (! ($hasEnrollments || $hasStudentTable)) {
            $subjects->each(function ($subject) {
                $subject->classes->each(function ($class) {
                    $class->setAttribute('students_count', 0);
                });
            });
        }

        return view('tenant.teacher.subjects.index', compact('subjects'));
    }
}
