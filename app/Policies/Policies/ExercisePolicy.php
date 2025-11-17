<?php

namespace App\Policies;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Carbon\Carbon;

class ExercisePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Teachers and students can view exercises
        return $user->hasAnyRole(['Teacher', 'Student', 'Admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exercise $exercise): bool
    {
        // Admin can view all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can view their own exercises
        if ($user->hasRole('Teacher') && $exercise->teacher_id === $user->id) {
            return true;
        }

        // Student can view exercises for classes they're enrolled in
        if ($user->hasRole('Student')) {
            $enrollment = $user->enrollments()
                ->where('school_class_id', $exercise->class_id)
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
        // Only teachers and admins can create exercises
        return $user->hasAnyRole(['Teacher', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exercise $exercise): bool
    {
        // Admin can update all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only update their own exercises
        return $user->hasRole('Teacher') && $exercise->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exercise $exercise): bool
    {
        // Admin can delete all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only delete their own exercises
        return $user->hasRole('Teacher') && $exercise->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can submit to this exercise.
     */
    public function submit(User $user, Exercise $exercise): bool
    {
        // Only students can submit
        if (!$user->hasRole('Student')) {
            return false;
        }

        // Must be enrolled in the class
        $enrollment = $user->enrollments()
            ->where('school_class_id', $exercise->class_id)
            ->where('status', 'active')
            ->exists();

        if (!$enrollment) {
            return false;
        }

        // Check if already submitted
        $hasSubmitted = $exercise->submissions()
            ->where('student_id', $user->id)
            ->exists();

        if ($hasSubmitted) {
            return false; // Already submitted
        }

        // Check if late submissions are allowed
        if (Carbon::now()->isAfter($exercise->due_date)) {
            return $exercise->allow_late_submission;
        }

        return true;
    }

    /**
     * Determine whether the user can grade submissions.
     */
    public function grade(User $user, Exercise $exercise): bool
    {
        // Admin can grade all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can only grade their own exercises
        return $user->hasRole('Teacher') && $exercise->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can view submissions.
     */
    public function viewSubmissions(User $user, Exercise $exercise): bool
    {
        // Admin can view all submissions
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can view submissions for their own exercises
        if ($user->hasRole('Teacher') && $exercise->teacher_id === $user->id) {
            return true;
        }

        // Student can view their own submission
        if ($user->hasRole('Student')) {
            $enrollment = $user->enrollments()
                ->where('school_class_id', $exercise->class_id)
                ->where('status', 'active')
                ->exists();
            
            return $enrollment;
        }

        return false;
    }

    /**
     * Determine whether the user can download submission files.
     */
    public function downloadSubmission(User $user, Exercise $exercise, $submissionStudentId): bool
    {
        // Admin can download all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Teacher can download submissions for their own exercises
        if ($user->hasRole('Teacher') && $exercise->teacher_id === $user->id) {
            return true;
        }

        // Student can only download their own submission
        if ($user->hasRole('Student') && $user->id === $submissionStudentId) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Exercise $exercise): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exercise $exercise): bool
    {
        return $user->hasRole('Admin');
    }
}
