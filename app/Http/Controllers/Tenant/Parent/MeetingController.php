<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Assuming meetings are stored with participant IDs in a JSON column or similar,
        // or we can filter by organizer if the parent requested it.
        // For now, let's assume we want to show meetings where the parent is a participant.
        // Since 'participants' is a JSON array of user IDs:

        $meetings = Meeting::whereJsonContains('participants', $user->id)
            ->orWhere('organizer_id', $user->id)
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        $parent = $user->parentProfile;
        $students = $parent ? $parent->students : collect();

        return view('tenant.parent.meetings.index', compact('meetings', 'students'));
    }

    public function show(Meeting $meeting)
    {
        $user = Auth::user();

        // Check if user is participant or organizer
        $isParticipant = collect($meeting->participants)->contains($user->id);
        $isOrganizer = $meeting->organizer_id == $user->id;

        if (!$isParticipant && !$isOrganizer) {
            abort(403);
        }

        return view('tenant.parent.meetings.show', compact('meeting'));
    }
}
