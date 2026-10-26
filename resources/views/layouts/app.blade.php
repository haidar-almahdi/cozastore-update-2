@php
use App\Models\Cart;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <title>@yield('title', 'Cozastore')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    <style>
        .profile-container {
            transform: translate3d(0px, 40px, 0px) !important;
        }



        /* Floating Orders Button */
        .floating-orders-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }

        .btn-floating {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,123,255,0.4);
            transition: all 0.3s ease;
            position: relative;
        }

        .btn-floating:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,123,255,0.6);
            color: white;
            text-decoration: none;
        }

        .btn-floating i {
            font-size: 1.2rem;
        }

        .floating-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Hide on small screens to avoid overlap */
        @media (max-width: 768px) {
            .floating-orders-btn {
                bottom: 80px;
                left: 15px;
            }

            .btn-floating {
                width: 50px;
                height: 50px;
            }

            .btn-floating i {
                font-size: 1rem;
            }
        }

        /* Hide on orders pages to avoid redundancy */
        .orders-page .floating-orders-btn {
            display: none;
        }

        /* User Navigation Bar */
        .user-nav-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .user-nav-links {
            display: flex;
            gap: 10px;
        }

        .user-nav-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            font-size: 0.9rem;
            font-weight: 500;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .user-nav-btn:hover {
            color: white;
            background: rgba(255,255,255,0.2);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .user-nav-btn.active {
            color: white !important;
            background: rgba(255,255,255,0.25) !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
        }

        /* Ensure consistent styling across all pages */
        .user-nav-btn:hover {
            color: white !important;
            background: rgba(255,255,255,0.2) !important;
            text-decoration: none !important;
            transform: translateY(-1px) !important;
        }

        .user-nav-btn i {
            font-size: 0.9rem;
        }

        .nav-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid white;
        }

        @media (max-width: 768px) {
            .user-nav-bar .container {
                padding: 0 15px;
            }

            .user-nav-bar .d-flex {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .user-nav-links {
                justify-content: center;
                gap: 8px;
            }

            .user-nav-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            .user-nav-btn span {
                display: none;
            }

            .user-nav-btn i {
                font-size: 1rem;
            }
        }

        /* Orders Button in Header */
        .orders-btn-header {
            display: flex;
            align-items: center;
            background: #007bff;
            color: white !important;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .orders-btn-header:hover {
            background: #0056b3;
            color: white !important;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        .orders-btn-header i {
            margin-left: 8px;
            font-size: 1rem;
        }

        .orders-btn-header span {
            font-size: 0.85rem;
        }

        .orders-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .orders-btn-header {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            .orders-btn-header span {
                display: none;
            }

            .orders-btn-header i {
                margin-left: 0;
                font-size: 1.1rem;
            }
        }

        /* Force display for orders button */
        .orders-btn-header {
            display: flex !important;
            visibility: visible !important;
        }

        /* Ensure user nav bar is visible */
        .user-nav-bar {
            display: block !important;
            visibility: visible !important;
        }

        /* Main menu orders link styling */
        .main-menu li a[href*="orders"] {
            color: #007bff !important;
            font-weight: 600 !important;
        }

        /* Quick Search Styles */
        .quick-search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 2px;
        }

        .quick-search-suggestions .suggestion-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
            font-size: 0.9rem;
        }

        .quick-search-suggestions .suggestion-item:hover,
        .quick-search-suggestions .suggestion-item.active {
            background-color: #f8f9fa;
        }

        .quick-search-suggestions .suggestion-item:last-child {
            border-bottom: none;
        }

        .quick-search-suggestions .suggestion-item i {
            color: #7494ec;
            width: 14px;
            font-size: 0.8rem;
        }

        .quick-search-suggestions .suggestion-item span {
            flex-grow: 1;
            font-weight: 500;
        }

        .quick-search-suggestions .suggestion-type {
            color: #666;
            font-size: 0.7rem;
            text-transform: capitalize;
            background: #e9ecef;
            padding: 1px 6px;
            border-radius: 10px;
        }

        .quick-search-bar {
            position: relative;
            flex: 1 1 auto;
            max-width: 300px;
            margin: 0 auto;
        }

        .wrap-icon-header {
            flex-wrap: nowrap !important;
            align-items: center !important;
        }

        .wrap-icon-header .nav-item.dropdown {
            list-style: none;
            margin-bottom: 0 !important;
            display: flex;
            align-items: center;
        }

        .quick-search-form {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #e7eaf0;
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .quick-search-form:focus-within {
            border-color: #7d95ee;
            box-shadow: 0 8px 24px rgba(116, 148, 236, 0.25);
            transform: translateY(-1px);
        }

        .quick-search-input {
            border: none;
            box-shadow: none !important;
            background: transparent;
            height: 42px;
            font-size: 0.92rem;
            padding: 0 14px;
        }

        .quick-search-input::placeholder {
            color: #9aa3b2;
        }

        .quick-search-btn {
            border: none;
            border-left: 1px solid #eef1f6;
            background: linear-gradient(135deg, #7494ec 0%, #8a66ea 100%);
            color: #fff;
            width: 44px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .quick-search-btn:hover {
            filter: brightness(0.95);
        }
        
        /* Transparent navbar on scroll */
        .header-v4.nav-transparent .wrap-menu-desktop {
            background: transparent !important;
            box-shadow: none !important;
        }
        .header-v4 .wrap-menu-desktop {
            transition: background-color .25s ease, box-shadow .25s ease;
        }
        /* Optional: soften background when not transparent */
        .header-v4:not(.nav-transparent) .wrap-menu-desktop {
            background: #ffffff !important;
            box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        }
    </style>
    @yield('styles')
</head>
<body class="animsition">


    <!-- Header -->
    <header class="header-v4">
        <div class="container-menu-desktop">
            <div class="wrap-menu-desktop">
                <nav class="limiter-menu-desktop container">
                    <a href="{{ route('shop.home') }}" class="logo">
                        <img src="{{ asset('images/icons/logo-01.png') }}" alt="IMG-LOGO">
                    </a>

                    <div class="menu-desktop">
                        <ul class="main-menu">
                            <li class="{{ request()->routeIs('shop.home') ? 'active-menu' : '' }}">
                                <a href="{{ route('shop.home') }}">Home</a>
                            </li>
                            <li class="{{ request()->routeIs('shop.products.*') ? 'active-menu' : '' }}">
                                <a href="{{ route('shop.products.index') }}">Shop</a>
                            </li>
                            <li class="{{ request()->routeIs('shop.deals.black-friday') ? 'active-menu' : '' }}">
                                <a href="{{ route('shop.deals.black-friday') }}">Black&nbsp;Friday</a>
                            </li>

                            <li class="{{ request()->routeIs('shop.about') ? 'active-menu' : '' }}">
                                <a href="{{ route('shop.about') }}">About</a>
                            </li>
                            <li class="{{ request()->routeIs('shop.contact') ? 'active-menu' : '' }}">
                                <a href="{{ route('shop.contact') }}">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick Search Bar -->
                    <div class="quick-search-bar d-none d-lg-block">
                        <form action="{{ route('shop.search') }}" method="GET" class="quick-search-form">
                            <input type="text" name="q" placeholder="Search products..." class="form-control quick-search-input" id="quick-search-input" autocomplete="off">
                            <button type="submit" class="quick-search-btn" aria-label="Search">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>

                        <!-- Quick Search Suggestions -->
                        <div id="quick-search-suggestions" class="quick-search-suggestions" style="display: none;">
                            <div class="suggestions-list"></div>
                        </div>
                    </div>

                    <div class="wrap-icon-header flex-w flex-r-m">
                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search d-lg-none">
                            <i class="zmdi zmdi-search"></i>
                        </div>

                        <a href="{{ route('shop.favorites') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11">
                            <i class="zmdi zmdi-favorite"></i>
                        </a>

                        @auth
                            @php
                                $unreadRepliesHeader = \App\Models\MessageReply::whereHas('contactMessage', function($q){
                                    $q->where('email', Auth::user()->email ?? '');
                                })->where('is_read_by_user', false)->count();
                            @endphp
                            <a href="{{ route('shop.my-messages.index') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                               data-notify="{{ $unreadRepliesHeader }}" title="My Messages">
                                <i class="zmdi zmdi-email"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11" title="Login to view messages">
                                <i class="zmdi zmdi-email"></i>
                            </a>
                        @endauth
                        
                        @auth
                            <a href="{{ route('shop.my-points.index') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                               data-notify="{{ auth()->user()->loyalty_points_balance ?? 0 }}"
                               title="My Points">
                                <i class="zmdi zmdi-star"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11" title="Login to see your points">
                                <i class="zmdi zmdi-star"></i>
                            </a>
                        @endauth

                        @auth
                            <a href="{{ route('shop.orders.index') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                               data-notify="{{ auth()->user()->orders()->where('status', 'pending')->count() }}"
                               title="My Orders">
                                <i class="zmdi zmdi-assignment"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11" title="Login to see your orders">
                                <i class="zmdi zmdi-assignment"></i>
                            </a>
                        @endauth

                        

                        <a href="{{ route('shop.cart') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="{{ auth()->check() ? auth()->user()->activeCart?->item_count ?? 0 : session('cart_count', 0) }}">
                            <i class="zmdi zmdi-shopping-cart"></i>
                        </a>

                        @auth
                            <li class="nav-item dropdown p-l-22 p-r-11">
                                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ Auth::user()->profile_photo ?? asset('images/product-min-01.jpg') }}" alt="User" width="30" height="30" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-container" aria-labelledby="userDropdown">
                                    @if(Auth::user()->role === 'admin')
                                        <a class="dropdown-item" href="{{ route('admin.home') }}">
                                            <i class="fa fa-tachometer-alt mr-2"></i> Admin Dashboard
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('shop.profile.edit') }}">
                                        <i class="fa fa-user-edit mr-2"></i> Edit Profile
                                    </a>

                                    @php
                                        $unreadRepliesCount = \App\Models\MessageReply::whereHas('contactMessage', function($q){
                                            $q->where('email', Auth::user()->email ?? '');
                                        })->where('is_read_by_user', false)->count();
                                    @endphp
                                    <a class="dropdown-item position-relative" href="{{ route('shop.my-messages.index') }}">
                                        <i class="fa fa-envelope mr-2"></i> My Messages
                                        @if($unreadRepliesCount > 0)
                                            <span class="nav-badge" style="position:absolute; top:8px; right:12px;">{{ $unreadRepliesCount }}</span>
                                        @endif
                                    </a>

                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="px-3 py-1">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger p-0">
                                            <i class="fa fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @else
                            <a href="{{ route('login') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11">
                                <i class="zmdi zmdi-account"></i>
                            </a>
                        @endauth
                    </div>
                </nav>
            </div>
        </div>

        <div class="wrap-header-mobile">
            <div class="logo-mobile">
                <a href="{{ route('shop.home') }}"><img src="{{ asset('images/icons/logo-01.png') }}" alt="IMG-LOGO"></a>
            </div>

            <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </div>
        </div>

        <div class="menu-mobile">
            <ul class="topbar-mobile">
                <li>
                    <div class="left-top-bar">
                        Free shipping for standard order over $100
                    </div>
                </li>
            </ul>

            <ul class="main-menu-m">
                <li>
                    <a href="{{ route('shop.home') }}">Home</a>
                </li>
                <li>
                    <a href="{{ route('shop.products.index') }}">Shop</a>
                </li>
                <li>
                    <a href="{{ route('shop.deals.black-friday') }}">Black&nbsp;Friday</a>
                </li>

                <li>
                    <a href="{{ route('shop.about') }}">About</a>
                </li>
                <li>
                    <a href="{{ route('shop.contact') }}">Contact</a>
                </li>
            </ul>
        </div>

        <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
            <div class="container-search-header">
                <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                    <img src="{{ asset('images/icons/icon-close2.png') }}" alt="CLOSE">
                </button>

                <form class="wrap-search-header flex-w p-l-15">
                    <button class="flex-c-m trans-04">
                        <i class="zmdi zmdi-search"></i>
                    </button>
                    <input class="plh3" type="text" name="search" placeholder="Search...">
                </form>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div id="sidebar" style="display:none;position:fixed;top:0;right:0;width:300px;height:100%;background:#fff;z-index:9999;box-shadow:-2px 0 5px rgba(0,0,0,0.1);padding:30px;">
        <button class="close" id="sidebarClose" style="position:absolute;top:10px;right:10px;font-size:2rem;">&times;</button>
        @auth
            <li class="nav-item dropdown">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{ Auth::user()->profile_photo ?? asset('images/product-min-01.jpg') }}" alt="User" width="40" height="40" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-container" aria-labelledby="userDropdown">
                    @if(Auth::user()->role === 'admin')
                        <a class="dropdown-item" href="{{ route('admin.home') }}">
                            <i class="fa fa-tachometer-alt mr-2"></i> Admin Dashboard
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a class="dropdown-item" href="{{ route('shop.profile.edit') }}">
                        <i class="fa fa-user-edit mr-2"></i> Edit Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout.form') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left;">
                            <i class="fa fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        @endauth
        <ul class="list-unstyled mt-5">
            <li><a href="{{ route('shop.home') }}">Home</a></li>
            <li><a href="{{ route('shop.products.index') }}">Shop</a></li>
            <li><a href="{{ route('shop.deals.black-friday') }}">Black&nbsp;Friday</a></li>
            @auth

                <li><a href="{{ route('shop.favorites') }}">Favorites</a></li>
                @php
                    $unreadRepliesCount = \App\Models\MessageReply::whereHas('contactMessage', function($q){
                        $q->where('email', Auth::user()->email ?? '');
                    })->where('is_read_by_user', false)->count();
                @endphp
                <li>
                    <a href="{{ route('shop.my-messages.index') }}" class="position-relative">
                        My Messages
                        @if($unreadRepliesCount > 0)
                            <span class="nav-badge" style="position:absolute; top:-6px; right:-10px;">{{ $unreadRepliesCount }}</span>
                        @endif
                    </a>
                </li>
                <li><a href="{{ route('shop.profile.edit') }}">Edit Profile</a></li>
                <li><a href="{{ route('shop.cart') }}">Cart</a></li>
            @endauth
            <li><a href="{{ route('shop.about') }}">About</a></li>
            <li><a href="{{ route('shop.contact') }}">Contact</a></li>
        </ul>
    </div>



    <!-- Content -->
    @yield('content')

    @auth
    <!-- Floating Orders Button -->
    <div class="floating-orders-btn">
        <a href="{{ route('shop.orders.index') }}" class="btn-floating" title="فواتيري">
            <i class="fa fa-file-text-o"></i>
            @if(Auth::user()->orders()->where('status', 'pending')->count() > 0)
                <span class="floating-badge">{{ Auth::user()->orders()->where('status', 'pending')->count() }}</span>
            @endif
        </a>
    </div>
    @endauth

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        // Quick Search Autocomplete
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar transparency on scroll
            const header = document.querySelector('.header-v4');
            const toggleTransparent = () => {
                if (!header) return;
                if (window.scrollY > 60) {
                    header.classList.add('nav-transparent');
                } else {
                    header.classList.remove('nav-transparent');
                }
            };
            toggleTransparent();
            window.addEventListener('scroll', toggleTransparent, { passive: true });

            const quickSearchInput = document.getElementById('quick-search-input');
            const quickSuggestionsContainer = document.getElementById('quick-search-suggestions');
            const quickSuggestionsList = quickSuggestionsContainer?.querySelector('.suggestions-list');
            let quickSearchTimeout;

            if (quickSearchInput && quickSuggestionsContainer) {
                quickSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(quickSearchTimeout);

                    if (query.length < 2) {
                        quickSuggestionsContainer.style.display = 'none';
                        return;
                    }

                    quickSearchTimeout = setTimeout(() => {
                        fetch(`{{ route('shop.search.suggestions') }}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(suggestions => {
                                if (suggestions.length > 0) {
                                    quickSuggestionsList.innerHTML = suggestions.map(suggestion => `
                                        <div class="suggestion-item" data-text="${suggestion.text}">
                                            <i class="fa fa-${suggestion.type === 'product' ? 'cube' : 'folder'}"></i>
                                            <span>${suggestion.text}</span>
                                            <small class="suggestion-type">${suggestion.type}</small>
                                        </div>
                                    `).join('');

                                    quickSuggestionsContainer.style.display = 'block';

                                    // Add click handlers to suggestions
                                    quickSuggestionsList.querySelectorAll('.suggestion-item').forEach(item => {
                                        item.addEventListener('click', function() {
                                            quickSearchInput.value = this.dataset.text;
                                            quickSuggestionsContainer.style.display = 'none';
                                            quickSearchInput.closest('form').submit();
                                        });
                                    });
                                } else {
                                    quickSuggestionsContainer.style.display = 'none';
                                }
                            })
                            .catch(error => {
                                console.error('Quick search suggestions error:', error);
                                quickSuggestionsContainer.style.display = 'none';
                            });
                    }, 300);
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (!quickSearchInput.contains(e.target) && !quickSuggestionsContainer.contains(e.target)) {
                        quickSuggestionsContainer.style.display = 'none';
                    }
                });

                // Handle keyboard navigation
                quickSearchInput.addEventListener('keydown', function(e) {
                    const suggestions = quickSuggestionsList.querySelectorAll('.suggestion-item');
                    const activeSuggestion = quickSuggestionsList.querySelector('.suggestion-item.active');

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (activeSuggestion) {
                            activeSuggestion.classList.remove('active');
                            const next = activeSuggestion.nextElementSibling;
                            if (next) {
                                next.classList.add('active');
                            } else {
                                suggestions[0]?.classList.add('active');
                            }
                        } else {
                            suggestions[0]?.classList.add('active');
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (activeSuggestion) {
                            activeSuggestion.classList.remove('active');
                            const prev = activeSuggestion.previousElementSibling;
                            if (prev) {
                                prev.classList.add('active');
                            } else {
                                suggestions[suggestions.length - 1]?.classList.add('active');
                            }
                        } else {
                            suggestions[suggestions.length - 1]?.classList.add('active');
                        }
                    } else if (e.key === 'Enter') {
                        if (activeSuggestion) {
                            e.preventDefault();
                            quickSearchInput.value = activeSuggestion.dataset.text;
                            quickSuggestionsContainer.style.display = 'none';
                            quickSearchInput.closest('form').submit();
                        }
                    } else if (e.key === 'Escape') {
                        quickSuggestionsContainer.style.display = 'none';
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
