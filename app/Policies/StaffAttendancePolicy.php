<?php

namespace App\Policies;

use App\Models\User;

class StaffAttendancePolicy
{
    /** Determine whether the user can view staff attendance pages */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'super-admin', 'school-admin', 'academic-head']);
    }

    /** Determine whether the user can mark staff attendance */
    public function mark(User $user): bool
    {
        return $user->hasRole(['Admin', 'super-admin', 'school-admin', 'academic-head']);
    }

    /** Determine whether the user can export staff attendance */
    public function export(User $user): bool
    {
        return $user->hasRole(['Admin', 'super-admin', 'school-admin', 'academic-head']);
    }
}