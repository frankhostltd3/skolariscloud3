<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Academic\ClassRoom;

class ClassRoomPolicy
{
    public function view(User $user, ClassRoom $class): bool
    {
        // admins and academic-head can view all
        if ($user->hasRole(['super-admin', 'school-admin', 'academic-head'])) return true;
        // class teacher can view own class
        if ($user->hasRole('teacher') && $class->class_teacher_id === $user->id) return true;
        return false;
    }

    public function mark(User $user, ClassRoom $class): bool
    {
        // same rules as view for now
        return $this->view($user, $class);
    }

    public function export(User $user, ClassRoom $class): bool
    {
        // admins and academic-head or class teacher
        return $this->view($user, $class);
    }
}