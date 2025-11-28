<?php

namespace Stancl\Tenancy;

use Illuminate\Contracts\Foundation\Application;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\Events\TenancyEnded;
use Stancl\Tenancy\Events\TenancyInitialized;

class Tenancy
{
    /**
     * @var callable|null
     */
    public $getBootstrappersUsing;

    protected ?TenantContract $tenant = null;

    public function __construct(protected Application $app)
    {
        $this->getBootstrappersUsing = fn () => config('tenancy.bootstrappers', []);
    }

    public function initialize(TenantContract|string $tenant): TenantContract
    {
        if (! $tenant instanceof TenantContract) {
            $tenant = Tenant::findOrFail($tenant);
        }

        $this->tenant = $tenant;

        $this->app->instance(TenantContract::class, $tenant);
        $this->app->instance('currentTenant', $tenant);

        event(new TenancyInitialized($tenant));

        return $tenant;
    }

    public function tenant(): ?TenantContract
    {
        return $this->tenant;
    }

    public function end(): void
    {
        if ($this->tenant !== null) {
            event(new TenancyEnded($this->tenant));
        }

        $this->tenant = null;

        $this->app->forgetInstance(TenantContract::class);

        if ($this->app->bound('currentTenant')) {
            $this->app->forgetInstance('currentTenant');
        }
    }
}
