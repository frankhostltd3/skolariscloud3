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
            ->latest('is_pinned', 'desc')
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
