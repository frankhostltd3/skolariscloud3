<?php

namespace App\Policies;

use App\Models\TimetableEntry;
use App\Models\User;

class TimetableEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view timetables
    }

    public function view(User $user, TimetableEntry $model): bool
    {
        return true; // Everyone can view individual timetable entries
    }

    public function create(User $user): bool
    {
        return $user->can('manage academics'); // Only admins can create
    }

    public function update(User $user, TimetableEntry $model): bool
    {
        return $user->can('manage academics'); // Only admins can update
    }

    public function delete(User $user, TimetableEntry $model): bool
    {
        return $user->can('manage academics'); // Only admins can delete
    }

    public function manage(User $user, ?TimetableEntry $model = null): bool
    {
        return $user->can('manage academics'); // Only admins can manage timetables
    }
}