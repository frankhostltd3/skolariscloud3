<?php

namespace App\Policies;

use App\Models\ClassStream;
use App\Models\User;

class ClassStreamPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, ClassStream $model): bool { return true; }
    public function create(User $user): bool { return $user->can('manage academics'); }
    public function update(User $user, ClassStream $model): bool { return $user->can('manage academics'); }
    public function delete(User $user, ClassStream $model): bool { return $user->can('manage academics'); }
}
