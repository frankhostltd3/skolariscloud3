<?php

namespace App\Policies;

use App\Models\LearningMaterial;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LearningMaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Teachers and students can view materials
        return $user->hasAnyRole(['Teacher', 'Student', 'Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LearningMaterial $learningMaterial): bool
    {
        // Admin can view all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can view their own materials
        if ($user->hasRole('Teacher') && $learningMaterial->teacher_id === $user->id) {
            return true;
        }

        // Student can view materials for classes they're enrolled in
        if ($user->hasRole('Student')) {
            $enrollment = $user->enrollments()
                ->where('class_id', $learningMaterial->class_id)
                ->where('status', 'active')
                ->exists();
            
            return $enrollment;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only teachers and admins can create materials
        return $user->hasAnyRole(['Teacher', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LearningMaterial $learningMaterial): bool
    {
        // Admin can update all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only update their own materials
        return $user->hasRole('Teacher') && $learningMaterial->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LearningMaterial $learningMaterial): bool
    {
        // Admin can delete all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only delete their own materials
        return $user->hasRole('Teacher') && $learningMaterial->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can download the material.
     */
    public function download(User $user, LearningMaterial $learningMaterial): bool
    {
        // Material must be downloadable
        if (!$learningMaterial->is_downloadable) {
            return false;
        }

        // Must be a file type (not URL or YouTube)
        if (!in_array($learningMaterial->type, ['document', 'video', 'image', 'audio'])) {
            return false;
        }

        // Admin can download all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can download their own materials
        if ($user->hasRole('Teacher') && $learningMaterial->teacher_id === $user->id) {
            return true;
        }

        // Student can download if enrolled in the class
        if ($user->hasRole('Student')) {
            $enrollment = $user->enrollments()
                ->where('class_id', $learningMaterial->class_id)
                ->where('status', 'active')
                ->exists();
            
            return $enrollment;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LearningMaterial $learningMaterial): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LearningMaterial $learningMaterial): bool
    {
        return $user->hasRole('Admin');
    }
}
