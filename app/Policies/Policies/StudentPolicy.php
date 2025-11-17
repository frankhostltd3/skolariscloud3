<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Student $model): bool { return true; }
    public function create(User $user): bool { return $user->can('manage academics'); }
    public function update(User $user, Student $model): bool { return $user->can('manage academics'); }
    public function delete(User $user, Student $model): bool { return $user->can('manage academics'); }
}
