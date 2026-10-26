@php
use App\Models\Cart;
@endphp
<!DOCTYPE html>
<html lang="en">
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

        /* Transparent navbar on scroll */
        .header-v4.nav-transparent .wrap-menu-desktop {
            background: transparent !important;
            box-shadow: none !important;
        }
        .header-v4 .wrap-menu-desktop {
            transition: background-color .25s ease, box-shadow .25s ease;
        }
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

                    <div class="wrap-icon-header flex-w flex-r-m">
                        <a href="{{ route('shop.favorites') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                           data-notify="{{ auth()->check() ? auth()->user()->favorites()->count() : 0 }}"
                           title="Favorites">
                            <i class="zmdi zmdi-favorite"></i>
                        </a>

                        @auth
                            @php
                                $unreadRepliesHeader = \App\Models\MessageReply::whereHas('contactMessage', function($q){
                                    $q->where('email', Auth::user()->email ?? '');
                                })->where('is_read_by_user', false)->count();
                            @endphp
                            <a href="{{ route('shop.my-messages.index') }}" class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                               data-notify="{{ $unreadRepliesHeader }}"
                               title="My Messages">
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

        <!-- Search modal removed for no-search layout -->
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
        // Navbar transparency on scroll (no-search layout)
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>

    @yield('scripts')
</body>
</html>
