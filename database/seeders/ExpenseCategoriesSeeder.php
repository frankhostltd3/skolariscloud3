<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\School;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all schools from tenant connection
        $schools = School::on('tenant')->get();

        $categories = [
            ['name' => 'Salaries & Wages', 'code' => 'SAL', 'description' => 'Staff salaries and wages'],
            ['name' => 'Utilities', 'code' => 'UTL', 'description' => 'Electricity, water, internet, phone'],
            ['name' => 'Maintenance & Repairs', 'code' => 'MNT', 'description' => 'Building and equipment maintenance'],
            ['name' => 'Supplies & Materials', 'code' => 'SUP', 'description' => 'Office and classroom supplies'],
            ['name' => 'Transportation', 'code' => 'TRN', 'description' => 'School buses and vehicle expenses'],
            ['name' => 'Food & Catering', 'code' => 'FOD', 'description' => 'Cafeteria and meal expenses'],
            ['name' => 'Insurance', 'code' => 'INS', 'description' => 'School insurance premiums'],
            ['name' => 'Professional Development', 'code' => 'PD', 'description' => 'Teacher training and workshops'],
            ['name' => 'Marketing & Advertising', 'code' => 'MKT', 'description' => 'Promotional materials and campaigns'],
            ['name' => 'Technology & Software', 'code' => 'TEC', 'description' => 'Computer equipment and software licenses'],
            ['name' => 'Rent & Lease', 'code' => 'RNT', 'description' => 'Building and equipment rentals'],
            ['name' => 'Other Expenses', 'code' => 'OTH', 'description' => 'Miscellaneous expenses'],
        ];

        foreach ($schools as $school) {
            foreach ($categories as $category) {
                ExpenseCategory::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'code' => $category['code'],
                    ],
                    [
                        'name' => $category['name'],
                        'description' => $category['description'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Expense categories seeded successfully for all tenant schools!');
    }
}
