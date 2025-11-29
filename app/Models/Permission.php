<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public function getConnectionName()
    {
        if (app()->bound('currentSchool')) {
            return 'tenant';
        }
        return config('database.default');
    }
}
