<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\QuizAttempt;
use App\Services\TenantDatabaseManager;

class RegradeQuizAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:regrade-quizzes {--school= : School ID or subdomain to process (optional, defaults to all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-grade all submitted quiz attempts across tenant schools';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schoolFilter = $this->option('school');

        $schoolsQuery = School::query();

        if ($schoolFilter) {
            $schoolsQuery->where(function ($q) use ($schoolFilter) {
                $q->where('id', $schoolFilter)
                  ->orWhere('subdomain', $schoolFilter);
            });
        }

        $schools = $schoolsQuery->get();

        if ($schools->isEmpty()) {
            $this->error('No schools found.');
            return 1;
        }

        $this->info("Processing {$schools->count()} school(s)...");

        $totalRegraded = 0;

        foreach ($schools as $school) {
            $this->line("Processing: {$school->name} ({$school->subdomain})");

            try {
                // Connect to tenant database
                app(TenantDatabaseManager::class)->connect($school);

                // Get all submitted attempts that need re-grading
                $attempts = QuizAttempt::whereNotNull('submitted_at')
                    ->whereNotNull('answers')
                    ->get();

                if ($attempts->isEmpty()) {
                    $this->line("  → No quiz attempts found.");
                    continue;
                }

                $regraded = 0;

                foreach ($attempts as $attempt) {
                    $oldScore = $attempt->score_auto ?? 0;
                    $newScore = $attempt->autoGrade();

                    if ($oldScore != $newScore) {
                        $this->line("  → Attempt #{$attempt->id}: {$oldScore} → {$newScore} points");
                    }

                    $regraded++;
                }

                $this->info("  → Re-graded {$regraded} attempt(s).");
                $totalRegraded += $regraded;

            } catch (\Exception $e) {
                $this->error("  → Error: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Done! Re-graded {$totalRegraded} quiz attempt(s) total.");

        return 0;
    }
}
