<?php

namespace Tests;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected ?School $school = null;
    protected ?User $admin = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the central migrations
        Artisan::call('migrate', [
            '--path' => 'database/migrations',
            '--realpath' => true,
        ]);

        // Create the central user and school
        $this->school = School::factory()->create([
            'subdomain' => 'test',
        ]);

        // Set the current tenant
        tenancy()->initialize($this->school);

        // Migrate and seed the tenant's database
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenants',
            '--realpath' => true,
        ]);
        Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--database' => 'tenant']);


        // Create a user within the tenant context and assign role
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');


        // Set the host to the tenant's domain for requests
        $this->withServerVariables(['HTTP_HOST' => $this->school->domain]);
    }
}
