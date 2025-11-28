<?php

namespace App\Services;

use App\Models\OnlineExam;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ExamWindowAutomationService
{
    /**
     * Synchronize exam statuses for the current tenant.
     *
     * @return array{activated: Collection<int, OnlineExam>, completed: Collection<int, OnlineExam>}
     */
    public function sync(?Carbon $moment = null): array
    {
        $moment ??= now();

        $activated = $this->activateDueExams($moment);
        $completed = $this->completeExpiredExams($moment);

        return compact('activated', 'completed');
    }

    /**
     * Move scheduled exams into an active state when their window opens.
     */
    protected function activateDueExams(Carbon $moment): Collection
    {
        $exams = OnlineExam::query()
            ->where('approval_status', 'approved')
            ->where('status', 'scheduled')
            ->whereIn('activation_mode', ['schedule', 'auto'])
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', $moment)
            ->orderBy('starts_at')
            ->get();

        $activated = collect();

        foreach ($exams as $exam) {
            if (! $exam->shouldActivate($moment)) {
                continue;
            }

            $exam->forceFill([
                'status' => 'active',
                'activated_at' => $exam->activated_at ?? $moment,
            ])->save();

            $activated->push($exam->fresh('teacher'));
        }

        return $activated;
    }

    /**
     * Close exams whose end time has elapsed.
     */
    protected function completeExpiredExams(Carbon $moment): Collection
    {
        $exams = OnlineExam::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $moment)
            ->orderBy('ends_at')
            ->get();

        $completed = collect();

        foreach ($exams as $exam) {
            if (! $exam->shouldComplete($moment)) {
                continue;
            }

            $exam->forceFill([
                'status' => 'completed',
                'completed_at' => $exam->completed_at ?? $moment,
            ])->save();

            $completed->push($exam->fresh('teacher'));
        }

        return $completed;
    }
}
