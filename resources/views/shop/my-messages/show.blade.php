<!DOCTYPE html>
<html lang="en">
<head>
    <title>Message Conversation - CozaStore</title>
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
        .conversation-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .conversation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        .message-bubble {
            margin-bottom: 20px;
        }
        .user-message {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 0 15px 15px 0;
        }
        .admin-reply {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 0 0 15px;
            margin-left: 50px;
        }
        .message-meta {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 10px;
        }
        .conversation-body {
            padding: 30px;
            max-height: 600px;
            overflow-y: auto;
        }
        .back-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body class="animsition">
    @include('layouts.sidebar')

    <div class="container" style="padding-top: 120px; padding-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="conversation-container">
                    <div class="conversation-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="mb-2">{{ $message->subject ?: 'No Subject' }}</h3>
                                <p class="mb-1">
                                    <i class="fa fa-calendar mr-2"></i>
                                    Started on {{ $message->created_at->format('F d, Y \a\t H:i') }}
                                </p>
                                <p class="mb-0">
                                    <i class="fa fa-comments mr-2"></i>
                                    {{ $message->replies->count() + 1 }} message{{ $message->replies->count() > 0 ? 's' : '' }} in this conversation
                                </p>
                            </div>
                            <a href="{{ route('shop.my-messages.index') }}" class="back-btn">
                                <i class="fa fa-arrow-left mr-2"></i>Back to Messages
                            </a>
                        </div>
                    </div>

                    <div class="conversation-body">
                        <!-- Original Message -->
                        <div class="message-bubble">
                            <div class="user-message">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa fa-user-circle fa-2x text-primary mr-3"></i>
                                    <div>
                                        <strong>You</strong>
                                        <div class="message-meta">
                                            {{ $message->created_at->format('F d, Y \a\t H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <div style="white-space: pre-wrap; line-height: 1.6;">{{ $message->message }}</div>
                            </div>
                        </div>

                        <!-- Replies -->
                        @foreach($message->replies as $reply)
                            <div class="message-bubble">
                                <div class="admin-reply">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa fa-user-tie fa-2x mr-3" style="color: rgba(255,255,255,0.8);"></i>
                                        <div>
                                            <strong>{{ $reply->admin->name }}</strong>
                                            <small class="d-block" style="color: rgba(255,255,255,0.8);">Support Team</small>
                                            <div class="message-meta">
                                                {{ $reply->created_at->format('F d, Y \a\t H:i') }}
                                                @if($reply->is_read_by_user)
                                                    <i class="fa fa-check-circle ml-2" title="You have read this reply"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div style="white-space: pre-wrap; line-height: 1.6;">{{ $reply->reply_message }}</div>
                                </div>
                            </div>
                        @endforeach

                        @if($message->replies->count() === 0)
                            <div class="text-center py-4">
                                <i class="fa fa-clock-o fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Waiting for Reply</h5>
                                <p class="text-muted">Our support team will respond to your message soon.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Footer -->
                    <div class="bg-light p-3 text-center">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fa fa-info-circle text-info mr-2"></i>
                                    <small class="text-muted">
                                        Need to send another message?
                                        <a href="{{ route('shop.contact') }}" class="text-primary">Contact us again</a>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fa fa-envelope text-success mr-2"></i>
                                    <small class="text-muted">
                                        You'll be notified when we reply to your message
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        // Auto-scroll to bottom of conversation
        $(document).ready(function() {
            $('.conversation-body').scrollTop($('.conversation-body')[0].scrollHeight);
        });
    </script>
</body>
</html>
