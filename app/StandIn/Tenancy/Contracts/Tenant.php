<?php

namespace Stancl\Tenancy\Contracts;

interface Tenant
{
    public function getTenantKey(): string;

    public function run(callable $callback): mixed;
}
