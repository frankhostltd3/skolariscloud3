<?php

namespace App\Listeners;

use App\Services\TenantMailConfigurator;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Events\TenancyInitialized;

class ApplyTenantMailConfig
{
	protected TenantMailConfigurator $configurator;

	public function __construct(TenantMailConfigurator $configurator)
	{
		$this->configurator = $configurator;
	}

	public function handle(TenancyInitialized $event): void
	{
		if (!Schema::hasTable('settings')) {
			return;
		}
		$this->configurator->applyFromSettings();
	}
}
