<?php

namespace Database\Seeders;

use App\Models\Academic\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');

        AcademicYear::firstOrCreate(
            ['name' => $currentYear],
            [
                'start_date' => "{$currentYear}-01-01",
                'end_date' => "{$currentYear}-12-31",
                'is_current' => true,
            ]
        );

        $nextYear = $currentYear + 1;
        AcademicYear::firstOrCreate(
            ['name' => $nextYear],
            [
                'start_date' => "{$nextYear}-01-01",
                'end_date' => "{$nextYear}-12-31",
                'is_current' => false,
            ]
        );
    }
}
