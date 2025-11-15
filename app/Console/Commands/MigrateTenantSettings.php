<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrateTenantSettings extends Command
{
    protected $signature = 'tenant:migrate-settings';
    protected $description = 'Create settings table in all tenant databases';

    public function __construct(private TenantDatabaseManager $manager)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $schools = School::query()->orderBy('id')->get();

        if ($schools->isEmpty()) {
            $this->components->info('No schools found.');
            return self::SUCCESS;
        }

        foreach ($schools as $school) {
            $this->components->task("Creating settings table for {$school->name}", function () use ($school) {
                $this->manager->runFor($school, function () {
                    if (Schema::connection('tenant')->hasTable('settings')) {
                        return;
                    }

                    Schema::connection('tenant')->create('settings', function (Blueprint $table) {
                        $table->id();
                        $table->string('key')->unique();
                        $table->text('value')->nullable();
                        $table->timestamps();
                    });
                });
            });
        }

        $this->info('Settings table created successfully in all tenant databases.');
        return self::SUCCESS;
    }
}
