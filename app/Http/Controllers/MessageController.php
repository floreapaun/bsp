<?php

namespace App\Http\Controllers;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($conversationId)
    {
        return Message::where('conversation_id', $conversationId)
        ->where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->orWhere('sender_id', '!=', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->get();    
    }

    public function show($id)
    {
        $conversation = Conversation::with('messages.sender')
            ->where('id', $id)
            ->firstOrFail();

        return $conversation;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'receiver_id' => 'required',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $validated['conversation_id'],
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
        ]);

        return response()->json($message, 201);
    }

    public function lastMessage($conversationId)
    {
        return Message::where('conversation_id', $conversationId)
        ->latest('created_at') 
        ->first(); 
    }

    public function markAsRead(Request $request)
    {
        $messageIds = $request->input('message_ids');

        Message::whereIn('id', $messageIds)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
