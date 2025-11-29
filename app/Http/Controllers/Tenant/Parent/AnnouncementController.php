<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::forAudience(['parent'])->active()->latest()->paginate(10);

        return view('tenant.parent.announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        // Ensure the announcement is for parents
        if (!$announcement->isFor('parent')) {
            abort(403);
        }

        return view('tenant.parent.announcements.show', compact('announcement'));
    }
}
