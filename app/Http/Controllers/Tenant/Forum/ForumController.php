<?php

namespace App\Http\Controllers\Tenant\Forum;

use App\Http\Controllers\Controller;
use App\Models\Forum\ForumPost;
use App\Models\Forum\ForumThread;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumThread::with(['author', 'posts', 'context'])
            ->where('school_id', tenant('id'));

        // Filter by context (e.g., only my classes)
        if ($request->has('filter') && $request->filter == 'my_classes') {
            // Logic to filter by user's classes
            // For now, just show all active
        }

        $threads = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tenant.forum.index', compact('threads'));
    }

    public function show($slug)
    {
        $thread = ForumThread::where('slug', $slug)
            ->where('school_id', tenant('id'))
            ->with(['author', 'posts.author', 'posts.replies.author'])
            ->firstOrFail();

        // Increment view count
        $thread->increment('views_count');

        return view('tenant.forum.show', compact('thread'));
    }

    public function create()
    {
        // Get contexts (Classes, Subjects)
        $classes = SchoolClass::where('school_id', tenant('id'))->get();
        $subjects = Subject::where('school_id', tenant('id'))->get();

        return view('tenant.forum.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'context_type' => 'nullable|string|in:class,subject,general',
            'context_id' => 'nullable|integer',
        ]);

        $thread = ForumThread::create([
            'school_id' => tenant('id'),
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'context_type' => $request->context_type,
            'context_id' => $request->context_id,
            'status' => 'active',
        ]);

        return redirect()->route('tenant.forum.show', $thread->slug)
            ->with('success', 'Discussion started successfully.');
    }

    public function reply(Request $request, $slug)
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        $request->validate([
            'content' => 'required|string',
        ]);

        ForumPost::create([
            'school_id' => tenant('id'),
            'forum_thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Reply posted.');
    }

    public function aiAssist(Request $request)
    {
        $prompt = $request->input('prompt');
        $model = $request->input('model', 'gemini-pro-3');

        // Mock AI Response (matching existing pattern)
        $response = "I am $model. I received your prompt: \"$prompt\". Here is a suggested response for the forum discussion...";

        return response()->json(['response' => $response]);
    }

    public function updateStatus(Request $request, $slug)
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        // Check permissions (Admin or Moderator)
        if (!Auth::user()->hasRole('Admin') && Auth::id() != $thread->moderator_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:active,closed,blocked'
        ]);

        $thread->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Thread status updated.');
    }
}
