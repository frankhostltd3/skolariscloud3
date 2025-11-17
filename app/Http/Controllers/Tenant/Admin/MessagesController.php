<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    /**
     * Display a listing of message threads.
     */
    public function index()
    {
        $threads = MessageThread::with(['creator', 'latestMessage.sender'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('created_by', Auth::id())
                      ->orWhereJsonContains('participants', Auth::id());
            })
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('tenant.admin.messages.index', compact('threads'));
    }

    /**
     * Show the form for creating a new message thread.
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->where('id', '!=', Auth::id())->get();
        return view('tenant.admin.messages.create', compact('users'));
    }

    /**
     * Store a newly created message thread.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'nullable|string|max:255',
            'recipients' => 'required|array|min:1',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create thread
        $thread = MessageThread::create([
            'subject' => $request->subject ?: 'New Conversation',
            'type' => count($request->recipients) > 1 ? 'group' : 'direct',
            'created_by' => Auth::id(),
            'participants' => array_merge($request->recipients, [Auth::id()]),
            'last_message_at' => now(),
        ]);

        // Create first message
        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => Auth::id(),
            'content' => $request->message,
            'message_type' => 'text',
        ]);

        // Create recipients
        foreach ($request->recipients as $recipientId) {
            MessageRecipient::create([
                'message_id' => $message->id,
                'recipient_id' => $recipientId,
            ]);
        }

        return redirect()->route('tenant.admin.messages.show', $thread)
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Display the specified message thread.
     */
    public function show(MessageThread $thread)
    {
        // Check if user has access to this thread
        if (!$thread->hasParticipant(Auth::id())) {
            abort(403, 'You do not have access to this conversation.');
        }

        $messages = $thread->messages()
            ->with(['sender', 'recipients.recipient'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read for current user
        MessageRecipient::where('recipient_id', Auth::id())
            ->whereIn('message_id', $messages->pluck('id'))
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('tenant.admin.messages.show', compact('thread', 'messages'));
    }

    /**
     * Store a new message in the thread.
     */
    public function reply(Request $request, MessageThread $thread)
    {
        // Check if user has access to this thread
        if (!$thread->hasParticipant(Auth::id())) {
            abort(403, 'You do not have access to this conversation.');
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create message
        $message = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => Auth::id(),
            'content' => $request->message,
            'message_type' => 'text',
        ]);

        // Create recipients (all participants except sender)
        $recipients = array_diff($thread->participants ?? [], [Auth::id()]);
        foreach ($recipients as $recipientId) {
            MessageRecipient::create([
                'message_id' => $message->id,
                'recipient_id' => $recipientId,
            ]);
        }

        // Update thread's last message timestamp
        $thread->update(['last_message_at' => now()]);

        return redirect()->back()
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Get unread message count for the current user.
     */
    public function unreadCount()
    {
        $count = MessageRecipient::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark thread as read for current user.
     */
    public function markAsRead(MessageThread $thread)
    {
        if (!$thread->hasParticipant(Auth::id())) {
            abort(403);
        }

        MessageRecipient::where('recipient_id', Auth::id())
            ->whereHas('message', function ($query) use ($thread) {
                $query->where('thread_id', $thread->id);
            })
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}