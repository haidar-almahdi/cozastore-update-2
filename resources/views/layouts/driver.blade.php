<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driver Panel') - CozaStore</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <style>
        body {
            background: radial-gradient(circle at top right, #e8efff 0%, #f4f7fb 45%, #eef2f8 100%);
            font-family: "Poppins", Arial, sans-serif;
            min-height: 100vh;
        }
        .driver-nav {
            background: linear-gradient(165deg, #223248 0%, #2f3f57 55%, #1d2a3c 100%);
            color: #fff;
            min-height: 100vh;
            box-shadow: 8px 0 24px rgba(25, 40, 58, .28);
        }
        .driver-nav .brand {
            padding: 12px;
            border-radius: 12px;
            background: rgba(255,255,255,.08);
            margin-bottom: 16px;
        }
        .driver-nav a {
            color: #dfe7f3;
            display: block;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 8px;
            text-decoration: none;
            transition: all .25s ease;
        }
        .driver-nav a.active, .driver-nav a:hover {
            background: rgba(116, 148, 236, 0.28);
            color: #fff;
            transform: translateX(4px);
        }
        .main-wrap { padding: 26px; animation: fadeIn .45s ease; }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 28px rgba(34, 50, 72, .1);
            overflow: hidden;
        }
        .card:hover { transform: translateY(-2px); transition: transform .2s ease; }
        .soft-badge {
            border-radius: 999px;
            padding: 4px 10px;
            font-size: .75rem;
            font-weight: 600;
            background: #e8f1ff;
            color: #2f4f9f;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 driver-nav p-3">
            <div class="brand">
                <h5 class="mb-1"><i class="fa fa-motorcycle"></i> Driver Panel</h5>
                <small style="color:#cbd7ea;">Cozastore Delivery</small>
            </div>
            <a href="{{ route('driver.orders.index') }}" class="{{ request()->routeIs('driver.orders.*') ? 'active' : '' }}">
                <i class="fa fa-list"></i> Delivery Orders
            </a>
            <a href="{{ route('shop.home') }}"><i class="fa fa-globe"></i> Website</a>
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button class="btn btn-outline-light btn-sm w-100" type="submit"><i class="fa fa-sign-out"></i> Logout</button>
            </form>
        </aside>
        <main class="col-md-9 col-lg-10 main-wrap">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @if(session('info')) <div class="alert alert-info">{{ session('info') }}</div> @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
@yield('scripts')
</body>
</html>
