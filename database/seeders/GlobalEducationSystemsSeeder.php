<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\EducationLevel;
use App\Models\Academic\ClassRoom;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class GlobalEducationSystemsSeeder extends Seeder
{
    /**
     * Seed education systems for different countries.
     *
     * This seeder demonstrates how the system adapts to ANY education structure worldwide.
     * Uncomment the country you want to seed, or create your own custom structure.
     */
    public function run(): void
    {
        $this->command->info('🌍 Seeding Global Education Systems...');

        // Get all tenant schools
        $schools = School::all();

        if ($schools->isEmpty()) {
            $this->command->warn('No schools found. Please create schools first.');
            return;
        }

        foreach ($schools as $school) {
            DB::connection('tenant')->beginTransaction();

            try {
                $this->command->info("\n📚 Setting up education system for: {$school->name}");

                // Choose ONE of these systems (or create your own):

                // 🇺🇬 Uganda System
                // $this->seedUgandaSystem($school);

                // 🇰🇪 Kenya System (Competency-Based Curriculum)
                // $this->seedKenyaSystem($school);

                // 🇺🇸 United States System
                // $this->seedUSASystem($school);

                // 🇬🇧 United Kingdom System
                // $this->seedUKSystem($school);

                // 🇿🇦 South Africa System
                // $this->seedSouthAfricaSystem($school);

                // 🇳🇬 Nigeria System
                // $this->seedNigeriaSystem($school);

                // 🇮🇳 India System (CBSE)
                // $this->seedIndiaSystem($school);

                // 🇦🇺 Australia System
                // $this->seedAustraliaSystem($school);

                // 🇨🇦 Canada System
                // $this->seedCanadaSystem($school);

                // 🇫🇷 France System
                // $this->seedFranceSystem($school);

                // 🇩🇪 Germany System
                // $this->seedGermanySystem($school);

                // 🇯🇵 Japan System
                // $this->seedJapanSystem($school);

                // 🇨🇳 China System
                // $this->seedChinaSystem($school);

                // 🇧🇷 Brazil System
                // $this->seedBrazilSystem($school);

                // Default: Uganda System (most commonly requested)
                $this->seedUgandaSystem($school);

                DB::connection('tenant')->commit();
                $this->command->info("✅ Education system setup complete for {$school->name}");

            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                $this->command->error("❌ Error setting up {$school->name}: {$e->getMessage()}");
            }
        }

        $this->command->info("\n🎉 Global education systems seeding completed!");
    }

    /**
     * 🇺🇬 UGANDA EDUCATION SYSTEM
     */
    private function seedUgandaSystem($school)
    {
        // Primary Education (P1-P7)
        $primary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Primary',
            'code' => 'P',
            'description' => 'Primary Education (7 years)',
            'min_grade' => 1,
            'max_grade' => 7,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (range(1, 7) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $primary->id,
                'name' => "Primary {$grade}",
                'code' => "P{$grade}",
                'capacity' => 50,
                'is_active' => true,
            ]);
        }

        // O-Level (S1-S4)
        $oLevel = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'O-Level',
            'code' => 'O',
            'description' => 'Ordinary Level (4 years)',
            'min_grade' => 1,
            'max_grade' => 4,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(1, 4) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $oLevel->id,
                'name' => "Senior {$grade}",
                'code' => "S{$grade}",
                'capacity' => 45,
                'is_active' => true,
            ]);
        }

        // A-Level (S5-S6)
        $aLevel = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'A-Level',
            'code' => 'A',
            'description' => 'Advanced Level (2 years)',
            'min_grade' => 5,
            'max_grade' => 6,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (range(5, 6) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $aLevel->id,
                'name' => "Senior {$grade}",
                'code' => "S{$grade}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ Uganda system: Primary (P1-P7), O-Level (S1-S4), A-Level (S5-S6)');
    }

    /**
     * 🇰🇪 KENYA EDUCATION SYSTEM (Competency-Based Curriculum)
     */
    private function seedKenyaSystem($school)
    {
        // Pre-Primary (PP1-PP2)
        $prePrimary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Pre-Primary',
            'code' => 'PP',
            'description' => 'Pre-Primary Education (2 years)',
            'min_grade' => 1,
            'max_grade' => 2,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (range(1, 2) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $prePrimary->id,
                'name' => "Pre-Primary {$grade}",
                'code' => "PP{$grade}",
                'capacity' => 30,
                'is_active' => true,
            ]);
        }

        // Primary (Grade 1-6)
        $primary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Primary',
            'code' => 'PRI',
            'description' => 'Primary Education (6 years)',
            'min_grade' => 1,
            'max_grade' => 6,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(1, 6) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $primary->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 45,
                'is_active' => true,
            ]);
        }

        // Junior Secondary (Grade 7-9)
        $juniorSecondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Junior Secondary',
            'code' => 'JS',
            'description' => 'Junior Secondary (3 years)',
            'min_grade' => 7,
            'max_grade' => 9,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (range(7, 9) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $juniorSecondary->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        // Senior Secondary (Grade 10-12)
        $seniorSecondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Senior Secondary',
            'code' => 'SS',
            'description' => 'Senior Secondary (3 years)',
            'min_grade' => 10,
            'max_grade' => 12,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        foreach (range(10, 12) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $seniorSecondary->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ Kenya CBC system: Pre-Primary (PP1-PP2), Primary (G1-G6), Junior Secondary (G7-G9), Senior Secondary (G10-G12)');
    }

    /**
     * 🇺🇸 UNITED STATES EDUCATION SYSTEM
     */
    private function seedUSASystem($school)
    {
        // Elementary School (K-5)
        $elementary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Elementary School',
            'code' => 'ELEM',
            'description' => 'Elementary Education (Kindergarten - Grade 5)',
            'min_grade' => 0,
            'max_grade' => 5,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ClassRoom::create([
            'school_id' => $school->id,
            'education_level_id' => $elementary->id,
            'name' => 'Kindergarten',
            'code' => 'K',
            'capacity' => 25,
            'is_active' => true,
        ]);

        foreach (range(1, 5) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $elementary->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 30,
                'is_active' => true,
            ]);
        }

        // Middle School (6-8)
        $middleSchool = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Middle School',
            'code' => 'MS',
            'description' => 'Middle School (Grades 6-8)',
            'min_grade' => 6,
            'max_grade' => 8,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(6, 8) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $middleSchool->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 35,
                'is_active' => true,
            ]);
        }

        // High School (9-12)
        $highSchool = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'High School',
            'code' => 'HS',
            'description' => 'High School (Grades 9-12)',
            'min_grade' => 9,
            'max_grade' => 12,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $gradeNames = [9 => 'Freshman', 10 => 'Sophomore', 11 => 'Junior', 12 => 'Senior'];
        foreach (range(9, 12) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $highSchool->id,
                'name' => "Grade {$grade} ({$gradeNames[$grade]})",
                'code' => "G{$grade}",
                'capacity' => 35,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ USA system: Elementary (K-5), Middle School (6-8), High School (9-12)');
    }

    /**
     * 🇬🇧 UNITED KINGDOM EDUCATION SYSTEM
     */
    private function seedUKSystem($school)
    {
        // Primary (Year 1-6)
        $primary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Primary',
            'code' => 'PRI',
            'description' => 'Primary Education (Years 1-6)',
            'min_grade' => 1,
            'max_grade' => 6,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (range(1, 6) as $year) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $primary->id,
                'name' => "Year {$year}",
                'code' => "Y{$year}",
                'capacity' => 30,
                'is_active' => true,
            ]);
        }

        // Secondary (Year 7-11)
        $secondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Secondary',
            'code' => 'SEC',
            'description' => 'Secondary Education (Years 7-11, leading to GCSEs)',
            'min_grade' => 7,
            'max_grade' => 11,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(7, 11) as $year) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $secondary->id,
                'name' => "Year {$year}",
                'code' => "Y{$year}",
                'capacity' => 32,
                'is_active' => true,
            ]);
        }

        // Sixth Form (Year 12-13)
        $sixthForm = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Sixth Form',
            'code' => 'SF',
            'description' => 'Sixth Form (Years 12-13, A-Levels)',
            'min_grade' => 12,
            'max_grade' => 13,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (range(12, 13) as $year) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $sixthForm->id,
                'name' => "Year {$year}",
                'code' => "Y{$year}",
                'capacity' => 25,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ UK system: Primary (Y1-Y6), Secondary (Y7-Y11), Sixth Form (Y12-Y13)');
    }

    /**
     * 🇿🇦 SOUTH AFRICA EDUCATION SYSTEM
     */
    private function seedSouthAfricaSystem($school)
    {
        // Foundation Phase (Grade R-3)
        $foundation = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Foundation Phase',
            'code' => 'FP',
            'description' => 'Foundation Phase (Grade R-3)',
            'min_grade' => 0,
            'max_grade' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ClassRoom::create([
            'school_id' => $school->id,
            'education_level_id' => $foundation->id,
            'name' => 'Grade R (Reception)',
            'code' => 'GR',
            'capacity' => 30,
            'is_active' => true,
        ]);

        foreach (range(1, 3) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $foundation->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 35,
                'is_active' => true,
            ]);
        }

        // Intermediate Phase (Grade 4-6)
        $intermediate = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Intermediate Phase',
            'code' => 'IP',
            'description' => 'Intermediate Phase (Grade 4-6)',
            'min_grade' => 4,
            'max_grade' => 6,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(4, 6) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $intermediate->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        // Senior Phase (Grade 7-9)
        $senior = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Senior Phase',
            'code' => 'SP',
            'description' => 'Senior Phase (Grade 7-9)',
            'min_grade' => 7,
            'max_grade' => 9,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (range(7, 9) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $senior->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        // FET Phase (Grade 10-12)
        $fet = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'FET Phase',
            'code' => 'FET',
            'description' => 'Further Education and Training (Grade 10-12, Matric)',
            'min_grade' => 10,
            'max_grade' => 12,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        foreach (range(10, 12) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $fet->id,
                'name' => "Grade {$grade}",
                'code' => "G{$grade}",
                'capacity' => 35,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ South Africa system: Foundation (R-3), Intermediate (4-6), Senior (7-9), FET (10-12)');
    }

    /**
     * 🇳🇬 NIGERIA EDUCATION SYSTEM
     */
    private function seedNigeriaSystem($school)
    {
        // Primary (Basic 1-6)
        $primary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Primary Education',
            'code' => 'PE',
            'description' => 'Primary Education (Basic 1-6)',
            'min_grade' => 1,
            'max_grade' => 6,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (range(1, 6) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $primary->id,
                'name' => "Basic {$grade}",
                'code' => "B{$grade}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        // Junior Secondary (JSS 1-3)
        $juniorSecondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Junior Secondary',
            'code' => 'JSS',
            'description' => 'Junior Secondary School (JSS 1-3)',
            'min_grade' => 1,
            'max_grade' => 3,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(1, 3) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $juniorSecondary->id,
                'name' => "JSS {$grade}",
                'code' => "JSS{$grade}",
                'capacity' => 45,
                'is_active' => true,
            ]);
        }

        // Senior Secondary (SS 1-3)
        $seniorSecondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Senior Secondary',
            'code' => 'SSS',
            'description' => 'Senior Secondary School (SS 1-3, leading to WAEC/NECO)',
            'min_grade' => 1,
            'max_grade' => 3,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (range(1, 3) as $grade) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $seniorSecondary->id,
                'name' => "SS {$grade}",
                'code' => "SS{$grade}",
                'capacity' => 45,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ Nigeria system: Primary (Basic 1-6), JSS (1-3), SSS (1-3)');
    }

    /**
     * 🇮🇳 INDIA EDUCATION SYSTEM (CBSE)
     */
    private function seedIndiaSystem($school)
    {
        // Primary (Class 1-5)
        $primary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Primary',
            'code' => 'PRI',
            'description' => 'Primary Education (Class 1-5)',
            'min_grade' => 1,
            'max_grade' => 5,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        foreach (range(1, 5) as $class) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $primary->id,
                'name' => "Class {$class}",
                'code' => "C{$class}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        // Middle (Class 6-8)
        $middle = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Middle School',
            'code' => 'MS',
            'description' => 'Middle School (Class 6-8)',
            'min_grade' => 6,
            'max_grade' => 8,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        foreach (range(6, 8) as $class) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $middle->id,
                'name' => "Class {$class}",
                'code' => "C{$class}",
                'capacity' => 45,
                'is_active' => true,
            ]);
        }

        // Secondary (Class 9-10)
        $secondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Secondary',
            'code' => 'SEC',
            'description' => 'Secondary Education (Class 9-10, Board Exam)',
            'min_grade' => 9,
            'max_grade' => 10,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach (range(9, 10) as $class) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $secondary->id,
                'name' => "Class {$class}",
                'code' => "C{$class}",
                'capacity' => 45,
                'is_active' => true,
            ]);
        }

        // Senior Secondary (Class 11-12)
        $seniorSecondary = EducationLevel::create([
            'school_id' => $school->id,
            'name' => 'Senior Secondary',
            'code' => 'SR',
            'description' => 'Senior Secondary (Class 11-12, with streams: Science/Commerce/Arts)',
            'min_grade' => 11,
            'max_grade' => 12,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        foreach (range(11, 12) as $class) {
            ClassRoom::create([
                'school_id' => $school->id,
                'education_level_id' => $seniorSecondary->id,
                'name' => "Class {$class}",
                'code' => "C{$class}",
                'capacity' => 40,
                'is_active' => true,
            ]);
        }

        $this->command->info('  ✓ India CBSE system: Primary (1-5), Middle (6-8), Secondary (9-10), Senior Secondary (11-12)');
    }

    // Additional systems can be added here...
    // seedAustraliaSystem(), seedCanadaSystem(), seedFranceSystem(), etc.
}
