<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Academic\Enrollment;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Student as StudentProfile;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\ClassRoom;
use App\Models\TimetableEntry;
use App\Models\OnlineExam;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\MaterialAccess;
use App\Models\MessageRecipient;
use App\Services\OnlineClassroomCacheService;
use App\Services\RegistrationPipelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return $this->index();
    }

    public function index()
    {
        $student = Auth::user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $studentProfile = null;

        if (Schema::hasTable('students')) {
            try {
                $studentProfile = StudentProfile::where('email', $student->email)->first();
            } catch (\Throwable $e) {
                $studentProfile = null;
            }
        }

        // Get student's current enrollment
        $enrollment = null;
        if ($currentAcademicYear) {
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->with(['classroom', 'classroom.subjects', 'classroom.teacher'])
                ->first();
        }

        // Calculate statistics
        $stats = [
            'total_subjects' => 0,
            'enrolled_class' => 'Not Enrolled',
            'class_teacher' => 'N/A',
            'total_grades' => 0
        ];

        $mySubjects = collect();
        $recentGrades = collect();

        $classIds = $student->activeEnrollments()->pluck('class_id')->filter()->unique()->values();

        if ($studentProfile && Schema::hasTable('student_subject')) {
            try {
                $subjectQuery = $studentProfile->subjects();

                if (Schema::hasColumn('student_subject', 'status')) {
                    $subjectQuery->where(function ($q) {
                        $q->whereNull('student_subject.status')
                            ->orWhereIn('student_subject.status', ['active', 'completed']);
                    });
                }

                if ($currentAcademicYear && Schema::hasColumn('student_subject', 'academic_year')) {
                    $subjectQuery->where(function ($q) use ($currentAcademicYear) {
                        $q->whereNull('student_subject.academic_year')
                            ->orWhere('student_subject.academic_year', $currentAcademicYear->name);
                    });
                }

                $mySubjects = $subjectQuery->orderBy('subjects.name')->get()->unique('id')->values();
            } catch (\Throwable $e) {
                $mySubjects = collect();
            }
        }

        if ($enrollment && $enrollment->classroom) {
            if ($mySubjects->isEmpty()) {
                // Fallback to class allocation when no explicit student subjects exist
                $mySubjects = $enrollment->classroom->activeSubjects()->get()->unique('id')->values();
            }

            $stats['enrolled_class'] = $enrollment->classroom->name;
            $stats['class_teacher'] = $enrollment->classroom->teacher->name ?? 'Not Assigned';

            if ($classIds->isEmpty()) {
                $classIds = collect([$enrollment->class_id]);
            }

            // Cache recent grades for a short period to keep dashboard snappy
            $recentGrades = Cache::remember(
                "student_{$student->id}_recent_grades",
                120,
                function () use ($student, $mySubjects) {
                    return Grade::where('student_id', $student->id)
                        ->when($mySubjects->isNotEmpty(), function ($q) use ($mySubjects) {
                            $q->whereIn('subject_id', $mySubjects->pluck('id'));
                        })
                        ->with(['subject', 'semester'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                }
            );

            $stats['total_grades'] = Cache::remember(
                "student_{$student->id}_total_grades",
                300,
                function () use ($student, $mySubjects) {
                    return Grade::where('student_id', $student->id)
                        ->when($mySubjects->isNotEmpty(), function ($q) use ($mySubjects) {
                            $q->whereIn('subject_id', $mySubjects->pluck('id'));
                        })
                        ->count();
                }
            );
        }

    $stats['total_subjects'] = $mySubjects->count();

        // Calculate average grade
        $averageGrade = 0;
        if ($recentGrades->count() > 0) {
            $averageGrade = $recentGrades->avg('grade');
        }

        // Get grade distribution
        $gradeDistribution = [];
        if ($enrollment && $enrollment->classroom) {
            $allGrades = Cache::remember(
                "student_{$student->id}_all_grades",
                300,
                function () use ($student, $mySubjects) {
                    return Grade::where('student_id', $student->id)
                        ->when($mySubjects->isNotEmpty(), function ($q) use ($mySubjects) {
                            $q->whereIn('subject_id', $mySubjects->pluck('id'));
                        })
                        ->get();
                }
            );

            $gradeDistribution = [
                'A' => $allGrades->where('grade', '>=', 90)->count(),
                'B' => $allGrades->whereBetween('grade', [80, 89])->count(),
                'C' => $allGrades->whereBetween('grade', [70, 79])->count(),
                'D' => $allGrades->whereBetween('grade', [60, 69])->count(),
                'F' => $allGrades->where('grade', '<', 60)->count(),
            ];
        }

        // Upcoming section: next class today, pending assignments, scheduled quizzes/exams
        $upcoming = [
            'next_class' => null,
            'assignments' => collect(),
            'quizzes' => collect(),
            'exams' => collect(),
        ];

        if ($enrollment && $enrollment->classroom) {
            $classId = $enrollment->class_id;
            $classIdList = $classIds->isNotEmpty() ? $classIds->all() : [$classId];
            /** @var OnlineClassroomCacheService $classroomCache */
            $classroomCache = app(OnlineClassroomCacheService::class);

            // Next class today from timetable
            $upcoming['next_class'] = Cache::remember(
                "student_{$student->id}_next_class",
                60,
                function () use ($classId) {
                    $dow = now()->isoWeekday();
                    $nowTime = now()->format('H:i:s');
                    return TimetableEntry::with(['subject', 'class', 'stream'])
                        ->where('class_id', $classId)
                        ->where('day_of_week', $dow)
                        ->where('starts_at', '>=', $nowTime)
                        ->orderBy('starts_at', 'asc')
                        ->first();
                }
            );

            // Upcoming assignments (exercises) not yet submitted
            $upcoming['assignments'] = $classroomCache->getStudentPendingAssignments($student->id, $classIdList, 3);

            // Upcoming quizzes assigned to student's class
            $upcoming['quizzes'] = Cache::remember(
                "student_{$student->id}_upcoming_quizzes",
                120,
                function () use ($classId) {
                    $connection = Schema::connection('tenant');
                    $hasPivot = $connection->hasTable('quiz_class');
                    $hasClassColumn = $connection->hasColumn('quizzes', 'class_id');
                    $availableFromColumn = $connection->hasColumn('quizzes', 'available_from')
                        ? 'available_from'
                        : ($connection->hasColumn('quizzes', 'start_at') ? 'start_at' : null);
                    $availableUntilColumn = $connection->hasColumn('quizzes', 'available_until')
                        ? 'available_until'
                        : ($connection->hasColumn('quizzes', 'end_at') ? 'end_at' : null);

                    $query = Quiz::query()->with(['teacher', 'subject']);
                    $query->where(function ($q) {
                        $q->where('is_published', true)
                            ->orWhere('status', 'published');
                    });

                    $query->where(function ($q) use ($classId, $hasPivot, $hasClassColumn) {
                        if ($hasPivot) {
                            $q->whereHas('classes', function ($sub) use ($classId) {
                                $sub->where('classes.id', $classId);
                            });
                            if ($hasClassColumn) {
                                $q->orWhere('class_id', $classId);
                            }
                        } elseif ($hasClassColumn) {
                            $q->where('class_id', $classId);
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    });

                    if ($availableFromColumn) {
                        $query->where(function ($q) use ($availableFromColumn) {
                            $q->whereNull($availableFromColumn)
                                ->orWhere($availableFromColumn, '>=', now()->subDay());
                        });
                    }

                    if ($availableUntilColumn) {
                        $query->where(function ($q) use ($availableUntilColumn) {
                            $q->whereNull($availableUntilColumn)
                                ->orWhere($availableUntilColumn, '>=', now());
                        });
                    }

                    if ($availableFromColumn) {
                        $query->orderBy($availableFromColumn, 'asc');
                    } elseif ($availableUntilColumn) {
                        $query->orderBy($availableUntilColumn, 'asc');
                    } else {
                        $query->orderBy('created_at', 'asc');
                    }

                    return $query->limit(3)->get();
                }
            );

            // Upcoming online exams
            $upcoming['exams'] = Cache::remember(
                "student_{$student->id}_upcoming_exams",
                120,
                function () use ($classId) {
                    $table = (new OnlineExam())->getTable();
                    $startColumn = Schema::hasColumn($table, 'start_time') ? 'start_time' : (Schema::hasColumn($table, 'starts_at') ? 'starts_at' : null);
                    $endColumn = Schema::hasColumn($table, 'end_time') ? 'end_time' : (Schema::hasColumn($table, 'ends_at') ? 'ends_at' : null);
                    $examDateColumn = Schema::hasColumn($table, 'exam_date') ? 'exam_date' : null;
                    $classColumn = Schema::hasColumn($table, 'class_id') ? 'class_id' : (Schema::hasColumn($table, 'class_room_id') ? 'class_room_id' : null);

                    $select = ["{$table}.*"];
                    if ($startColumn === 'starts_at' && !Schema::hasColumn($table, 'start_time')) {
                        $select[] = "{$table}.{$startColumn} as start_time";
                    }
                    if ($endColumn === 'ends_at' && !Schema::hasColumn($table, 'end_time')) {
                        $select[] = "{$table}.{$endColumn} as end_time";
                    }

                    $query = OnlineExam::select($select)->with(['class', 'subject']);

                    if (Schema::hasColumn($table, 'status')) {
                        $query->whereIn('status', ['scheduled', 'active']);
                    }

                    if ($classColumn) {
                        $query->where($classColumn, $classId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }

                    if ($startColumn) {
                        $query->where(function ($q) use ($startColumn) {
                            $q->whereNull($startColumn)->orWhere($startColumn, '>=', now());
                        });
                    } elseif ($examDateColumn) {
                        $query->where(function ($q) use ($examDateColumn) {
                            $q->whereNull($examDateColumn)->orWhereDate($examDateColumn, '>=', now()->toDateString());
                        });
                    }

                    if ($startColumn) {
                        $query->orderBy($startColumn, 'asc');
                    } elseif ($examDateColumn) {
                        $query->orderBy($examDateColumn, 'asc');
                    } else {
                        $query->orderBy('created_at', 'asc');
                    }

                    return $query
                        ->limit(3)
                        ->get();
                }
            );
        }

        // Unread counts (messages and notifications)
        $unreadCounts = Cache::remember(
            "student_{$student->id}_unread_counts",
            60,
            function () use ($student) {
                $unreadMessages = MessageRecipient::forRecipient($student->id)->unread()->count();
                $unreadNotifications = 0;
                try {
                    $unreadNotifications = $student->unreadNotifications()->count();
                } catch (\Throwable $e) {
                    $unreadNotifications = 0;
                }
                return [
                    'messages' => $unreadMessages,
                    'notifications' => $unreadNotifications,
                ];
            }
        );

        // Continue where you left off
        $continue = [
            'material' => null,
            'quiz_attempt' => null,
        ];
        try {
            $continue['material'] = Cache::remember(
                "student_{$student->id}_last_material",
                120,
                function () use ($student) {
                    // Model/table columns may vary across tenants; use controller's pattern (accessed_at)
                    $lastAccess = MaterialAccess::where('student_id', $student->id)
                        ->orderByDesc('accessed_at')
                        ->first();
                    return $lastAccess?->material?->load(['class', 'subject']);
                }
            );
        } catch (\Throwable $e) {
            // ignore
        }
        try {
            $continue['quiz_attempt'] = Cache::remember(
                "student_{$student->id}_last_quiz_attempt",
                120,
                function () use ($student) {
                    return QuizAttempt::with('quiz')
                        ->where('student_id', $student->id)
                        ->whereNull('submitted_at')
                        ->orderByDesc('started_at')
                        ->first();
                }
            );
        } catch (\Throwable $e) {
            // ignore
        }

        $registrationTimeline = app(RegistrationPipelineService::class)->studentTimeline($student, [
            'class_name' => $stats['enrolled_class'] ?? null,
        ]);

        return view('tenant.student.dashboard', compact(
            'stats',
            'mySubjects',
            'recentGrades',
            'averageGrade',
            'gradeDistribution',
            'enrollment',
            'currentAcademicYear',
            'upcoming',
            'unreadCounts',
            'continue',
            'registrationTimeline'
        ));
    }
}
