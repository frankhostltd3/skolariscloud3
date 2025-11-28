<?php

namespace App\Services;

use App\Models\Assignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class OnlineClassroomCacheService
{
    protected int $cacheTtl;

    public function __construct(?int $cacheTtl = null)
    {
        $this->cacheTtl = $cacheTtl ?? 120;
    }

    public function getStudentPendingAssignments(int $studentId, array $classIds, int $limit = 3): Collection
    {
        if ($studentId <= 0 || empty($classIds) || !Schema::hasTable('assignments')) {
            return collect();
        }

        $cacheKey = sprintf(
            'student_%d_pending_assignments_%s_%d',
            $studentId,
            md5(json_encode($classIds)),
            $limit
        );

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($studentId, $classIds, $limit) {
            $query = Assignment::query()
                ->whereIn('class_id', $classIds)
                ->with(['subject'])
                ->limit($limit);

            if (Schema::hasColumn('assignments', 'published')) {
                $query->where('published', true);
            }

            if (Schema::hasColumn('assignments', 'due_date')) {
                $query->orderBy('due_date');
            } else {
                $query->latest();
            }

            if (Schema::hasTable('assignment_submissions')) {
                $query->whereDoesntHave('submissions', function ($q) use ($studentId) {
                    $q->where('student_id', $studentId);
                });
            }

            return $query->get();
        });
    }
}
