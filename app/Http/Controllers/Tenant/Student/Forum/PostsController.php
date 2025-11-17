<?php

namespace App\Http\Controllers\Tenant\Student\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ThreadReplied;

class PostsController extends Controller
{
    public function store(Request $request, ForumCategory $category, ForumThread $thread)
    {
        abort_unless($thread->category_id === $category->id, 404);

        $data = $request->validate([
            'content' => ['required', 'string']
        ]);

        $post = ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);

        // Notify thread owner
        if ($thread->user && $thread->user->id !== Auth::id()) {
            $thread->user->notify(new ThreadReplied($post));
        }
        // Notify unique prior participants excluding current author and thread owner
        $participantIds = $thread->posts()->whereNotNull('user_id')->pluck('user_id')->unique()
            ->filter(fn($id) => $id !== Auth::id() && $id !== ($thread->user?->id))->all();
        if (!empty($participantIds)) {
            $notifiables = \App\Models\User::whereIn('id', $participantIds)->get();
            foreach ($notifiables as $user) {
                $user->notify(new ThreadReplied($post));
            }
        }

        return redirect()->route('tenant.student.forum.threads.show', [$category, $thread])->with('success', 'Reply posted.');
    }
}