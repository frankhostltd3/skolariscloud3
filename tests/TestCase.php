<?php

namespace Tests;

use App\Enums\UserType;
use App\Models\School;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Only wrap the central sqlite connection in transactions so tenant migrations stay isolated.
     *
     * @var array<int, string>
     */
    protected array $connectionsToTransact = ['sqlite'];

    protected ?School $school = null;
    protected ?Tenant $tenant = null;
    protected ?User $admin = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the central user and school
        $this->school = School::factory()->create([
            'subdomain' => 'test',
        ]);

        // Create a lightweight tenant representation and initialize context
        $this->tenant = Tenant::query()->create([
            'id' => (string) Str::uuid(),
            'name' => $this->school->name,
            'data' => [
                'school_id' => $this->school->id,
                'subdomain' => $this->school->subdomain,
                'domain' => $this->school->domain,
            ],
        ]);

        tenancy()->initialize($this->tenant);
        app()->instance('currentSchool', $this->school);

        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->setPermissionsTeamId($this->tenant->getTenantKey());

        /** @var TenantDatabaseManager $manager */
        $manager = app(TenantDatabaseManager::class);
        $manager->connect($this->school);

        // Migrate and seed the tenant's database fresh for each test
        Artisan::call('migrate:fresh', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenants',
            '--realpath' => true,
            '--force' => true,
        ]);
        Artisan::call('db:seed', [
            '--class' => 'DatabaseSeeder',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        $permissionRegistrar->forgetCachedPermissions();
        Role::query()->firstOrCreate(
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'tenant_id' => $this->tenant->getTenantKey(),
            ]
        );


        // Create a user within the tenant context and assign role
        $this->admin = User::factory()->create([
            'user_type' => UserType::ADMIN,
        ]);
        $this->admin->assignRole('admin');


        // Set the host to the tenant's domain for requests (fallback to localhost)
        $host = $this->school->domain ?: 'test.localhost';
        $this->withServerVariables(['HTTP_HOST' => $host]);
    }

    protected function tearDown(): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
        app(TenantDatabaseManager::class)->disconnect();

        parent::tearDown();
    }
}
