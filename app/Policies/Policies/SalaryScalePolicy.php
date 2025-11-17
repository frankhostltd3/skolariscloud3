<?php

namespace App\Policies;

use App\Models\SalaryScale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalaryScalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SalaryScale $salaryScale): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SalaryScale $salaryScale): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SalaryScale $salaryScale): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SalaryScale $salaryScale): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SalaryScale $salaryScale): bool
    {
        return $user->hasRole(['Admin', 'administrator', 'Staff']);
    }
}
