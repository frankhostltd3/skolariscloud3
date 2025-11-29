<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function getConnectionName()
    {
        if (app()->bound('currentSchool')) {
            return 'tenant';
        }
        return config('database.default');
    }
}
