<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use Database\Seeders\GlobalEducationSystemsSeeder;

class SetupEducationSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'education:setup
                            {school? : School ID or subdomain}
                            {--country= : Country code (UG, KE, US, UK, ZA, NG, IN, AU, CA, FR, DE, JP, CN, BR)}
                            {--list : List all available country systems}';

    /**
     * The console command description.
     */
    protected $description = 'Setup education system for a school based on country';

    /**
     * Available country systems
     */
    private $countries = [
        'UG' => ['name' => 'Uganda', 'method' => 'seedUgandaSystem', 'desc' => 'Primary (P1-P7), O-Level (S1-S4), A-Level (S5-S6)'],
        'KE' => ['name' => 'Kenya', 'method' => 'seedKenyaSystem', 'desc' => 'Pre-Primary (PP1-PP2), Primary (G1-G6), Junior Secondary (G7-G9), Senior Secondary (G10-G12)'],
        'US' => ['name' => 'United States', 'method' => 'seedUSASystem', 'desc' => 'Elementary (K-5), Middle School (6-8), High School (9-12)'],
        'UK' => ['name' => 'United Kingdom', 'method' => 'seedUKSystem', 'desc' => 'Primary (Y1-Y6), Secondary (Y7-Y11), Sixth Form (Y12-Y13)'],
        'ZA' => ['name' => 'South Africa', 'method' => 'seedSouthAfricaSystem', 'desc' => 'Foundation (R-3), Intermediate (4-6), Senior (7-9), FET (10-12)'],
        'NG' => ['name' => 'Nigeria', 'method' => 'seedNigeriaSystem', 'desc' => 'Primary (Basic 1-6), JSS (1-3), SSS (1-3)'],
        'IN' => ['name' => 'India', 'method' => 'seedIndiaSystem', 'desc' => 'Primary (1-5), Middle (6-8), Secondary (9-10), Senior Secondary (11-12)'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // List available systems
        if ($this->option('list')) {
            $this->listCountrySystems();
            return 0;
        }

        // Get country
        $countryCode = strtoupper($this->option('country') ?? '');

        if (empty($countryCode)) {
            $countryCode = $this->askForCountry();
        }

        if (!isset($this->countries[$countryCode])) {
            $this->error("❌ Invalid country code: {$countryCode}");
            $this->info("Run 'php artisan education:setup --list' to see available systems.");
            return 1;
        }

        // Get school
        $school = $this->getSchool();

        if (!$school) {
            $this->error('❌ School not found.');
            return 1;
        }

        // Confirm action
        $country = $this->countries[$countryCode];
        $this->info("\n🌍 Setting up {$country['name']} education system for: {$school->name}");
        $this->info("📚 Structure: {$country['desc']}");

        if (!$this->confirm('Do you want to proceed?', true)) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Setup education system
        try {
            $seeder = new GlobalEducationSystemsSeeder();
            $method = $country['method'];

            // Use reflection to call the private method
            $reflection = new \ReflectionClass($seeder);
            $reflectionMethod = $reflection->getMethod($method);
            $reflectionMethod->setAccessible(true);

            $this->info("\n⏳ Creating education levels and classes...");

            \DB::connection('tenant')->beginTransaction();
            $reflectionMethod->invoke($seeder, $school);
            \DB::connection('tenant')->commit();

            $this->info("✅ Education system setup complete!");
            $this->info("\n📊 Summary:");

            // Show summary
            $levels = \App\Models\Academic\EducationLevel::where('school_id', $school->id)->count();
            $classes = \App\Models\Academic\ClassRoom::where('school_id', $school->id)->count();

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Education Levels', $levels],
                    ['Classes Created', $classes],
                ]
            );

            $this->info("\n🚀 Next steps:");
            $this->info("1. View classes: http://{$school->subdomain}.localhost:8000/tenant/academics/classes");
            $this->info("2. Assign subjects to classes");
            $this->info("3. Enroll students");

            return 0;

        } catch (\Exception $e) {
            \DB::connection('tenant')->rollBack();
            $this->error("❌ Error: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * List all available country systems
     */
    private function listCountrySystems()
    {
        $this->info("\n🌍 Available Education Systems\n");

        $data = [];
        foreach ($this->countries as $code => $country) {
            $data[] = [
                $code,
                $country['name'],
                $country['desc'],
            ];
        }

        $this->table(['Code', 'Country', 'Structure'], $data);

        $this->info("\n📖 Usage Examples:");
        $this->info("  php artisan education:setup --country=UG");
        $this->info("  php artisan education:setup myschool --country=US");
        $this->info("  php artisan education:setup 1 --country=UK");
        $this->info("\n💡 Tip: You can create custom systems by editing GlobalEducationSystemsSeeder.php");
    }

    /**
     * Ask user to select a country
     */
    private function askForCountry()
    {
        $choices = [];
        foreach ($this->countries as $code => $country) {
            $choices[] = "{$code} - {$country['name']}";
        }

        $selection = $this->choice('Select your country\'s education system', $choices);

        // Extract code from selection
        return substr($selection, 0, 2);
    }

    /**
     * Get school from argument or prompt user
     */
    private function getSchool()
    {
        $schoolIdentifier = $this->argument('school');

        if ($schoolIdentifier) {
            // Try to find by ID or subdomain
            $school = is_numeric($schoolIdentifier)
                ? School::find($schoolIdentifier)
                : School::where('subdomain', $schoolIdentifier)->first();

            if ($school) {
                return $school;
            }
        }

        // List schools for selection
        $schools = School::all();

        if ($schools->isEmpty()) {
            $this->error('No schools found in database.');
            return null;
        }

        if ($schools->count() === 1) {
            return $schools->first();
        }

        $choices = [];
        $schoolsMap = [];

        foreach ($schools as $school) {
            $key = "{$school->id} - {$school->name} ({$school->subdomain})";
            $choices[] = $key;
            $schoolsMap[$key] = $school;
        }

        $selection = $this->choice('Select a school', $choices);

        return $schoolsMap[$selection];
    }
}
