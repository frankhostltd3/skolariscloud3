<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExamSession;

class ExamSessionPolicy
{
    public function view(User $user, ExamSession $session): bool
    {
        if ($user->hasRole(['super-admin', 'school-admin'])) return true;
        // class teacher or invigilator
        if ($session->invigilator_id === $user->id) return true;
        return $session->class?->class_teacher_id === $user->id
            || $session->class?->subjects()->wherePivot('teacher_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('teacher') || $user->hasAnyRole(['super-admin', 'school-admin']);
    }

    public function update(User $user, ExamSession $session): bool
    {
        if ($user->hasRole(['super-admin', 'school-admin'])) return true;
        // Allow class teacher or invigilator to update
        if ($session->invigilator_id === $user->id) return true;
        return $session->class?->class_teacher_id === $user->id
            || $session->class?->subjects()->wherePivot('teacher_id', $user->id)->exists();
    }

    public function delete(User $user, ExamSession $session): bool
    {
        return $this->update($user, $session);
    }

    public function mark(User $user, ExamSession $session): bool
    {
        return $this->view($user, $session);
    }

    public function export(User $user, ExamSession $session): bool
    {
        return $this->view($user, $session);
    }
}