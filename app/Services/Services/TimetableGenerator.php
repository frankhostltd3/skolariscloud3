<?php

namespace App\Services;

use App\Models\TimetableEntry;
use App\Models\SchoolClass;
use App\Models\ClassStream;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimetableConstraint;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimetableGenerator
{
    protected $constraints = [];
    protected $timeSlots = [];
    protected $rooms = [];
    protected $maxGenerations = 1000;
    protected $populationSize = 50;
    protected $mutationRate = 0.1;

    public function __construct()
    {
        $this->initializeTimeSlots();
        $this->initializeRooms();
        $this->loadConstraints();
    }

    /**
     * Generate timetable for a class
     */
    public function generateForClass(SchoolClass $class, array $options = []): array
    {
        $subjects = $this->getClassSubjects($class);
        $teachers = $this->getAvailableTeachers($subjects);

        $constraints = [
            'max_periods_per_day' => $options['max_periods_per_day'] ?? 8,
            'max_periods_per_week' => $options['max_periods_per_week'] ?? 40,
            'break_after_periods' => $options['break_after_periods'] ?? 4,
            'lunch_break_slot' => $options['lunch_break_slot'] ?? 4,
            'working_days' => $options['working_days'] ?? [1, 2, 3, 4, 5], // Mon-Fri
        ];

        return $this->generateTimetable($class, $subjects, $teachers, $constraints);
    }

    /**
     * Generate timetable using genetic algorithm approach
     */
    protected function generateTimetable(SchoolClass $class, Collection $subjects, Collection $teachers, array $constraints): array
    {
        $population = $this->initializePopulation($class, $subjects, $teachers, $constraints);

        for ($generation = 0; $generation < $this->maxGenerations; $generation++) {
            $fitnessScores = $this->evaluatePopulation($population, $constraints);

            // Check if we have a perfect solution
            $bestFitness = max($fitnessScores);
            if ($bestFitness >= 1.0) {
                $bestIndex = array_search($bestFitness, $fitnessScores);
                return $population[$bestIndex];
            }

            // Create new population
            $population = $this->evolvePopulation($population, $fitnessScores);
        }

        // Return best solution found
        $fitnessScores = $this->evaluatePopulation($population, $constraints);
        $bestIndex = array_search(max($fitnessScores), $fitnessScores);
        return $population[$bestIndex];
    }

    /**
     * Initialize population of potential timetables
     */
    protected function initializePopulation(SchoolClass $class, Collection $subjects, Collection $teachers, array $constraints): array
    {
        $population = [];

        for ($i = 0; $i < $this->populationSize; $i++) {
            $timetable = $this->createRandomTimetable($class, $subjects, $teachers, $constraints);
            $population[] = $timetable;
        }

        return $population;
    }

    /**
     * Create a random timetable respecting basic constraints
     */
    protected function createRandomTimetable(SchoolClass $class, Collection $subjects, Collection $teachers, array $constraints): array
    {
        $timetable = [];
        $subjectAssignments = $this->distributeSubjectPeriods($subjects, $constraints);

        foreach ($constraints['working_days'] as $day) {
            $daySchedule = [];
            $availableSlots = $this->getAvailableSlotsForDay($day, $constraints);

            foreach ($subjectAssignments as $subjectId => $periodsNeeded) {
                $subject = $subjects->find($subjectId);
                $teacher = $this->getRandomTeacherForSubject($subject, $teachers);

                for ($p = 0; $p < $periodsNeeded; $p++) {
                    if (empty($availableSlots)) break;

                    $slotIndex = array_rand($availableSlots);
                    $slot = $availableSlots[$slotIndex];
                    unset($availableSlots[$slotIndex]);

                    $daySchedule[] = [
                        'day_of_week' => $day,
                        'starts_at' => $slot['start'],
                        'ends_at' => $slot['end'],
                        'class_id' => $class->id,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacher ? $teacher->id : null,
                        'room' => $this->getRandomRoom(),
                        'notes' => null,
                    ];
                }
            }

            $timetable = array_merge($timetable, $daySchedule);
        }

        return $timetable;
    }

    /**
     * Distribute subject periods across the week
     */
    protected function distributeSubjectPeriods(Collection $subjects, array $constraints): array
    {
        $distribution = [];
        $totalSlotsPerWeek = count($constraints['working_days']) * ($constraints['max_periods_per_day'] - 1); // -1 for lunch

        foreach ($subjects as $subject) {
            $periodsPerWeek = $subject->pivot->periods_per_week ?? 5; // Default 5 periods per week
            $distribution[$subject->id] = $periodsPerWeek;
        }

        return $distribution;
    }

    /**
     * Get available time slots for a day
     */
    protected function getAvailableSlotsForDay(int $day, array $constraints): array
    {
        $slots = [];
        $currentTime = Carbon::createFromTime(8, 0); // Start at 8:00 AM
        $endTime = Carbon::createFromTime(15, 0); // End at 3:00 PM
        $periodLength = 45; // 45 minutes per period

        $slotCount = 0;
        while ($currentTime->lt($endTime) && $slotCount < $constraints['max_periods_per_day']) {
            // Skip lunch break
            if ($slotCount == $constraints['lunch_break_slot']) {
                $currentTime->addMinutes(60); // 1 hour lunch
                continue;
            }

            // Skip break after certain periods
            if ($slotCount > 0 && $slotCount % $constraints['break_after_periods'] == 0) {
                $currentTime->addMinutes(15); // 15 minute break
                continue;
            }

            $slotEnd = $currentTime->copy()->addMinutes($periodLength);

            $slots[] = [
                'start' => $currentTime->format('H:i'),
                'end' => $slotEnd->format('H:i'),
            ];

            $currentTime = $slotEnd;
            $slotCount++;
        }

        return $slots;
    }

    /**
     * Get random teacher for a subject
     */
    protected function getRandomTeacherForSubject(Subject $subject, Collection $teachers): ?Teacher
    {
        $subjectTeachers = $teachers->filter(function ($teacher) use ($subject) {
            return $teacher->subjects->contains($subject->id);
        });

        return $subjectTeachers->isNotEmpty() ? $subjectTeachers->random() : null;
    }

    /**
     * Get random available room
     */
    protected function getRandomRoom(): string
    {
        return $this->rooms[array_rand($this->rooms)];
    }

    /**
     * Evaluate fitness of population
     */
    protected function evaluatePopulation(array $population, array $constraints): array
    {
        $fitnessScores = [];

        foreach ($population as $timetable) {
            $fitnessScores[] = $this->calculateFitness($timetable, $constraints);
        }

        return $fitnessScores;
    }

    /**
     * Calculate fitness score for a timetable (0.0 to 1.0)
     */
    protected function calculateFitness(array $timetable, array $constraints): float
    {
        $score = 0;
        $totalChecks = 0;

        // Check for teacher conflicts (same teacher at same time)
        $teacherConflicts = $this->checkTeacherConflicts($timetable);
        $score += (1 - $teacherConflicts / count($timetable));
        $totalChecks++;

        // Check for room conflicts (same room at same time)
        $roomConflicts = $this->checkRoomConflicts($timetable);
        $score += (1 - $roomConflicts / count($timetable));
        $totalChecks++;

        // Check for class conflicts (same class at same time - should be 0)
        $classConflicts = $this->checkClassConflicts($timetable);
        $score += (1 - $classConflicts / count($timetable));
        $totalChecks++;

        // Check teacher workload balance
        $workloadBalance = $this->checkWorkloadBalance($timetable);
        $score += $workloadBalance;
        $totalChecks++;

        // Check subject distribution
        $subjectDistribution = $this->checkSubjectDistribution($timetable);
        $score += $subjectDistribution;
        $totalChecks++;

        return $score / $totalChecks;
    }

    /**
     * Check for teacher conflicts
     */
    protected function checkTeacherConflicts(array $timetable): int
    {
        $conflicts = 0;

        for ($i = 0; $i < count($timetable); $i++) {
            for ($j = $i + 1; $j < count($timetable); $j++) {
                $entry1 = $timetable[$i];
                $entry2 = $timetable[$j];

                if ($entry1['teacher_id'] && $entry2['teacher_id'] &&
                    $entry1['teacher_id'] === $entry2['teacher_id'] &&
                    $entry1['day_of_week'] === $entry2['day_of_week'] &&
                    $this->timesOverlap($entry1['starts_at'], $entry1['ends_at'], $entry2['starts_at'], $entry2['ends_at'])) {
                    $conflicts++;
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check for room conflicts
     */
    protected function checkRoomConflicts(array $timetable): int
    {
        $conflicts = 0;

        for ($i = 0; $i < count($timetable); $i++) {
            for ($j = $i + 1; $j < count($timetable); $j++) {
                $entry1 = $timetable[$i];
                $entry2 = $timetable[$j];

                if ($entry1['room'] && $entry2['room'] &&
                    $entry1['room'] === $entry2['room'] &&
                    $entry1['day_of_week'] === $entry2['day_of_week'] &&
                    $this->timesOverlap($entry1['starts_at'], $entry1['ends_at'], $entry2['starts_at'], $entry2['ends_at'])) {
                    $conflicts++;
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check for class conflicts (should be 0 for valid timetable)
     */
    protected function checkClassConflicts(array $timetable): int
    {
        $conflicts = 0;

        for ($i = 0; $i < count($timetable); $i++) {
            for ($j = $i + 1; $j < count($timetable); $j++) {
                $entry1 = $timetable[$i];
                $entry2 = $timetable[$j];

                if ($entry1['class_id'] === $entry2['class_id'] &&
                    $entry1['day_of_week'] === $entry2['day_of_week'] &&
                    $this->timesOverlap($entry1['starts_at'], $entry1['ends_at'], $entry2['starts_at'], $entry2['ends_at'])) {
                    $conflicts++;
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check workload balance among teachers
     */
    protected function checkWorkloadBalance(array $timetable): float
    {
        $teacherWorkload = [];

        foreach ($timetable as $entry) {
            if ($entry['teacher_id']) {
                $teacherWorkload[$entry['teacher_id']] = ($teacherWorkload[$entry['teacher_id']] ?? 0) + 1;
            }
        }

        if (empty($teacherWorkload)) return 0.5;

        $avgWorkload = array_sum($teacherWorkload) / count($teacherWorkload);
        $variance = 0;

        foreach ($teacherWorkload as $workload) {
            $variance += pow($workload - $avgWorkload, 2);
        }

        $variance /= count($teacherWorkload);
        $stdDev = sqrt($variance);

        // Return score based on standard deviation (lower is better)
        return max(0, 1 - ($stdDev / $avgWorkload));
    }

    /**
     * Check subject distribution across days
     */
    protected function checkSubjectDistribution(array $timetable): float
    {
        $subjectDays = [];

        foreach ($timetable as $entry) {
            $key = $entry['subject_id'] . '_' . $entry['day_of_week'];
            $subjectDays[$key] = true;
        }

        $subjects = array_unique(array_column($timetable, 'subject_id'));
        $workingDays = array_unique(array_column($timetable, 'day_of_week'));

        $totalPossible = count($subjects) * count($workingDays);
        $actual = count($subjectDays);

        return $actual / $totalPossible;
    }

    /**
     * Check if two time periods overlap
     */
    protected function timesOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        return $start1 < $end2 && $start2 < $end1;
    }

    /**
     * Evolve population using genetic algorithm
     */
    protected function evolvePopulation(array $population, array $fitnessScores): array
    {
        $newPopulation = [];

        // Keep best individual (elitism)
        $bestIndex = array_search(max($fitnessScores), $fitnessScores);
        $newPopulation[] = $population[$bestIndex];

        // Create rest through crossover and mutation
        while (count($newPopulation) < $this->populationSize) {
            $parent1 = $this->selectParent($population, $fitnessScores);
            $parent2 = $this->selectParent($population, $fitnessScores);

            $child = $this->crossover($parent1, $parent2);
            $child = $this->mutate($child);

            $newPopulation[] = $child;
        }

        return $newPopulation;
    }

    /**
     * Select parent using tournament selection
     */
    protected function selectParent(array $population, array $fitnessScores): array
    {
        $tournamentSize = 5;
        $tournament = [];

        for ($i = 0; $i < $tournamentSize; $i++) {
            $randomIndex = array_rand($population);
            $tournament[] = [
                'individual' => $population[$randomIndex],
                'fitness' => $fitnessScores[$randomIndex],
            ];
        }

        usort($tournament, fn($a, $b) => $b['fitness'] <=> $a['fitness']);
        return $tournament[0]['individual'];
    }

    /**
     * Perform crossover between two timetables
     */
    protected function crossover(array $parent1, array $parent2): array
    {
        $crossoverPoint = rand(1, min(count($parent1), count($parent2)) - 1);

        return array_merge(
            array_slice($parent1, 0, $crossoverPoint),
            array_slice($parent2, $crossoverPoint)
        );
    }

    /**
     * Apply mutation to timetable
     */
    protected function mutate(array $timetable): array
    {
        foreach ($timetable as &$entry) {
            if (rand(0, 100) / 100 < $this->mutationRate) {
                // Randomly change teacher or room
                if (rand(0, 1)) {
                    $entry['teacher_id'] = $this->getRandomTeacherId();
                } else {
                    $entry['room'] = $this->getRandomRoom();
                }
            }
        }

        return $timetable;
    }

    /**
     * Get random teacher ID
     */
    protected function getRandomTeacherId(): ?int
    {
        $teachers = Teacher::inRandomOrder()->limit(10)->pluck('id')->toArray();
        return $teachers ? $teachers[array_rand($teachers)] : null;
    }

    /**
     * Get class subjects with periods per week
     */
    protected function getClassSubjects(SchoolClass $class): Collection
    {
        return $class->subjects()->withPivot('periods_per_week')->get();
    }

    /**
     * Get available teachers for subjects
     */
    protected function getAvailableTeachers(Collection $subjects): Collection
    {
        $subjectIds = $subjects->pluck('id');
        return Teacher::whereHas('subjects', function ($query) use ($subjectIds) {
            $query->whereIn('subjects.id', $subjectIds);
        })->get();
    }

    /**
     * Initialize time slots
     */
    protected function initializeTimeSlots(): void
    {
        $this->timeSlots = [
            ['start' => '08:00', 'end' => '08:45'],
            ['start' => '08:45', 'end' => '09:30'],
            ['start' => '09:45', 'end' => '10:30'],
            ['start' => '10:30', 'end' => '11:15'],
            ['start' => '11:30', 'end' => '12:15'],
            ['start' => '12:15', 'end' => '13:00'],
            ['start' => '14:00', 'end' => '14:45'],
            ['start' => '14:45', 'end' => '15:30'],
        ];
    }

    /**
     * Initialize available rooms
     */
    protected function initializeRooms(): void
    {
        $this->rooms = ['Room 101', 'Room 102', 'Room 103', 'Room 104', 'Room 105',
                       'Lab 1', 'Lab 2', 'Hall A', 'Hall B', 'Gym'];
    }

    /**
     * Load constraints from database
     */
    protected function loadConstraints(): void
    {
        // Load any saved constraints from database
        $this->constraints = TimetableConstraint::all()->keyBy('type');
    }
}