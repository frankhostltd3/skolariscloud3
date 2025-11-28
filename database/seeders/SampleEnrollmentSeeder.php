<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SampleEnrollmentSeeder extends Seeder
{
    /**
     * Seed a handful of enrollment records so dashboards and classroom
     * features have meaningful demo data.
     */
    public function run(): void
    {
        $schema = Schema::connection('tenant');
        $requiredTables = ['users', 'classes', 'enrollments'];

        foreach ($requiredTables as $table) {
            if (! $schema->hasTable($table)) {
                $this->command?->warn("Skipping sample enrollment seeding because the {$table} table is missing.");
                return;
            }
        }

        $schoolId = config('tenant.school_id');

        $academicYear = null;
        if ($schema->hasTable('academic_years')) {
            $label = now()->format('Y') . '/' . now()->addYear()->format('Y');
            $academicYear = AcademicYear::firstOrCreate(
                ['name' => $label],
                [
                    'start_date' => now()->copy()->startOfYear(),
                    'end_date' => now()->copy()->endOfYear(),
                    'is_current' => true,
                ]
            );
        }

        $classes = ClassRoom::query()->take(3)->get();

        if ($classes->isEmpty()) {
            if (! $schema->hasTable('schools')) {
                $this->command?->warn('No classes found and schools table missing. Unable to create sample classes.');
                return;
            }

            $defaultSchoolId = $schoolId ?? DB::connection('tenant')->table('schools')->value('id');

            if (! $defaultSchoolId) {
                $this->command?->warn('No classes found and no school record available to attach new classes.');
                return;
            }

            $classes = collect(range(1, 3))->map(function ($index) use ($defaultSchoolId) {
                return ClassRoom::create([
                    'school_id' => $defaultSchoolId,
                    'education_level_id' => null,
                    'name' => "Sample Class {$index}",
                    'code' => 'CLS' . $index,
                    'capacity' => 45,
                    'is_active' => true,
                ]);
            });
        }

        if ($classes->isEmpty()) {
            $this->command?->warn('No classes available to attach enrollments to.');
            return;
        }

        $students = [
            ['name' => 'Olivia Namusoke', 'email' => 'olivia.student@example.com'],
            ['name' => 'Noah Auma', 'email' => 'noah.student@example.com'],
            ['name' => 'Ava Kagwa', 'email' => 'ava.student@example.com'],
            ['name' => 'Ethan Mbabazi', 'email' => 'ethan.student@example.com'],
            ['name' => 'Mia Kamya', 'email' => 'mia.student@example.com'],
        ];

        $adminId = User::where('user_type', UserType::ADMIN->value)->value('id');

        foreach ($students as $index => $data) {
            $student = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'user_type' => UserType::STUDENT->value,
                    'school_id' => $schoolId,
                    'is_active' => true,
                ]
            );

            if (! $student->email_verified_at) {
                $student->forceFill(['email_verified_at' => now()])->save();
            }

            if ($student->user_type !== UserType::STUDENT->value) {
                $student->forceFill(['user_type' => UserType::STUDENT->value])->save();
            }

            $class = $classes[$index % $classes->count()];

            Enrollment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $academicYear?->id,
                ],
                [
                    'enrollment_date' => now()->copy()->subDays($index * 10),
                    'status' => 'active',
                    'fees_total' => 1200,
                    'fees_paid' => $index % 2 === 0 ? 1200 : 600,
                    'notes' => 'Seeded sample enrollment for demo purposes.',
                    'enrolled_by' => $adminId,
                ]
            );
        }

        $this->command?->info('Sample enrollments seeded successfully.');
    }
}
