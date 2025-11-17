<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\Carbon;

class SeedQuizData extends Command
{
    protected $signature = 'tenants:seed-quiz-data';
    protected $description = 'Seed sample quiz and quiz attempt data for all tenant schools';

    public function handle()
    {
        // Get all tenants from main database
        $tenants = \DB::connection('mysql')->table('schools')->get();

        foreach ($tenants as $tenant) {
            $this->info("Seeding quiz data for {$tenant->name}...");

            // Switch to tenant database
            config(['database.connections.tenant.database' => $tenant->database]);
            \DB::purge('tenant');

            // Get or create classes for this school
            $classes = SchoolClass::on('tenant')->where('school_id', $tenant->id)->get();

            if ($classes->isEmpty()) {
                $this->warn("No classes found for {$tenant->name}. Creating sample classes...");
                $classes = $this->createSampleClasses($tenant);
            }

            // Get all active users (we'll treat them as potential teachers/students)
            $users = User::on('tenant')->where('school_id', $tenant->id)
                ->where('is_active', true)
                ->get();

            if ($users->count() < 2) {
                $this->warn("Skipping {$tenant->name} - not enough users found.");
                continue;
            }

            // Split users for teachers (first 20%) and students (remaining 80%)
            $teacherCount = max(1, (int)($users->count() * 0.2));
            $teachers = $users->take($teacherCount);
            $students = $users->skip($teacherCount);

            // Create sample quizzes
            $quizCount = 0;
            $attemptCount = 0;

            foreach ($classes as $class) {
                $teacher = $teachers->random();

                // Create 3-5 quizzes per class
                for ($i = 0; $i < rand(3, 5); $i++) {
                    $quiz = Quiz::on('tenant')->create([
                        'school_id' => $tenant->id,
                        'teacher_id' => $teacher->id,
                        'class_id' => $class->id,
                        'title' => $this->getQuizTitle($i),
                        'description' => 'Sample quiz for ' . $class->name,
                        'duration_minutes' => rand(30, 90),
                        'total_marks' => rand(50, 100),
                        'start_at' => Carbon::now()->subDays(rand(7, 30)),
                        'end_at' => Carbon::now()->subDays(rand(1, 6))->setHour(rand(14, 17))->setMinute(rand(0, 59)),
                        'is_active' => true,
                        'allow_late_submission' => rand(0, 1) ? true : false,
                        'late_penalty_percent' => rand(5, 20),
                    ]);
                    $quizCount++;

                    // Create quiz attempts for 60-80% of students
                    $attemptingStudents = $students->random(rand((int)($students->count() * 0.6), (int)($students->count() * 0.8)));

                    foreach ($attemptingStudents as $student) {
                        // 30% chance of late submission
                        $isLate = rand(1, 100) <= 30;

                        $submittedAt = $quiz->end_at->copy()->addMinutes($isLate ? rand(5, 120) : rand(-30, 0));
                        $minutesLate = $isLate ? (int) $quiz->end_at->diffInMinutes($submittedAt) : 0;

                        $scoreAuto = rand(40, 95);
                        $scoreManual = rand(0, 5);
                        $scoreTotal = $scoreAuto + $scoreManual;

                        // Apply late penalty if applicable
                        if ($minutesLate > 0 && $quiz->late_penalty_percent > 0) {
                            $penalty = ($scoreTotal * $quiz->late_penalty_percent) / 100;
                            $scoreTotal = max(0, $scoreTotal - $penalty);
                        }

                        QuizAttempt::on('tenant')->create([
                            'school_id' => $tenant->id,
                            'quiz_id' => $quiz->id,
                            'student_id' => $student->id,
                            'started_at' => $submittedAt->copy()->subMinutes(rand(20, 60)),
                            'submitted_at' => $submittedAt,
                            'score_auto' => $scoreAuto,
                            'score_manual' => $scoreManual,
                            'score_total' => $scoreTotal,
                            'minutes_late' => $minutesLate,
                            'status' => 'graded',
                            'answers' => json_encode([]),
                            'feedback' => $minutesLate > 0 ? 'Late submission penalty applied' : null,
                        ]);
                        $attemptCount++;
                    }
                }
            }

            $this->info("✓ Seeded {$quizCount} quizzes and {$attemptCount} quiz attempts for {$tenant->name}");
        }

        $this->info("\nQuiz data seeded successfully for all tenant schools!");
    }

    private function createSampleClasses($tenant)
    {
        $classNames = ['Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6', 'Primary 7'];
        $classes = collect();

        foreach ($classNames as $name) {
            // Insert with only the columns that exist
            $class = \DB::connection('tenant')->table('classes')->insertGetId([
                'school_id' => $tenant->id,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Reload as model
            $classModel = SchoolClass::on('tenant')->find($class);
            $classes->push($classModel);
        }

        return $classes;
    }

    private function getQuizTitle($index)
    {
        $titles = [
            'Mid-Term Assessment',
            'Weekly Quiz',
            'Chapter Review Test',
            'Practice Exam',
            'Unit Test',
            'Monthly Assessment',
            'Skills Check',
            'Progress Quiz',
        ];

        return $titles[$index % count($titles)] . ' #' . ($index + 1);
    }
}
