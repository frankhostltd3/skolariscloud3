<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Teacher $model): bool { return true; }
    public function create(User $user): bool { return $user->can('manage academics'); }
    public function update(User $user, Teacher $model): bool { return $user->can('manage academics'); }
    public function delete(User $user, Teacher $model): bool { return $user->can('manage academics'); }
}
