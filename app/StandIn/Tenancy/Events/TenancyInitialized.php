<?php

namespace Stancl\Tenancy\Events;

use Stancl\Tenancy\Contracts\Tenant;

class TenancyInitialized
{
    public function __construct(
        public Tenant $tenant
    ) {}
}
