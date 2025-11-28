<?php

namespace App\Policies;

use App\Models\OnlineExam;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OnlineExamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('teacher');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OnlineExam $onlineExam): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'super-admin', 'Admin', 'admin', 'Staff', 'staff'])) {
            return true;
        }

        return $user->id === $onlineExam->teacher_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('teacher');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OnlineExam $onlineExam): bool
    {
        return $user->id === $onlineExam->teacher_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OnlineExam $onlineExam): bool
    {
        return $user->id === $onlineExam->teacher_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OnlineExam $onlineExam): bool
    {
        return $user->id === $onlineExam->teacher_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OnlineExam $onlineExam): bool
    {
        return $user->id === $onlineExam->teacher_id;
    }
}
