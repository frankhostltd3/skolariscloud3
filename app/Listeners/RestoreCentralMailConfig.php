<?php

namespace App\Listeners;

use App\Services\TenantMailConfigurator;
use Stancl\Tenancy\Events\TenancyEnded;

class RestoreCentralMailConfig
{
    protected TenantMailConfigurator $configurator;

    public function __construct(TenantMailConfigurator $configurator)
    {
        $this->configurator = $configurator;
    }

    public function handle(TenancyEnded $event): void
    {
        $this->configurator->restoreBaseline();
    }
}
