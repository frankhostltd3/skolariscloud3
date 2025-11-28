<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Student $model): bool { return true; }
    public function create(User $user): bool { return $user->can('students.create'); }
    public function update(User $user, Student $model): bool { return $user->can('students.edit'); }
    public function delete(User $user, Student $model): bool { return $user->can('students.delete'); }
}
