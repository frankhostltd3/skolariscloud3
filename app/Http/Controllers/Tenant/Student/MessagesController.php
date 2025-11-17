<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    /**
     * Display the inbox
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.dashboard')
                ->with('error', 'Student record not found.');
        }

        // Get threads where student is a participant
        $query = MessageThread::where('is_active', true)
            ->whereJsonContains('participants', $user->id)
            ->with(['latestMessage.sender', 'creator'])
            ->orderBy('last_message_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhereHas('messages', function($mq) use ($search) {
                      $mq->where('content', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by unread
        if ($request->filter == 'unread') {
            $query->whereHas('messages', function($q) use ($user) {
                $q->where('sender_id', '!=', $user->id)
                  ->where('is_read', false);
            });
        }

        $threads = $query->paginate(15);

        // Get unread count
        $unreadCount = Message::whereHas('thread', function($q) use ($user) {
                $q->whereJsonContains('participants', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        // Get teachers for compose
        $teachers = Teacher::with('user')->get();

        // Get statistics
        $statistics = [
            'total_threads' => MessageThread::whereJsonContains('participants', $user->id)->count(),
            'unread_messages' => $unreadCount,
            'total_sent' => Message::where('sender_id', $user->id)->count(),
        ];

        return view('tenant.student.messages.index', compact('threads', 'unreadCount', 'teachers', 'student', 'statistics'));
    }

    /**
     * Display a specific thread
     */
    public function show($id)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.dashboard')
                ->with('error', 'Student record not found.');
        }

        $thread = MessageThread::with(['messages.sender', 'creator'])
            ->findOrFail($id);

        // Check if user is participant
        if (!$thread->hasParticipant($user->id)) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        // Mark messages as read
        Message::where('thread_id', $thread->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $messages = $thread->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return view('tenant.student.messages.show', compact('thread', 'messages', 'student'));
    }

    /**
     * Show compose form
     */
    public function create()
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.dashboard')
                ->with('error', 'Student record not found.');
        }

        $teachers = Teacher::with('user')->get();

        return view('tenant.student.messages.create', compact('teachers', 'student'));
    }

    /**
     * Send a new message (create thread)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.dashboard')
                ->with('error', 'Student record not found.');
        }

        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif'
        ]);

        // Create thread
        $thread = MessageThread::create([
            'subject' => $request->subject,
            'type' => 'student_teacher',
            'created_by' => $user->id,
            'last_message_at' => now(),
            'is_active' => true,
            'participants' => [$user->id, (int)$request->recipient_id],
        ]);

        // Handle attachment
        $attachments = [];
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('messages/attachments', $filename, 'public');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ];
        }

        // Create message
        Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $user->id,
            'content' => $request->content,
            'message_type' => 'text',
            'attachments' => $attachments,
            'is_read' => false,
        ]);

        return redirect()->route('tenant.student.messages.show', $thread->id)
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Reply to a thread
     */
    public function reply(Request $request, $id)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student) {
            return redirect()->route('tenant.student.dashboard')
                ->with('error', 'Student record not found.');
        }

        $thread = MessageThread::findOrFail($id);

        // Check if user is participant
        if (!$thread->hasParticipant($user->id)) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif'
        ]);

        // Handle attachment
        $attachments = [];
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('messages/attachments', $filename, 'public');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ];
        }

        // Create reply
        Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $user->id,
            'content' => $request->content,
            'message_type' => 'text',
            'attachments' => $attachments,
            'is_read' => false,
        ]);

        // Update thread
        $thread->update(['last_message_at' => now()]);

        return redirect()->route('tenant.student.messages.show', $thread->id)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Delete a thread
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $thread = MessageThread::findOrFail($id);

        // Check if user is participant
        if (!$thread->hasParticipant($user->id)) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        // Remove user from participants
        $thread->removeParticipant($user->id);

        // If no participants left, deactivate thread
        if (empty($thread->participants)) {
            $thread->update(['is_active' => false]);
        }

        return redirect()->route('tenant.student.messages.index')
            ->with('success', 'Conversation deleted successfully!');
    }
}