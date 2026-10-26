@extends('layouts.admin')

@section('title', 'View Message')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-1">View Message</h1>
            <p class="page-subtitle mb-0">Manage and reply to customer inquiries</p>
        </div>
        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary no-transition">
            <i class="fa fa-arrow-left mr-1"></i> Back to Messages
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $message->subject ?: 'No Subject' }}</h5>
                <div class="text-white-50 small">
                    From <strong>{{ $message->name }}</strong> · <a class="text-white" href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    · {{ $message->created_at->format('F d, Y \a\t H:i') }}
                    @if($message->is_read && $message->read_at)
                        · <i class="fa fa-eye"></i> Read on {{ $message->read_at->format('F d, Y \a\t H:i') }}
                    @endif
                </div>
            </div>
            <div>
                @if(!$message->is_read)
                    <form action="{{ route('admin.messages.read', $message) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check mr-1"></i> Mark as Read
                        </button>
                    </form>
                @endif
                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-primary">
                    <i class="fa fa-reply mr-1"></i> Reply via Email
                </a>
                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="message-content" style="background:#f8fbff; border-left:4px solid #3498db; padding:18px; border-radius:10px;">
                <h6 class="mb-2 text-primary"><i class="fa fa-comment mr-1"></i> Message Content</h6>
                <div style="white-space: pre-wrap; line-height: 1.8; color:#2c3e50;">{{ $message->message }}</div>
            </div>
        </div>
    </div>

    @if($message->replies->count() > 0)
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0"><i class="fa fa-comments mr-2"></i> Previous Replies ({{ $message->replies->count() }})</h5>
            </div>
            <div class="card-body">
                @foreach($message->replies as $reply)
                    <div class="border rounded p-3 mb-3" style="background:#f9fafb;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $reply->admin->name }}</strong>
                                <span class="text-muted">replied on {{ $reply->created_at->format('M d, Y \\a\\t H:i') }}</span>
                            </div>
                            <span class="badge {{ $reply->is_read_by_user ? 'badge-success' : 'badge-warning' }}">
                                {{ $reply->is_read_by_user ? 'Read by User' : 'Unread' }}
                            </span>
                        </div>
                        <div style="white-space: pre-wrap; line-height: 1.7;">{{ $reply->reply_message }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-reply mr-2"></i> Send Reply</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.messages.reply', $message) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="reply_message">Your Reply</label>
                    <textarea name="reply_message" id="reply_message" class="form-control" rows="5" required placeholder="Type your reply here...">{{ old('reply_message') }}</textarea>
                    @error('reply_message')
                        <div class="text-danger mt-1">{{ $errors->first('reply_message') }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane mr-1"></i> Send Reply
                </button>
            </form>
        </div>
    </div>
@endsection
