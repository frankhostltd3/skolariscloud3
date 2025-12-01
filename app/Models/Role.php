<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function getConnectionName()
    {
        // If we are in landlord context (team_id = 'landlord'), use central connection
        $teamId = app(\Spatie\Permission\PermissionRegistrar::class)->getPermissionsTeamId();
        if ($teamId === 'landlord') {
            return config('database.default');
        }

        if (app()->bound('currentSchool')) {
            return 'tenant';
        }
        return config('database.default');
    }
}
