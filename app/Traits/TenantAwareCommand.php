<?php

namespace App\Traits;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\App;

trait TenantAwareCommand
{
    protected function processTenants(callable $callback)
    {
        $manager = App::make(TenantDatabaseManager::class);
        $schools = School::all();

        foreach ($schools as $school) {
            $this->info("Switching to tenant: {$school->name} ({$school->subdomain})");

            try {
                $manager->connectToTenant($school);

                // Execute the callback
                $callback($school);

            } catch (\Exception $e) {
                $this->error("Error processing tenant {$school->name}: " . $e->getMessage());
            }
        }

        // Reconnect to central database if needed, or just end
        // $manager->purge();
    }
}
