<?php

namespace App\Http\Controllers\Tenant\Student\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ThreadsController extends Controller
{
    public function index(ForumCategory $category)
    {
        $threads = $category->threads()->where('is_published', true)->orderByDesc('is_pinned')->paginate(20);
        return view('tenant.student.forum.threads.index', compact('category', 'threads'));
    }

    public function create(ForumCategory $category)
    {
        return view('tenant.student.forum.threads.create', compact('category'));
    }

    public function store(Request $request, ForumCategory $category)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);
        $thread = ForumThread::create([
            'category_id' => $category->id,
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::random(6),
            'content' => $data['content'],
            'is_published' => true,
        ]);

        return redirect()->route('tenant.student.forum.threads.show', [$category, $thread])->with('success', 'Thread created.');
    }

    public function show(ForumCategory $category, ForumThread $thread)
    {
        abort_unless($thread->category_id === $category->id, 404);
        $thread->load(['user', 'posts.user']);
        return view('tenant.student.forum.threads.show', [
            'category' => $category,
            'thread' => $thread,
            'context' => 'student',
        ]);
    }
}