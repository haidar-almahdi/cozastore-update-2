@extends('layouts.app-no-search')

@section('title', 'Contact Us - CozaStore')

@section('styles')
<style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .contact-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInLeft 0.8s ease-out;
        }

        .contact-info {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            color: white;
            padding: 50px;
            border-radius: 25px;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .form-control {
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 15px;
            padding: 18px;
            font-size: 16px;
            transition: all 0.4s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.15);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .btn-send {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 18px 45px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-send::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-send:hover::before {
            left: 100%;
        }

        .btn-send:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(10px);
        }

        .contact-item:hover i {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Logo in Contact Item */
        .contact-icon-logo {
            font-size: 20px;
            margin-right: 20px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            padding: 8px;
        }

        .contact-icon-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1); /* Makes logo white */
        }

        .contact-item:hover .contact-icon-logo {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Contact Title Styling */
        .contact-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 3rem;
            color: white !important;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            letter-spacing: 2px;
            position: relative;
            animation: titleFadeIn 1s ease-out;
        }

        .contact-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.7) 100%);
            border-radius: 2px;
            animation: underlineDraw 1s ease-out 0.5s both;
        }

        @keyframes titleFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes underlineDraw {
            from {
                width: 0;
            }
            to {
                width: 80px;
            }
        }

        @media (max-width: 768px) {
            .contact-title {
                font-size: 2.2rem;
                letter-spacing: 1px;
            }
        }

        .contact-item i {
            font-size: 20px;
            margin-right: 20px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

    </style>
@endsection

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-5 text-center contact-title">Contact Us</h2>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle mr-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="contact-form">
                    <h4 class="mb-4">Send us a Message</h4>

                    @guest
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>
                            <strong>Tip:</strong> <a href="{{ route('login') }}">Login</a> to automatically fill your name and email, and to track replies to your messages.
                        </div>
                    @endguest

                    <form method="POST" action="{{ route('shop.contact.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">Full Name *</label>
                                    @auth
                                        <input type="text" name="name" id="name" class="form-control"
                                               value="{{ auth()->user()->name }}" readonly
                                               style="background-color: #f8f9fa; cursor: not-allowed;">
                                        <small class="text-muted">Using your registered name</small>
                                    @else
                                        <input type="text" name="name" id="name" class="form-control"
                                               value="{{ old('name') }}" required placeholder="Enter your full name">
                                    @endauth
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="font-weight-bold">Email Address *</label>
                                    @auth
                                        <input type="email" name="email" id="email" class="form-control"
                                               value="{{ auth()->user()->email }}" readonly
                                               style="background-color: #f8f9fa; cursor: not-allowed;">
                                        <small class="text-muted">Using your registered email address</small>
                                    @else
                                        <input type="email" name="email" id="email" class="form-control"
                                               value="{{ old('email') }}" required placeholder="Enter your email">
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject" class="font-weight-bold">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control"
                                   value="{{ old('subject') }}" placeholder="Enter message subject (optional)">
                        </div>

                        <div class="form-group">
                            <label for="message" class="font-weight-bold">Message *</label>
                            <textarea name="message" id="message" class="form-control" rows="6"
                                      required placeholder="Enter your message here...">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-send">
                            <i class="fa fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="contact-info">
                    <h4 class="mb-4">Get in Touch</h4>
                    <p class="mb-4">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

                    <div class="contact-item">
                        <div class="contact-icon-logo">
                            <img src="{{ asset('images/icons/logo-01.png') }}" alt="CozaStore Logo">
                        </div>
                        <div>
                            <strong>Address</strong><br>
                            SPU, Dier Ali, Ghabagheb, DR 10018
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fa fa-phone"></i>
                        <div>
                            <strong>Phone</strong><br>
                            (+963) 99 276 2770<br>
                            (+963) 98 879 7331
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fa fa-envelope"></i>
                        <div>
                            <strong>Email</strong><br>
                            info@cozastore.com<br>
                            support@cozastore.com
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Form validation
        $('form').on('submit', function(e) {
            let isValid = true;

            // Check required fields
            $(this).find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Check email format
            const email = $('#email').val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                isValid = false;
                $('#email').addClass('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
            }
        });

        // Remove invalid class on input
        $('.form-control').on('input', function() {
            $(this).removeClass('is-invalid');
        });
    </script>
@endsection
