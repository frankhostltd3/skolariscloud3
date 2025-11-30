<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DiscussionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discussions = Discussion::with(['class', 'subject'])
            ->where('teacher_id', Auth::id())
            ->orderBy('is_pinned', 'desc')
            ->latest()
            ->paginate(15);

        return view('tenant.teacher.classroom.discussions.index', compact('discussions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Removed non-existent is_active filters to match current schema
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.discussions.create', compact('classes', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'type' => 'required|in:general,question,announcement,poll',
            'is_pinned' => 'nullable|boolean',
            'allow_replies' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'is_locked' => 'nullable|boolean',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif',
        ]);

        $validated['teacher_id'] = Auth::id();
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['allow_replies'] = $request->has('allow_replies');
        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_locked'] = $request->has('is_locked');

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('discussions/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $discussion = Discussion::create($validated);

        return redirect()
            ->route('tenant.teacher.classroom.discussions.show', $discussion)
            ->with('success', 'Discussion created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discussion $discussion)
    {
        $this->authorize('view', $discussion);

        $discussion->load(['class', 'subject', 'teacher', 'replies.user', 'replies.replies.user']);

        return view('tenant.teacher.classroom.discussions.show', compact('discussion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discussion $discussion)
    {
        $this->authorize('update', $discussion);

        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('tenant.teacher.classroom.discussions.edit', compact('discussion', 'classes', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discussion $discussion)
    {
        $this->authorize('update', $discussion);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'type' => 'required|in:general,question,announcement,poll',
            'is_pinned' => 'nullable|boolean',
            'allow_replies' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'is_locked' => 'nullable|boolean',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,gif',
        ]);

        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['allow_replies'] = $request->has('allow_replies');
        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_locked'] = $request->has('is_locked');

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = $discussion->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('discussions/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $discussion->update($validated);

        return redirect()
            ->route('tenant.teacher.classroom.discussions.show', $discussion)
            ->with('success', 'Discussion updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discussion $discussion)
    {
        $this->authorize('delete', $discussion);

        // Delete attachments
        if ($discussion->attachments) {
            foreach ($discussion->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $discussion->delete();

        return redirect()
            ->route('tenant.teacher.classroom.discussions.index')
            ->with('success', 'Discussion deleted successfully!');
    }

    public function reply(Request $request, Discussion $discussion)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:discussion_replies,id',
        ]);

        $discussion->replies()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => !$discussion->requires_approval, // Auto-approve if not required
        ]);

        return back()->with('success', 'Reply posted successfully!');
    }

    public function togglePin(Discussion $discussion)
    {
        $this->authorize('update', $discussion);
        $discussion->update(['is_pinned' => !$discussion->is_pinned]);
        return back()->with('success', $discussion->is_pinned ? 'Discussion pinned.' : 'Discussion unpinned.');
    }

        public function toggleLock(Discussion $discussion)
        {
            $this->authorize('update', $discussion);
            $discussion->update(['is_locked' => !$discussion->is_locked]);
            return back()->with('success', $discussion->is_locked ? 'Discussion locked.' : 'Discussion unlocked.');
        }
    }
