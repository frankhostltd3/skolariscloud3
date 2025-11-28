<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User as UserModel;

class EmployeeIdPolicy
{
    /**
     * Determine whether the user can view employee IDs.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasRole(['Admin', 'Staff']);
    }

    /**
     * Determine whether the user can generate employee IDs.
     */
    public function generate(UserModel $user): bool
    {
        return $user->hasRole(['Admin', 'Staff']);
    }

    /**
     * Determine whether the user can download employee IDs.
     */
    public function download(UserModel $user): bool
    {
        return $user->hasRole(['Admin', 'Staff']);
    }
}App\Policies;

use App\Models\User;

class EmployeeIdPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
}
