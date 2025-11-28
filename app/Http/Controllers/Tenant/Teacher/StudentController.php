<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Academic\ClassRoom;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class StudentController extends Controller
{
    /**
     * Show a list of the teacher's classes with quick links to students.
     */
    public function index()
    {
        $teacher = Auth::user();

        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);

        $classSubjectTable = null;
        foreach (['class_subjects', 'class_subject'] as $candidate) {
            if (tenant_table_exists($candidate, $connection)) {
                $classSubjectTable = $candidate;
                break;
            }
        }

        $classesQuery = ClassRoom::query();
        $relations = [];

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

        $constraintApplied = false;

        if ($hasClassTeacherColumn) {
            $classesQuery->where('class_teacher_id', $teacher->id);
            $constraintApplied = true;
        }

        if ($classSubjectTable) {
            $method = $constraintApplied ? 'orWhereHas' : 'whereHas';
            $classesQuery->{$method}('subjects', function ($query) use ($teacher, $classSubjectTable) {
                $query->where($classSubjectTable . '.teacher_id', $teacher->id);
            });
            $constraintApplied = true;
        }

        if (! $constraintApplied) {
            // No safe constraint available for this tenant schema
            $classesQuery->whereRaw('0 = 1');
        }

        $classesQuery->with($relations);

        $withCounts = [];

        if ($hasEnrollments) {
            $withCounts['students as students_count'] = function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('enrollments.status', 'active')
                        ->orWhereNull('enrollments.status');
                });
            };
        } elseif ($hasStudentTable) {
            $withCounts['students as students_count'] = function ($query) {
                // Legacy schema without enrollments table
            };
        }

        if ($classSubjectTable) {
            $withCounts['subjects as subjects_count'] = function ($query) use ($teacher, $classSubjectTable) {
                $query->where($classSubjectTable . '.teacher_id', $teacher->id);
            };
        }

        if (! empty($withCounts)) {
            $classesQuery->withCount($withCounts);
        }

        $classes = $classesQuery->get();

        if (! ($hasEnrollments || $hasStudentTable)) {
            $classes->each(function ($class) {
                $class->setRelation('students', collect());
                $class->setAttribute('students_count', 0);
            });
        }

        if (! $classSubjectTable) {
            $classes->each(function ($class) {
                $class->setRelation('subjects', collect());
                $class->setAttribute('subjects_count', 0);
            });
        }

        return view('tenant.teacher.students.index', compact('classes'));
    }

    /**
     * Show a student profile tailored for teachers.
     */
    public function show(User $student)
    {
        $teacher = Auth::user();

        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasGradesTable = $schema->hasTable('grades');
        $hasAttendanceTable = $schema->hasTable('attendance');
        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);

        $classSubjectTable = null;
        foreach (['class_subjects', 'class_subject'] as $candidate) {
            if (tenant_table_exists($candidate, $connection)) {
                $classSubjectTable = $candidate;
                break;
            }
        }

        // Authorization: teacher must be class teacher or subject teacher for the student's class.
        // Prefer class_id from route/query when provided to avoid coupling to "current" enrollment only.
        $classId = request()->query('class_id');
        if ($classId) {
            $class = ClassRoom::find($classId);
            abort_if(!$class, 404, 'Class not found');
            // Ensure the student actually belongs to this class
            if ($hasEnrollments) {
                $belongsToClass = $student->enrollments()->where('class_id', $class->id)->exists();
                abort_if(!$belongsToClass, 403, 'Student is not enrolled in this class.');
            }
        } else {
            if ($hasEnrollments) {
                $enrollment = $student->currentEnrollment()->with('class')->first();
                abort_if(!$enrollment, 404, 'Student has no current enrollment');
                $class = $enrollment->class;
            } else {
                abort(400, 'Class context is required for this student.');
            }
        }

        $isClassTeacher = $hasClassTeacherColumn && $class && $class->class_teacher_id === $teacher->id;
        $isSubjectTeacher = $classSubjectTable && $class
            ? $class->subjects()->where($classSubjectTable . '.teacher_id', $teacher->id)->exists()
            : false;

        if (!$isClassTeacher && !$isSubjectTeacher) {
            abort(403, 'You are not authorized to view this student.');
        }

        // Load recent grades by this teacher and attendance summary
        $student->load(['enrollments.class', 'studentSubjects']);

        $recentGrades = $hasGradesTable
            ? Grade::where('student_id', $student->id)
                ->where('class_id', $class->id)
                ->where('teacher_id', $teacher->id)
                ->with(['subject'])
                ->latest('assessment_date')
                ->limit(10)
                ->get()
            : collect();

        if ($hasAttendanceTable) {
            $attendanceSummary = [
                'present' => \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->whereHas('attendance', function ($query) use ($class) {
                        $query->where('class_id', $class->id);
                    })
                    ->where('status', 'present')
                    ->count(),
                'absent' => \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->whereHas('attendance', function ($query) use ($class) {
                        $query->where('class_id', $class->id);
                    })
                    ->where('status', 'absent')
                    ->count(),
                'late' => \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->whereHas('attendance', function ($query) use ($class) {
                        $query->where('class_id', $class->id);
                    })
                    ->where('status', 'late')
                    ->count(),
                'excused' => \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->whereHas('attendance', function ($query) use ($class) {
                        $query->where('class_id', $class->id);
                    })
                    ->where('status', 'excused')
                    ->count(),
            ];
        } else {
            $attendanceSummary = [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
            ];
        }

        return view('tenant.teacher.students.show', compact('student', 'class', 'recentGrades', 'attendanceSummary'));
    }
}
