<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VirtualClass;
use Illuminate\Auth\Access\Response;

class VirtualClassPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Teachers and students can view virtual classes
        return $user->hasAnyRole(['Teacher', 'Student', 'Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VirtualClass $virtualClass): bool
    {
        // Admin can view all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can view their own classes
        if ($user->hasRole('Teacher') && $virtualClass->teacher_id === $user->id) {
            return true;
        }

        // Student can view classes they're enrolled in
        if ($user->hasRole('Student')) {
            // Check if student is enrolled in this class
            $enrollment = $user->enrollments()
                ->where('school_class_id', $virtualClass->class_id)
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
        // Only teachers and admins can create virtual classes
        return $user->hasAnyRole(['Teacher', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VirtualClass $virtualClass): bool
    {
        // Admin can update all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only update their own classes
        if ($user->hasRole('Teacher') && $virtualClass->teacher_id === $user->id) {
            // Can only edit scheduled classes
            return $virtualClass->status === 'scheduled';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VirtualClass $virtualClass): bool
    {
        // Admin can delete all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only delete their own scheduled classes
        if ($user->hasRole('Teacher') && $virtualClass->teacher_id === $user->id) {
            return $virtualClass->status === 'scheduled';
        }

        return false;
    }

    /**
     * Determine whether the user can start the class.
     */
    public function start(User $user, VirtualClass $virtualClass): bool
    {
        // Only the teacher who owns the class can start it
        if ($virtualClass->teacher_id !== $user->id) {
            return false;
        }

        // Can only start scheduled classes
        return $virtualClass->status === 'scheduled';
    }

    /**
     * Determine whether the user can end the class.
     */
    public function end(User $user, VirtualClass $virtualClass): bool
    {
        // Only the teacher who owns the class can end it
        if ($virtualClass->teacher_id !== $user->id) {
            return false;
        }

        // Can only end ongoing classes
        return $virtualClass->status === 'ongoing';
    }

    /**
     * Determine whether the user can cancel the class.
     */
    public function cancel(User $user, VirtualClass $virtualClass): bool
    {
        // Admin can cancel any class
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only cancel their own scheduled classes
        if ($virtualClass->teacher_id === $user->id) {
            return $virtualClass->status === 'scheduled';
        }

        return false;
    }

    /**
     * Determine whether the user can take attendance.
     */
    public function takeAttendance(User $user, VirtualClass $virtualClass): bool
    {
        // Only the teacher who owns the class can take attendance
        if ($virtualClass->teacher_id !== $user->id) {
            return false;
        }

        // Can take attendance for ongoing or completed classes
        return in_array($virtualClass->status, ['ongoing', 'completed']);
    }

    /**
     * Determine whether the user can join the class.
     */
    public function join(User $user, VirtualClass $virtualClass): bool
    {
        // Student must be enrolled in the class
        if (!$user->hasRole('Student')) {
            return false;
        }

        $enrollment = $user->enrollments()
            ->where('school_class_id', $virtualClass->class_id)
            ->where('status', 'active')
            ->exists();

        if (!$enrollment) {
            return false;
        }

        // Can only join scheduled or ongoing classes
        return in_array($virtualClass->status, ['scheduled', 'ongoing']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VirtualClass $virtualClass): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VirtualClass $virtualClass): bool
    {
        return $user->hasRole('Admin');
    }
}
