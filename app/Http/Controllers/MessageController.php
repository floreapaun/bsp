<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => $validated['sender_id'],
            'post_id' => $validated['post_id'],
            'content' => $validated['content'],
        ]);

        return response()->json($message, 201);
    }

    public function getMessages($postId)
    {
        $messages = Message::where('post_id', $postId)
            ->with(['sender']) 
            ->orderBy('created_at', 'asc') 
            ->get();

    
        return response()->json($messages);
    }

}
