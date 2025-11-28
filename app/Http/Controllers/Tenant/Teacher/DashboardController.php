<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Academic\ClassRoom;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Academic\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Services\RegistrationPipelineService;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        $connection = $teacher->getConnectionName() ?? config('database.default', 'tenant');
        $schema = Schema::connection($connection);

        $hasClassTeacherColumn = tenant_column_exists('classes', 'class_teacher_id', $connection);
        $hasEnrollments = $schema->hasTable('enrollments');
        $hasStudentTable = $schema->hasTable('students');
        $hasGradesTable = $schema->hasTable('grades');

        $classSubjectTable = null;
        foreach (['class_subjects', 'class_subject'] as $candidate) {
            if (tenant_table_exists($candidate, $connection)) {
                $classSubjectTable = $candidate;
                break;
            }
        }

        $classRelations = [];

        if ($classSubjectTable) {
            $classRelations['subjects'] = function ($query) use ($teacher, $classSubjectTable) {
                $query->where($classSubjectTable . '.teacher_id', $teacher->id);
            };
        }

        if ($hasEnrollments || $hasStudentTable) {
            $classRelations[] = 'students';
        }

        // Get the teacher record (not just the user)
        $teacherRecord = \App\Models\Teacher::where('employee_record_id', $teacher->employee_record_id ?? null)
            ->orWhere('email', $teacher->email)
            ->first();

        if (!$teacherRecord) {
            // Fallback: try to find by user ID if teacher record doesn't exist
            $teacherRecord = \App\Models\Teacher::find($teacher->id);
        }

        // Classes where teacher is class teacher (stored with user ID)
        $classesAsClassTeacher = collect();
        if ($hasClassTeacherColumn) {
            try {
                $classesAsClassTeacher = ClassRoom::where('class_teacher_id', $teacher->id)
                    ->with($classRelations)
                    ->get();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($classSubjectTable && $this->isMissingTableException($e, $classSubjectTable)) {
                    unset($classRelations['subjects']);
                    $classSubjectTable = null;
                    $classesAsClassTeacher = ClassRoom::where('class_teacher_id', $teacher->id)
                        ->with($classRelations)
                        ->get();
                } else {
                    throw $e;
                }
            }
        }

        if (! ($hasEnrollments || $hasStudentTable)) {
            $classesAsClassTeacher->each(function ($class) {
                $class->setRelation('students', collect());
            });
        }

        if (! $classSubjectTable) {
            $classesAsClassTeacher->each(function ($class) {
                $class->setRelation('subjects', collect());
            });
        }

        // Classes where teacher teaches at least one subject (based on class_subjects.teacher_id)
        $subjectClassIds = collect();
        if ($classSubjectTable) {
            try {
                $subjectClassIds = DB::connection($connection)
                    ->table($classSubjectTable)
                    ->where('teacher_id', $teacher->id)
                    ->distinct()
                    ->pluck('class_id')
                    ->filter()
                    ->values();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($this->isMissingTableException($e, $classSubjectTable)) {
                    unset($classRelations['subjects']);
                    $classSubjectTable = null;
                    $subjectClassIds = collect();
                } else {
                    throw $e;
                }
            }
        }

        $allocatedClasses = collect();
        if ($subjectClassIds->isNotEmpty()) {
            try {
                $allocatedClasses = ClassRoom::whereIn('id', $subjectClassIds)
                    ->with($classRelations)
                    ->get();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($classSubjectTable && $this->isMissingTableException($e, $classSubjectTable)) {
                    unset($classRelations['subjects']);
                    $classSubjectTable = null;
                    $allocatedClasses = ClassRoom::whereIn('id', $subjectClassIds)
                        ->with($classRelations)
                        ->get();
                } else {
                    throw $e;
                }
            }
        }

        if (! ($hasEnrollments || $hasStudentTable)) {
            $allocatedClasses->each(function ($class) {
                $class->setRelation('students', collect());
            });
        }

        if (! $classSubjectTable) {
            $allocatedClasses->each(function ($class) {
                $class->setRelation('subjects', collect());
            });
        }

        // Merge all classes (remove duplicates)
        $allClasses = $classesAsClassTeacher
            ->merge($allocatedClasses)
            ->unique('id')
            ->values();

        // Subjects allocated to teacher (derived from class_subjects)
        $allSubjects = collect();
        if ($classSubjectTable) {
            try {
                $allSubjects = Subject::whereHas('classes', function ($query) use ($teacher, $classSubjectTable) {
                    $query->where($classSubjectTable . '.teacher_id', $teacher->id);
                    })
                    ->with('educationLevel')
                    ->get();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($this->isMissingTableException($e, $classSubjectTable)) {
                    $allSubjects = collect();
                } else {
                    throw $e;
                }
            }
        }

        // Today's timetable entries for this teacher
        $teacherIdForSchedule = $teacherRecord?->id ?? $teacher->id; // Prefer Teacher model ID if available
        $todayDow = now()->isoWeekday(); // 1=Mon..7=Sun
        $todaySchedule = \App\Models\TimetableEntry::where('teacher_id', $teacherIdForSchedule)
            ->where('day_of_week', $todayDow)
            ->with(['subject', 'class', 'stream'])
            ->orderBy('starts_at')
            ->get();

        // Calculate statistics
        $stats = [
            'classes' => $allClasses->count(),
            'subjects' => $allSubjects->count(),
            'students' => $allClasses->sum(function ($class) {
                return $class->students->count();
            }),
            'assignments' => 0, // Placeholder, implement if assignments model exists
            'attendance' => 0, // Placeholder, implement if attendance model exists
        ];

        // Recent grade entries by this teacher
        $recentGrades = $hasGradesTable
            ? Grade::where('teacher_id', $teacher->id)
                ->with(['student', 'subject', 'class'])
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        // Classes needing attention (low enrollment or no subjects)
        $classesNeedingAttention = $allClasses->filter(function ($class) {
            return $class->students->count() < 5 || $class->subjects->count() === 0;
        });

        // Upcoming assessments (grades that need to be entered)
        $subjectIds = $allSubjects->pluck('id');
        $classIds = $allClasses->pluck('id');

        $studentsNeedingGrades = collect();
        if ($hasEnrollments && $classIds->isNotEmpty() && $subjectIds->isNotEmpty()) {
            $studentsNeedingGrades = \App\Models\Academic\Enrollment::whereIn('class_id', $classIds)
                ->with(['student', 'class'])
                ->where('status', 'active')
                ->get();
        }

        $registrationInsights = app(RegistrationPipelineService::class)->teacherOverview($teacher, $allClasses);

        return view('tenant.teacher.dashboard', compact(
            'stats',
            'allClasses',
            'allSubjects',
            'todaySchedule',
            'recentGrades',
            'classesNeedingAttention',
            'studentsNeedingGrades',
            'registrationInsights'
        ));
    }

    /**
     * Determine if the query exception was caused by a missing table.
     */
    private function isMissingTableException(\Illuminate\Database\QueryException $exception, string $table): bool
    {
        $sqlState = $exception->getCode();
        $errorInfo = $exception->errorInfo ?? [];
        if ((! is_string($sqlState) || $sqlState === '') && is_array($errorInfo) && isset($errorInfo[0])) {
            $sqlState = $errorInfo[0];
        }

        $message = $exception->getMessage();

        return in_array($sqlState, ['42S02', '42S22'], true)
            || str_contains($message, 'Base table or view not found')
            || (str_contains($message, 'Unknown column') && str_contains($message, $table));
    }
}
