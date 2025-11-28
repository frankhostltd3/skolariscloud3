<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Notifications\ExamReviewDecisionNotification;
use App\Services\ExamWindowAutomationService;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SyncExamWindows extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenants:sync-exams {--tenant= : Limit the sync to a specific tenant ID or subdomain}';

    /**
     * The console command description.
     */
    protected $description = 'Auto-activate and close online exams according to their schedules across all tenants';

    public function __construct(
        protected TenantDatabaseManager $manager,
        protected ExamWindowAutomationService $automationService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $schools = $this->resolveTargetSchools();

        if ($schools->isEmpty()) {
            $this->warn('No matching tenants found.');
            return self::SUCCESS;
        }

        $totalActivated = 0;
        $totalCompleted = 0;
        $now = Carbon::now();

        foreach ($schools as $school) {
            $this->components->task("Syncing {$school->name}", function () use ($school, $now, &$totalActivated, &$totalCompleted): void {
                $result = $this->manager->runFor($school, function () use ($now) {
                    return $this->automationService->sync($now);
                });

                $activated = $result['activated'];
                $completed = $result['completed'];

                $totalActivated += $activated->count();
                $totalCompleted += $completed->count();

                $activated->each(function ($exam): void {
                    if ($exam->teacher) {
                        $exam->teacher->notify(new ExamReviewDecisionNotification($exam, 'activated'));
                    }
                });

                $completed->each(function ($exam): void {
                    if ($exam->teacher) {
                        $exam->teacher->notify(new ExamReviewDecisionNotification($exam, 'completed'));
                    }
                });
            });
        }

        $this->newLine();
        $this->info('Exam windows synchronized successfully.');
        $this->line("Activated: {$totalActivated}");
        $this->line("Completed: {$totalCompleted}");

        return self::SUCCESS;
    }

    protected function resolveTargetSchools()
    {
        $query = School::query()->orderBy('id');

        if ($target = $this->option('tenant')) {
            $query->where(function ($inner) use ($target) {
                $inner->where('id', $target)
                    ->orWhere('subdomain', $target)
                    ->orWhere('domain', $target);
            });
        }

        return $query->get();
    }
}
