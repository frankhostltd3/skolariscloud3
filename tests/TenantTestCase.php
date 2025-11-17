<?php

namespace Tests;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

abstract class TenantTestCase extends TestCase
{
    use RefreshDatabase;

    protected ?School $school = null;
    protected ?User $admin = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareTenant();
    }

    protected function prepareTenant(): void
    {
        // Create the central user and school
        $this->school = School::factory()->create([
            'subdomain' => 'test',
        ]);

        // Set the current tenant
        tenancy()->initialize($this->school);

        // Migrate and seed the tenant's database
        Artisan::call('tenants:migrate', ['--tenants' => $this->school->id]);
        Artisan::call('tenants:seed', ['--tenants' => $this->school->id, '--class' => 'DatabaseSeeder']);

        // Create a user within the tenant context
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Set the host to the tenant's domain for requests
        $this->withServerVariables(['HTTP_HOST' => $this->school->domain]);
    }
}
