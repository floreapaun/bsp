<?php

namespace App\Http\Controllers;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        return Conversation::where('user_one_id', auth()->id())
            ->orWhere('user_two_id', auth()->id())
            ->with(['post', 'userOne', 'userTwo'])
            ->get();
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_two_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id'
        ]);

        $conversation = Conversation::firstOrCreate(
            [
                'post_id' => $validated['post_id'],
                'user_one_id' => auth()->id(),
                'user_two_id' => $validated['user_two_id'],
            ]
        );

        return response()->json($conversation, 201);
    }
 
}
