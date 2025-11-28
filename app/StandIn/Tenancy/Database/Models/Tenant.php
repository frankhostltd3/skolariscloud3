<?php

namespace Stancl\Tenancy\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;

class Tenant extends Model implements TenantContract
{
    protected $table = 'tenants';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'plan',
        'contact_email',
        'billing_contact_email',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'tenant_id');
    }

    public function getTenantKey(): string
    {
        return (string) $this->getKey();
    }

    public function run(callable $callback): mixed
    {
        return $callback($this);
    }
}
