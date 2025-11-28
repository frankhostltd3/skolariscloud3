<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Academic\ClassRoom;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ClassController extends Controller
{
    /**
     * Display teacher's assigned classes.
     */
    public function index()
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        // Class ownership based on class_teacher_id
        // Classes where teacher is class teacher OR assigned to any subject in the class
        $classesQuery = ClassRoom::query();

        $classesQuery->where(function ($query) use ($teacher, $classSubjectTable, $hasClassTeacherColumn) {
            $applied = false;

            if ($hasClassTeacherColumn) {
                $query->where('class_teacher_id', $teacher->id);
                $applied = true;
            }

            if ($classSubjectTable) {
                $constraint = function ($subjectQuery) use ($teacher, $classSubjectTable) {
                    $subjectQuery->where($classSubjectTable . '.teacher_id', $teacher->id);
                };

                if ($applied) {
                    $query->orWhereHas('subjects', $constraint);
                } else {
                    $query->whereHas('subjects', $constraint);
                }

                $applied = true;
            }

            if (! $applied) {
                $query->whereRaw('0 = 1');
            }
        });

        $relations = [];

        if ($classSubjectTable) {
            $relations['subjects'] = function ($query) use ($teacher, $classSubjectTable) {
                $query->where($classSubjectTable . '.teacher_id', $teacher->id);
            };
        }

        $classesQuery->with($relations);

        $withCounts = [];

        if ($classSubjectTable) {
            $withCounts['subjects as subjects_count'] = function ($query) use ($teacher, $classSubjectTable) {
                $query->where($classSubjectTable . '.teacher_id', $teacher->id);
            };
        }

        if ($hasEnrollments) {
            $withCounts['students as students_count'] = function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            };
        } elseif ($hasStudentTable) {
            $withCounts['students as students_count'] = function ($query) {
                // Default count without additional filtering for legacy schema
            };
        }

        if (! empty($withCounts)) {
            $classesQuery->withCount($withCounts);
        }

        $classes = $classesQuery->get();

        if (! $hasEnrollments && ! $hasStudentTable) {
            $classes->each(function ($class) {
                $class->setAttribute('students_count', 0);
            });
        }

        if (! $classSubjectTable) {
            $classes->each(function ($class) {
                $class->setAttribute('subjects_count', 0);
                $class->setRelation('subjects', collect());
            });
        }

        return view('tenant.teacher.classes.index', compact('classes'));
    }

    /**
     * Show details of a specific class.
     */
    public function show(ClassRoom $class)
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        // Ensure teacher can only view their own classes
        $isClassTeacher = $hasClassTeacherColumn && $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $classSubjectTable
            ? $class->subjects()->where($classSubjectTable . '.teacher_id', $teacher->id)->exists()
            : false;
        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this class.');
        }

        $relations = [];

        if ($classSubjectTable) {
            $relations['subjects'] = function ($query) use ($teacher, $classSubjectTable) {
                $query->where($classSubjectTable . '.teacher_id', $teacher->id);
            };
        }

        if ($hasEnrollments || $hasStudentTable) {
            $relations['students'] = function ($query) use ($hasEnrollments) {
                if ($hasEnrollments) {
                    $query->where(function ($subQuery) {
                        $subQuery->where('enrollments.status', 'active')
                            ->orWhereNull('enrollments.status');
                    });
                }
            };
        }

        $class->load($relations);

        if (! ($hasEnrollments || $hasStudentTable)) {
            $class->setRelation('students', collect());
        }

        if (! $classSubjectTable) {
            $class->setRelation('subjects', collect());
        }

        // Get recent grades for this class
        $recentGrades = Grade::where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->with(['student', 'subject'])
            ->latest()
            ->limit(10)
            ->get();

        // Get attendance statistics (placeholder)
        $attendanceStats = [
            'total_students' => $class->students->count(),
            'present_today' => 0,
            'absent_today' => 0,
            'attendance_rate' => 0
        ];

        // Get students with low grades
        $studentsWithLowGrades = $this->getStudentsWithLowGrades($class, $teacher->id);

        return view('tenant.teacher.classes.show', compact(
            'class',
            'recentGrades',
            'attendanceStats',
            'studentsWithLowGrades'
        ));
    }

    /**
     * Show students in a class.
     */
    public function students(ClassRoom $class)
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        $isClassTeacher = $hasClassTeacherColumn && $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $classSubjectTable
            ? $class->subjects()->where($classSubjectTable . '.teacher_id', $teacher->id)->exists()
            : false;
        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this class.');
        }

        if (! ($hasEnrollments || $hasStudentTable)) {
            $students = collect();
        } else {
            $students = $class->students()
                ->with(['grades' => function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                }])
                ->get();
        }

        // Calculate average percentage for each student (use model accessor percentage)
        $students->each(function ($student) {
            $grades = $student->grades;
            $student->averageGrade = $grades->count() > 0
                ? $grades->avg(function ($g) {
                    return $g->percentage;
                })
                : null;
            $student->gradeCount = $grades->count();
        });

        return view('tenant.teacher.classes.students', compact('class', 'students'));
    }

    /**
     * Show grades for a specific class.
     */
    public function grades(ClassRoom $class, Request $request)
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $classSubjectTable = $this->resolveClassSubjectTable($connection);

        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $isClassTeacher = $hasClassTeacherColumn && $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $classSubjectTable
            ? $class->subjects()->where($classSubjectTable . '.teacher_id', $teacher->id)->exists()
            : false;
        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this class.');
        }

        // Get teacher's subjects for this class
        if ($classSubjectTable) {
            $subjects = $class->subjects()
                ->where($classSubjectTable . '.teacher_id', $teacher->id)
                ->get();
        } else {
            $subjects = collect();
        }

        $query = Grade::where('class_id', $class->id)
            ->where('teacher_id', $teacher->id)
            ->with(['student', 'subject', 'semester']);

        // Filter by subject if selected
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by assessment type if selected
        if ($request->filled('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        // Date range filters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        if ($startDate && $endDate) {
            $query->whereBetween('assessment_date', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } elseif ($startDate) {
            $query->whereDate('assessment_date', '>=', \Carbon\Carbon::parse($startDate)->toDateString());
        } elseif ($endDate) {
            $query->whereDate('assessment_date', '<=', \Carbon\Carbon::parse($endDate)->toDateString());
        }

        // Clone query for summary before pagination
        $summaryQuery = (clone $query);
        $summaryCount = $summaryQuery->count();
        // Compute average percentage safely in SQL without relying on a DB column
        if ($summaryCount > 0) {
            $avgRow = (clone $query)
                ->selectRaw(Grade::percentageAvgExpression() . ' AS avg_pct')
                ->first();
            $summaryAvg = $avgRow && $avgRow->avg_pct !== null ? (float) $avgRow->avg_pct : 0.0;
        } else {
            $summaryAvg = 0.0;
        }

        $grades = $query->latest('assessment_date')->paginate(20)->appends($request->query());

        return view('tenant.teacher.classes.grades', compact(
            'class',
            'grades',
            'subjects',
            'summaryCount',
            'summaryAvg'
        ));
    }

    /**
     * Get students with low grades in the class.
     */
    private function getStudentsWithLowGrades($class, $teacherId)
    {
        $percentageExpr = Grade::percentageValueExpression();

        $connection = $class->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        if (! ($schema->hasTable('enrollments') || $schema->hasTable('students'))) {
            return collect();
        }

        return $class->students()
            ->select('users.*')
            // Students who have any grade below threshold in this class by this teacher
            ->whereExists(function ($q) use ($teacherId, $class, $percentageExpr) {
                $q->from('grades')
                    ->whereColumn('users.id', 'grades.student_id')
                    ->where('grades.teacher_id', $teacherId)
                    ->where('grades.class_id', $class->id)
                    ->whereRaw($percentageExpr . ' < 70');
            })
            // Average grade percentage as a subselect
            ->selectSub(function ($q) use ($teacherId, $class, $percentageExpr) {
                $q->from('grades')
                    ->whereColumn('users.id', 'grades.student_id')
                    ->where('grades.teacher_id', $teacherId)
                    ->where('grades.class_id', $class->id)
                    ->selectRaw('AVG(' . $percentageExpr . ')');
            }, 'average_grade')
            ->having('average_grade', '<', 70)
            ->limit(5)
            ->get();
    }

    private function resolveClassSubjectTable(string $connection): ?string
    {
        foreach (['class_subjects', 'class_subject'] as $table) {
            if (tenant_table_exists($table, $connection)) {
                return $table;
            }
        }

        return null;
    }
}
