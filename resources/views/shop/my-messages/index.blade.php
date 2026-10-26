<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Messages - CozaStore</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animsition/css/animsition.min.css') }}">
    <style>
        .message-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .message-card:hover {
            transform: translateY(-5px);
        }
        .message-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        .message-body {
            padding: 20px;
        }
        .reply-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        .no-reply-badge {
            background: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        .unread-replies {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="animsition">
    @include('layouts.sidebar')

    <div class="container" style="padding-top: 120px; padding-bottom: 50px;">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4 text-center">My Messages</h2>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h3>{{ $messages->total() }}</h3>
                            <p class="mb-0">Total Messages</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h3>{{ $messages->filter(function($message) { return $message->replies->count() > 0; })->count() }}</h3>
                            <p class="mb-0">Messages with Replies</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h3>{{ $unreadRepliesCount }}</h3>
                            <p class="mb-0">Unread Replies</p>
                        </div>
                    </div>
                </div>

                @if($messages->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-envelope-o" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <h4 class="text-muted">No messages yet</h4>
                        <p class="text-muted">You haven't sent any messages to us yet.</p>
                        <a href="{{ route('shop.contact') }}" class="btn btn-primary">
                            <i class="fa fa-paper-plane mr-2"></i>Send Your First Message
                        </a>
                    </div>
                @else
                    @foreach($messages as $message)
                        <div class="message-card">
                            <div class="message-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1">{{ $message->subject ?: 'No Subject' }}</h5>
                                        <small>Sent on {{ $message->created_at->format('F d, Y \a\t H:i') }}</small>
                                    </div>
                                    <div class="text-right">
                                        @if($message->replies->count() > 0)
                                            @php
                                                $unreadReplies = $message->replies->where('is_read_by_user', false)->count();
                                            @endphp
                                            @if($unreadReplies > 0)
                                                <span class="unread-replies">
                                                    <i class="fa fa-bell mr-1"></i>{{ $unreadReplies }} New Reply{{ $unreadReplies > 1 ? 'ies' : '' }}
                                                </span>
                                            @else
                                                <span class="reply-badge">
                                                    <i class="fa fa-check mr-1"></i>{{ $message->replies->count() }} Reply{{ $message->replies->count() > 1 ? 'ies' : '' }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="no-reply-badge">
                                                <i class="fa fa-clock-o mr-1"></i>No Reply Yet
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="message-body">
                                <div class="mb-3">
                                    <strong>Your Message:</strong>
                                    <p class="mt-2">{{ Str::limit($message->message, 200) }}</p>
                                </div>

                                @if($message->replies->count() > 0)
                                    <div class="mb-3">
                                        <strong>Latest Reply:</strong>
                                        <div class="bg-light p-3 rounded mt-2">
                                            <p class="mb-1">{{ Str::limit($message->replies->last()->reply_message, 150) }}</p>
                                            <small class="text-muted">
                                                By {{ $message->replies->last()->admin->name }} on
                                                {{ $message->replies->last()->created_at->format('M d, Y \a\t H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                @endif

                                <div class="text-right">
                                    <a href="{{ route('shop.my-messages.show', $message) }}" class="btn btn-primary">
                                        <i class="fa fa-eye mr-2"></i>View Full Conversation
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if($messages->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $messages->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
