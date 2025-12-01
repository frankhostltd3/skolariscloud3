<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
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
