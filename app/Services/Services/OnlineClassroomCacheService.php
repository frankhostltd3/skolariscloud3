<?php

namespace App\Services;

use App\Models\VirtualClass;
use App\Models\Exercise;
use App\Models\LearningMaterial;
use App\Models\ExerciseSubmission;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OnlineClassroomCacheService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 60; // 1 hour
    const STATS_CACHE_DURATION = 30; // 30 minutes
    const SHORT_CACHE_DURATION = 10; // 10 minutes

    /**
     * Get or cache teacher's virtual class statistics
     */
    public function getTeacherVirtualClassStats(int $teacherId): array
    {
        $cacheKey = "teacher_{$teacherId}_virtual_class_stats";

        return Cache::remember($cacheKey, self::STATS_CACHE_DURATION, function () use ($teacherId) {
            $teacherClassesQuery = VirtualClass::where('teacher_id', $teacherId);

            return [
                'total' => (clone $teacherClassesQuery)->count(),
                'scheduled' => (clone $teacherClassesQuery)->where('status', 'scheduled')->count(),
                'ongoing' => (clone $teacherClassesQuery)->where('status', 'ongoing')->count(),
                'completed' => (clone $teacherClassesQuery)->where('status', 'completed')->count(),
                'cancelled' => (clone $teacherClassesQuery)->where('status', 'cancelled')->count(),
                'total_participants' => (clone $teacherClassesQuery)
                    ->withCount('attendances')
                    ->get()
                    ->sum('attendances_count'),
                'total_hours' => round((clone $teacherClassesQuery)->sum('duration_minutes') / 60, 1),
            ];
        });
    }

    /**
     * Get or cache teacher's exercise statistics
     */
    public function getTeacherExerciseStats(int $teacherId): array
    {
        $cacheKey = "teacher_{$teacherId}_exercise_stats";

        return Cache::remember($cacheKey, self::STATS_CACHE_DURATION, function () use ($teacherId) {
            $teacherExercisesQuery = Exercise::where('teacher_id', $teacherId);
            $teacherSubmissionsQuery = ExerciseSubmission::whereHas('exercise', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });

            return [
                'total' => (clone $teacherExercisesQuery)->count(),
                'active' => (clone $teacherExercisesQuery)->where('due_date', '>=', Carbon::now())->count(),
                'pending_grading' => (clone $teacherSubmissionsQuery)->whereNull('score')->count(),
                'total_submissions' => (clone $teacherSubmissionsQuery)->count(),
                'graded_submissions' => (clone $teacherSubmissionsQuery)->whereNotNull('score')->count(),
                'average_score' => (clone $teacherSubmissionsQuery)->whereNotNull('score')->avg('grade') ?? 0,
            ];
        });
    }

    /**
     * Get or cache teacher's learning material statistics
     */
    public function getTeacherMaterialStats(int $teacherId): array
    {
        $cacheKey = "teacher_{$teacherId}_material_stats";

        return Cache::remember($cacheKey, self::STATS_CACHE_DURATION, function () use ($teacherId) {
            $teacherMaterialsQuery = LearningMaterial::where('teacher_id', $teacherId);

            return [
                'total' => (clone $teacherMaterialsQuery)->count(),
                'documents' => (clone $teacherMaterialsQuery)->where('type', 'document')->count(),
                'videos' => (clone $teacherMaterialsQuery)->where('type', 'video')->count(),
                'youtube' => (clone $teacherMaterialsQuery)->where('type', 'youtube')->count(),
                'links' => (clone $teacherMaterialsQuery)->where('type', 'link')->count(),
                'images' => (clone $teacherMaterialsQuery)->where('type', 'image')->count(),
                'audios' => (clone $teacherMaterialsQuery)->where('type', 'audio')->count(),
                'total_views' => (clone $teacherMaterialsQuery)->withCount('accesses')->get()->sum('accesses_count'),
            ];
        });
    }

    /**
     * Get or cache student's classroom statistics
     */
    public function getStudentClassroomStats(int $studentId, array $classIds): array
    {
        $cacheKey = "student_{$studentId}_classroom_stats";

        return Cache::remember($cacheKey, self::STATS_CACHE_DURATION, function () use ($studentId, $classIds) {
            // Virtual classes stats
            $classesQuery = VirtualClass::whereIn('class_id', $classIds);
            $totalClasses = (clone $classesQuery)->count();
            $attendedClasses = (clone $classesQuery)
                ->whereHas('attendances', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId)
                        ->whereIn('status', ['present', 'late']);
                })
                ->count();

            // Materials stats
            $materialsQuery = LearningMaterial::whereIn('class_id', $classIds);
            $totalMaterials = (clone $materialsQuery)->count();
            $accessedMaterials = (clone $materialsQuery)
                ->whereHas('accesses', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->count();

            // Assignments stats
            $submissionsQuery = ExerciseSubmission::where('student_id', $studentId)
                ->whereHas('exercise', function ($query) use ($classIds) {
                    $query->whereIn('class_id', $classIds);
                });
            
            $totalAssignments = Exercise::whereIn('class_id', $classIds)->count();
            $submittedAssignments = (clone $submissionsQuery)->count();
            $gradedAssignments = (clone $submissionsQuery)->whereNotNull('grade')->count();
            $averageGrade = (clone $submissionsQuery)->whereNotNull('grade')->avg('grade') ?? 0;

            return [
                'total_classes' => $totalClasses,
                'attended_classes' => $attendedClasses,
                'attendance_rate' => $totalClasses > 0 ? round(($attendedClasses / $totalClasses) * 100, 1) : 0,
                'total_materials' => $totalMaterials,
                'accessed_materials' => $accessedMaterials,
                'material_access_rate' => $totalMaterials > 0 ? round(($accessedMaterials / $totalMaterials) * 100, 1) : 0,
                'total_assignments' => $totalAssignments,
                'submitted_assignments' => $submittedAssignments,
                'graded_assignments' => $gradedAssignments,
                'average_grade' => round($averageGrade, 1),
            ];
        });
    }

    /**
     * Get or cache student's pending assignments
     */
    public function getStudentPendingAssignments(int $studentId, array $classIds, int $limit = 5): \Illuminate\Support\Collection
    {
        $cacheKey = "student_{$studentId}_pending_assignments_{$limit}";

        return Cache::remember($cacheKey, self::SHORT_CACHE_DURATION, function () use ($studentId, $classIds, $limit) {
            return Exercise::whereIn('class_id', $classIds)
                ->where('due_date', '>=', Carbon::now())
                ->whereDoesntHave('submissions', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->orderBy('due_date', 'asc')
                ->with([
                    'class:id,name',
                    'subject:id,name',
                    'teacher:id,name'
                ])
                ->select('id', 'title', 'class_id', 'subject_id', 'teacher_id', 'due_date', 'max_score')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get or cache student's upcoming virtual classes
     */
    public function getStudentUpcomingClasses(int $studentId, array $classIds, int $limit = 5): \Illuminate\Support\Collection
    {
        $cacheKey = "student_{$studentId}_upcoming_classes_{$limit}";

        return Cache::remember($cacheKey, self::SHORT_CACHE_DURATION, function () use ($classIds, $limit) {
            return VirtualClass::whereIn('class_id', $classIds)
                ->whereIn('status', ['scheduled', 'ongoing'])
                ->where('scheduled_at', '>=', Carbon::now()->subHours(3))
                ->orderBy('scheduled_at', 'asc')
                ->with([
                    'class:id,name,grade_level,section',
                    'subject:id,name,code',
                    'teacher:id,name,email'
                ])
                ->take($limit)
                ->get();
        });
    }

    /**
     * Clear teacher's cache
     */
    public function clearTeacherCache(int $teacherId): void
    {
        $keys = [
            "teacher_{$teacherId}_virtual_class_stats",
            "teacher_{$teacherId}_exercise_stats",
            "teacher_{$teacherId}_material_stats",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear student's cache
     */
    public function clearStudentCache(int $studentId): void
    {
        // Clear all cache keys that start with student ID
        $patterns = [
            "student_{$studentId}_classroom_stats",
            "student_{$studentId}_pending_assignments_*",
            "student_{$studentId}_upcoming_classes_*",
        ];

        foreach ($patterns as $pattern) {
            // For simple file cache, just forget the exact key
            // For Redis, you would use pattern matching
            if (strpos($pattern, '*') === false) {
                Cache::forget($pattern);
            } else {
                // For patterns, clear with known limits
                $basePat = str_replace('_*', '', $pattern);
                foreach ([3, 5, 10] as $limit) {
                    Cache::forget("{$basePat}_{$limit}");
                }
            }
        }
    }

    /**
     * Clear cache for specific exercise
     */
    public function clearExerciseCache(int $exerciseId): void
    {
        // Clear teacher stats when exercise is created/updated/deleted
        $exercise = Exercise::find($exerciseId);
        if ($exercise) {
            $this->clearTeacherCache($exercise->teacher_id);
            
            // Clear student caches for all students in the class
            $studentIds = $exercise->class->students()->pluck('id');
            foreach ($studentIds as $studentId) {
                $this->clearStudentCache($studentId);
            }
        }
    }

    /**
     * Clear cache for specific virtual class
     */
    public function clearVirtualClassCache(int $classId): void
    {
        $class = VirtualClass::find($classId);
        if ($class) {
            $this->clearTeacherCache($class->teacher_id);
            
            // Clear student caches for all students in the class
            $studentIds = $class->class->students()->pluck('id');
            foreach ($studentIds as $studentId) {
                $this->clearStudentCache($studentId);
            }
        }
    }

    /**
     * Clear cache for specific learning material
     */
    public function clearMaterialCache(int $materialId): void
    {
        $material = LearningMaterial::find($materialId);
        if ($material) {
            $this->clearTeacherCache($material->teacher_id);
            
            // Clear student caches for all students in the class
            $studentIds = $material->class->students()->pluck('id');
            foreach ($studentIds as $studentId) {
                $this->clearStudentCache($studentId);
            }
        }
    }

    /**
     * Clear cache when submission is created/updated
     */
    public function clearSubmissionCache(int $submissionId): void
    {
        $submission = ExerciseSubmission::with('exercise')->find($submissionId);
        if ($submission && $submission->exercise) {
            // Clear teacher's cache
            $this->clearTeacherCache($submission->exercise->teacher_id);
            
            // Clear student's cache
            $this->clearStudentCache($submission->student_id);
        }
    }

    /**
     * Clear all online classroom caches (use with caution)
     */
    public function clearAllCache(): void
    {
        // This would be used for system-wide cache clear
        // In production, use with caution as it clears everything
        Cache::flush();
    }
}
