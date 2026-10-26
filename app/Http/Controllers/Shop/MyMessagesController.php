<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\MessageReply;
use Illuminate\Http\Request;

class MyMessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Get user's messages with replies
        $messages = ContactMessage::where('email', $user->email)
            ->with(['replies.admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Count unread replies
        $unreadRepliesCount = MessageReply::whereHas('contactMessage', function($query) use ($user) {
            $query->where('email', $user->email);
        })->where('is_read_by_user', false)->count();

        return view('shop.my-messages.index', compact('messages', 'unreadRepliesCount'));
    }

    public function show(ContactMessage $message)
    {
        $user = auth()->user();

        // Check if this message belongs to the current user
        if ($message->email !== $user->email) {
            abort(403, 'Unauthorized access to this message.');
        }

        // Mark all replies as read by user
        $message->replies()->where('is_read_by_user', false)->each(function($reply) {
            $reply->markAsReadByUser();
        });

        // Load replies with admin info
        $message->load(['replies.admin']);

        return view('shop.my-messages.show', compact('message'));
    }
}
