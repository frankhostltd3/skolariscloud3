<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTenantDatabases extends Command
{
    protected $signature = 'tenant:check-databases';

    protected $description = 'Check which tenant databases exist';

    public function handle()
    {
        $databases = DB::connection('mysql')->select('SHOW DATABASES');

        $this->info('All databases:');
        foreach ($databases as $db) {
            if (strpos($db->Database, 'tenant_') === 0) {
                $this->line('  - ' . $db->Database);
            }
        }

        $schools = \App\Models\School::select('id', 'name', 'subdomain', 'database')->get();

        $this->info("\nSchools in system:");
        foreach ($schools as $school) {
            $exists = false;
            foreach ($databases as $db) {
                if ($db->Database === $school->database) {
                    $exists = true;
                    break;
                }
            }

            $status = $exists ? '<info>✓ EXISTS</info>' : '<error>✗ MISSING</error>';
            $this->line("  - {$school->name} ({$school->subdomain}) => {$school->database} {$status}");
        }

        return 0;
    }
}
