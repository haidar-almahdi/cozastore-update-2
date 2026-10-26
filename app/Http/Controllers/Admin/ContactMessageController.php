<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\MessageReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);
        $unreadCount = ContactMessage::unread()->count();
        $totalMessages = ContactMessage::count();

        return view('admin.messages.index', compact('messages', 'unreadCount', 'totalMessages'));
    }

    public function show(ContactMessage $message)
    {
        // Mark as read when viewed
        if (!$message->is_read) {
            $message->markAsRead();
        }

        // Load replies with admin info
        $message->load(['replies.admin']);

        return view('admin.messages.show', compact('message'));
    }

    public function markAsRead(ContactMessage $message)
    {
        $message->markAsRead();

        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $request->validate([
            'reply_message' => 'required|string|max:2000'
        ]);

        MessageReply::create([
            'contact_message_id' => $message->getKey(),
            'admin_user_id' => Auth::id(),
            'reply_message' => $request->input('reply_message'),
        ]);

        // Mark original message as read if not already
        if (!$message->is_read) {
            $message->markAsRead();
        }

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }
}
