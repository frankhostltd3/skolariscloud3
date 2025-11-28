<?php

namespace Stancl\Tenancy\Events;

use Stancl\Tenancy\Contracts\Tenant;

class TenancyEnded
{
    public function __construct(
        public Tenant $tenant
    ) {}
}
