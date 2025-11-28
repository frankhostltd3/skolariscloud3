<?php

namespace App\Policies;

use App\Models\EducationLevel;
use App\Models\User;

class EducationLevelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view education levels') || $user->can('manage education levels');
    }

    public function view(User $user, EducationLevel $educationLevel): bool
    {
        return $user->can('view education levels') || $user->can('manage education levels');
    }

    public function create(User $user): bool
    {
        return $user->can('manage education levels');
    }

    public function update(User $user, EducationLevel $educationLevel): bool
    {
        return $user->can('manage education levels');
    }

    public function delete(User $user, EducationLevel $educationLevel): bool
    {
        return $user->can('manage education levels');
    }
}
