<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Subject $model): bool { return true; }
    public function create(User $user): bool { return $user->hasAnyRole(['Admin','Staff']) || $user->can('manage academics'); }
    public function update(User $user, Subject $model): bool { return $user->hasAnyRole(['Admin','Staff']) || $user->can('manage academics'); }
    public function delete(User $user, Subject $model): bool { return $user->hasAnyRole(['Admin','Staff']) || $user->can('manage academics'); }
}
