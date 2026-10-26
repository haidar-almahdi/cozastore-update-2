<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <title>@yield('title', 'Admin Dashboard') - CozaStore</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #4a5f7a 0%, #5a6c7d 50%, #4a5f7a 100%);
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .sidebar {
            min-height: 100vh;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            background: rgba(44, 62, 80, 0.95);
            backdrop-filter: blur(10px);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            border-right: 1px solid rgba(255,255,255,0.1);
            transition: transform 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: rgba(52, 152, 219, 0.8) rgba(255, 255, 255, 0.08);
        }

        .sidebar::-webkit-scrollbar {
            width: 10px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            margin: 6px 0;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3498db, #2980b9);
            border-radius: 12px;
            border: 2px solid rgba(44, 62, 80, 0.85);
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #5dade2, #3498db);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            position: relative;
        }

        .sidebar-header h4 {
            color: #3498db;
            font-weight: 600;
            margin: 0;
            font-size: 1.2rem;
        }

        .sidebar-header small {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 5px 15px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: rgba(52, 73, 94, 0.8);
            color: #3498db;
            transform: translateX(5px);
            text-decoration: none;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 12px;
            font-size: 1rem;
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .sidebar-toggle-btn {
            border-radius: 10px;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(52, 73, 94, 0.9);
            color: #ecf0f1;
            transition: all 0.25s ease;
        }

        .sidebar-toggle-btn:hover {
            background: rgba(41, 128, 185, 0.95);
            color: #fff;
            transform: translateY(-1px);
        }

        .sidebar-toggle-inside {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 5;
        }

        .sidebar-reopen-btn {
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 1200;
            display: none;
        }

        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0;
        }

        body.sidebar-collapsed .sidebar-reopen-btn {
            display: inline-flex;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .card:hover,
        .card.elevated {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        .card-header {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px 25px;
            border: none;
        }

        .card-body {
            padding: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .stat-card.blue { border-top: 4px solid #3498db; }
        .stat-card.green { border-top: 4px solid #27ae60; }
        .stat-card.orange { border-top: 4px solid #f39c12; }
        .stat-card.red { border-top: 4px solid #e74c3c; }
        .stat-card.purple { border-top: 4px solid #9b59b6; }
        .stat-card.teal { border-top: 4px solid #1abc9c; }
        .stat-card.yellow { border-top: 4px solid #f1c40f; }
        .stat-card.pink { border-top: 4px solid #e91e63; }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            opacity: 0.2;
        }

        .table {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(45deg, #34495e, #2c3e50);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(52, 152, 219, 0.1);
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: rgba(0,0,0,0.05);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .page-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .page-subtitle {
            color: #7f8c8d;
            margin: 5px 0 0 0;
            font-size: 1rem;
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(44, 62, 80, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0; }
            50% { transform: translateY(-100px) rotate(180deg); opacity: 1; }
        }

        /* Enhanced Card Animations */
        .card {
            transform: translateY(20px);
            opacity: 0;
            animation: slideInUp 0.6s ease forwards;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }

        @keyframes slideInUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Stat Card Pulse Effect */
        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s ease;
            opacity: 0;
        }

        .stat-card:hover::before {
            animation: shine 0.6s ease;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
        }

        /* Enhanced Button Effects */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Sidebar Animation */
        .sidebar .nav-link {
            position: relative;
        }

        .sidebar .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: #3498db;
            transition: width 0.3s ease;
        }

        .sidebar .nav-link:hover::after,
        .sidebar .nav-link.active::after {
            width: 100%;
        }

        /* Table Row Hover Effect */
        .table tbody tr {
            position: relative;
        }

        .table tbody tr::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(52, 152, 219, 0.1), transparent);
            transition: width 0.3s ease;
            pointer-events: none;
            z-index: 0;
        }

        .table tbody tr:hover::before {
            width: 100%;
        }

        .table tbody td {
            position: relative;
            z-index: 1;
        }

        /* Page Header Animation */
        .page-header {
            animation: fadeInDown 0.8s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Filter Section Animation */
        .filter-section {
            animation: fadeInLeft 0.8s ease;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Success/Error Messages Animation */
        .alert {
            animation: slideInRight 0.5s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* RTL Support */
        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
        }

        [dir="rtl"] .main-content {
            margin-left: 0;
            margin-right: 280px;
        }

        [dir="rtl"] .sidebar-toggle-btn {
            left: auto;
        }

        [dir="rtl"] body.sidebar-collapsed .sidebar {
            transform: translateX(100%);
        }

        [dir="rtl"] body.sidebar-collapsed .main-content {
            margin-right: 0;
        }

        [dir="rtl"] .sidebar-toggle-inside {
            right: auto;
            left: 14px;
        }

        [dir="rtl"] .sidebar-reopen-btn {
            left: auto;
            right: 14px;
        }

        [dir="rtl"] .sidebar .nav-link i {
            margin-right: 0;
            margin-left: 12px;
        }

        [dir="rtl"] .d-flex.justify-content-between {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .btn-group .btn:not(:last-child) {
            border-left: 0;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .sidebar-toggle-btn {
                position: fixed;
            }
            [dir="rtl"] .main-content {
                margin-right: 0;
            }
            .particles {
                display: none;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="text-center text-white">
        <div class="loading-spinner mb-3"></div>
        <p>Loading...</p>
    </div>
</div>

<!-- Floating Particles -->
<div class="particles"></div>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-header">
        <button type="button" class="sidebar-toggle-btn sidebar-toggle-inside" id="sidebarToggleBtn" title="Hide Sidebar">
            <i class="fa fa-angle-left"></i>
        </button>
        <h4><i class="fa fa-tachometer"></i> Admin Dashboard</h4>
        <small>Management Panel</small>
    </div>

    <div class="p-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.home') }}" class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}">
                    <i class="fa fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fa fa-cube"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa fa-tags"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fa fa-shopping-cart"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.size-guides.index') }}" class="nav-link {{ request()->routeIs('admin.size-guides.*') ? 'active' : '' }}">
                    <i class="fa fa-table"></i> Size Guides
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.loyalty.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty.*') ? 'active' : '' }}">
                    <i class="fa fa-star"></i> Loyalty
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.messages.index') }}" class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                    <i class="fa fa-envelope"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.discounts.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.discounts.*') ? 'active' : '' }}">
                    <i class="fa fa-percent"></i> Discount Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.offers.quick.index') }}" class="nav-link {{ request()->routeIs('admin.offers.quick.*') ? 'active' : '' }}">
                    <i class="fa fa-bolt"></i> Quick Offers
                </a>
            </li>
        </ul>
    </div>

    <div class="position-absolute" style="bottom: 20px; left: 20px; right: 20px;">
        <div class="text-center">
            <small style="color: rgba(255,255,255,0.7);">Welcome {{ Auth::user()->name }}</small>
            <br>
            <a href="{{ route('shop.home') }}" class="btn btn-outline-light btn-sm mt-2 mr-1">
                <i class="fa fa-globe"></i> Website
            </a>
            <a href="{{ route('shop.profile.edit') }}" class="btn btn-outline-info btn-sm mt-2 mr-1">
                <i class="fa fa-user-edit"></i> Edit Profile
            </a>
            <div class="btn-group mt-2 mr-1">
                <button type="button" class="btn btn-outline-light btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-globe"></i> Language
                </button>
                
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<button type="button" class="sidebar-toggle-btn sidebar-reopen-btn" id="sidebarReopenBtn" title="Show Sidebar">
    <i class="fa fa-bars"></i>
</button>
<div class="main-content">
    @yield('content')
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

<script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>

<script>
    // Loading Overlay
    function showLoading() {
        $('.loading-overlay').addClass('show');
    }

    function hideLoading() {
        $('.loading-overlay').removeClass('show');
    }

    // Create Floating Particles
    function createParticles() {
        const particlesContainer = $('.particles');
        const particleCount = 15;

        for (let i = 0; i < particleCount; i++) {
            const particle = $('<div class="particle"></div>');
            particle.css({
                left: Math.random() * 100 + '%',
                animationDelay: Math.random() * 6 + 's',
                animationDuration: (Math.random() * 3 + 3) + 's'
            });
            particlesContainer.append(particle);
        }
    }

    // Number Counter Animation
    function animateNumbers() {
        $('.stat-number').each(function() {
            const $this = $(this);
            const originalText = $this.text();
            const hasDollar = originalText.includes('$');
            const hasPercent = originalText.includes('%');
            const countTo = parseInt(originalText.replace(/[^0-9]/g, ''));

            if (countTo > 0) {
                // Start from 1 for a snappier feel and to avoid flashing 0
                $({ countNum: 1 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        const num = Math.max(1, Math.floor(this.countNum));
                        if (hasDollar) {
                            $this.text('$' + num.toLocaleString());
                        } else if (hasPercent) {
                            $this.text(num + '%');
                        } else {
                            $this.text(num.toLocaleString());
                        }
                    },
                    complete: function() {
                        // Force the exact final value to avoid off-by-one due to flooring/easing
                        if (hasDollar) {
                            $this.text('$' + countTo.toLocaleString());
                        } else if (hasPercent) {
                            $this.text(countTo.toLocaleString() + '%');
                        } else {
                            $this.text(countTo.toLocaleString());
                        }
                    }
                });
            } else {
                // For zero values, show 0 immediately without animation and with proper suffix/prefix
                if (hasDollar) {
                    $this.text('$0');
                } else if (hasPercent) {
                    $this.text('0%');
                } else {
                    $this.text('0');
                }
            }
        });
    }

    // Smooth Page Transitions
    function initPageTransitions() {
        $('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"]):not(.no-transition)').on('click', function(e) {
            const href = $(this).attr('href');
            if (href && href !== '#' && !href.startsWith('javascript:')) {
                e.preventDefault();
                showLoading();
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            }
        });
    }

    // Enhanced Form Interactions
    function initFormEnhancements() {
        // Form input focus effects
        $('.form-control').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });

        // Form submission with loading
        $('form').on('submit', function() {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            showLoading();
        });
    }

    // Table Enhancements
    function initTableEnhancements() {
        // Row selection effect
        $('.table tbody tr').on('click', function() {
            $(this).addClass('selected').siblings().removeClass('selected');
        });

        // Sortable headers (visual effect only)
        $('.table thead th').on('click', function() {
            $(this).toggleClass('sorted-asc sorted-desc');
        });
    }

    // Sidebar Enhancements
    function initSidebarEnhancements() {
        // Active link highlighting
        const currentPath = window.location.pathname;
        $('.sidebar .nav-link').each(function() {
            const linkPath = $(this).attr('href');
            if (currentPath.includes(linkPath) && linkPath !== '/') {
                $(this).addClass('active').siblings().removeClass('active');
            }
        });

        // Sidebar collapse toggle
        $('#sidebarToggleBtn, #sidebarReopenBtn').on('click', function() {
            $('body').toggleClass('sidebar-collapsed');
        });
    }

    // Card Hover Effects
    function initCardEffects() {
        $('.card').hover(
            function() {
                $(this).addClass('elevated');
            },
            function() {
                $(this).removeClass('elevated');
            }
        );
    }

    // Initialize everything when document is ready
    $(document).ready(function() {
        // Hide loading on page load
        setTimeout(hideLoading, 500);

        // Create visual effects
        createParticles();

        // Initialize enhancements
        initPageTransitions();
        initFormEnhancements();
        initTableEnhancements();
        initSidebarEnhancements();
        initCardEffects();

        // Animate numbers after a short delay
        setTimeout(animateNumbers, 800);

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    });

    // Enhanced confirm delete with better styling
    function confirmDelete(message) {
        return new Promise((resolve) => {
            const modal = $(`
                <div class="modal fade" id="confirmModal" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Delete</h5>
                            </div>
                            <div class="modal-body">
                                <p>${message || 'Are you sure you want to delete this item?'}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            $('body').append(modal);
            modal.modal('show');

            modal.find('#confirmDelete').on('click', function() {
                modal.modal('hide');
                resolve(true);
            });

            modal.on('hidden.bs.modal', function() {
                modal.remove();
                resolve(false);
            });
        });
    }

    // Page visibility change effects
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            document.title = '💤 Dashboard - Away';
        } else {
            document.title = '🚀 Dashboard - CozaStore';
            // Refresh stats when user returns
            setTimeout(() => {
                animateNumbers();
            }, 500);
        }
    });
</script>

@yield('scripts')

</body>
</html>
