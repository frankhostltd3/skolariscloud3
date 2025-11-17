<?php

namespace App\Policies;

use App\Models\GradingScheme;
use App\Models\User;

class GradingSchemePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view grading systems') || $user->can('manage grading systems');
    }
    public function view(User $user, GradingScheme $scheme): bool
    {
        return $user->can('view grading systems') || $user->can('manage grading systems');
    }
    public function create(User $user): bool
    {
        return $user->can('manage grading systems');
    }
    public function update(User $user, GradingScheme $scheme): bool
    {
        return $user->can('manage grading systems');
    }
    public function delete(User $user, GradingScheme $scheme): bool
    {
        return $user->can('manage grading systems');
    }
}
