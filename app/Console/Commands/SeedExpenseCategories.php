<?php

namespace App\Console\Commands;

use App\Models\ExpenseCategory;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedExpenseCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:seed-expense-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed expense categories for all tenant schools';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
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

        // Get all tenant databases using the configured central connection
        $centralConnection = config('database.central_connection', config('database.default'));
        $tenants = DB::connection($centralConnection)->table('schools')->get();

        foreach ($tenants as $tenant) {
            $this->info("Seeding expense categories for {$tenant->name}...");

            // Switch to tenant database
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            // Use the school ID from the main database
            foreach ($categories as $category) {
                ExpenseCategory::on('tenant')->updateOrCreate(
                    [
                        'school_id' => $tenant->id,
                        'code' => $category['code'],
                    ],
                    [
                        'name' => $category['name'],
                        'description' => $category['description'],
                        'is_active' => true,
                    ]
                );
            }
            $this->info("✓ Seeded 12 expense categories for {$tenant->name}");
        }

        $this->info('');
        $this->info('Expense categories seeded successfully for all tenant schools!');
        return 0;
    }
}
