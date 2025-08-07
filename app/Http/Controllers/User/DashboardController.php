<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return Inertia::render('Dashboard', compact('users'));
    }

    public function chat(Request $request, $id)
    {
        $authId = auth()->id();
        $otherUserId = $id; // Receiver's ID from request

        $messages = ChatMessage::with(['sender', 'receiver'])
            ->where(function ($query) use ($authId, $otherUserId) {
                $query->where('user_id', $authId)->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($authId, $otherUserId) {
                $query->where('user_id', $otherUserId)->where('receiver_id', $authId);
            })
            ->orderBy('created_at')
            ->get();

        return Inertia::render('UserChat', [
            'messages' => $messages,
            'receiver_id' =>  $id,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }
public function send(Request $request)
{
    $request->validate([
        'receiver' => 'required|exists:users,id',
        'type' => 'required|in:text,file',
        'content' => 'required_if:type,text',
        'file' => 'required_if:type,file|file',
    ]);

    $user = auth()->user();

    if ($request->type === 'text') {
        $message = $user->messages()->create([
            'receiver_id' => $request->receiver,
            'content' => $request->content,
            'type' => 'text',
        ]);
    } elseif ($request->type === 'file') {
        $path = $request->file('file')->store('chat_files', 'public');
        $message = $user->messages()->create([
            'receiver_id' => $request->receiver, // ✅ FIXED HERE
            'content' => basename($path),
            'type' => 'file',
        ]);
    }

    return back()->with('messages', $user->messages()->latest()->take(50)->get());
}

}
