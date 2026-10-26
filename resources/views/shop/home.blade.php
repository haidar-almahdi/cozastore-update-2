@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
            list-style: none;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Floating particles animation */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        .container {
            /* background: #fff; */
            border-radius: 30px;
            /* box-shadow: 0 0 30px rgba(0, 0, 0, .2); */
            padding: 20px;
        }

        .categories-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px 20px;
            margin: 50px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 25px;
            padding: 12px 30px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.6);
            color: #fff;
        }

        .hero-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(238, 90, 36, 0.6);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title {
            animation: pulse 3s ease-in-out infinite;
        }

        .card {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .card:hover::before {
            left: 100%;
        }

        .card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        .category-image-container {
            height: 250px;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            position: relative;
        }

        .category-card:hover .card-img-top {
            transform: scale(1.1);
        }

        .category-card:hover .category-overlay {
            opacity: 1 !important;
        }

        .category-card:hover .card {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .category-btn {
            position: relative;
            overflow: hidden;
        }

        .category-btn::after {
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

        .category-btn:hover::after {
            width: 300px;
            height: 300px;
        }

        .card-img-top {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            border-radius: 8px;
        }

        .card-img-top:hover {
            transform: scale(1.05);
        }

        /* Product Cards Styles */
        .product-card {
            position: relative;
            overflow: hidden;
        }

        .product-image-container {
            position: relative;
            height: 250px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .product-card .card-img-top {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            border-radius: 8px;
        }

        /* Favorite Button Styles */
        .favorite-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 2;
        }

        .favorite-btn i {
            color: #ccc;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .favorite-btn:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .favorite-btn:hover i {
            color: #dc3545;
        }

        .favorite-btn.active {
            background: white;
        }

        .favorite-btn.active i {
            color: #dc3545;
            animation: heartBeat 0.6s ease-in-out;
        }

        @keyframes heartBeat {
            0% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Product Card Hover Effects */
        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .product-card:hover .favorite-btn {
            transform: scale(1.1);
        }
        .card-title {
            margin-bottom: 0px !important;
        }
        .card-text {
            margin-bottom: 0.75rem !important;
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
        }

        .hero-section {
            position: relative;
        }

        .hero-slide {
            transition: opacity 0.45s ease;
        }

        .hero-panel {
            animation: fadeInUp 1s ease-out;
            background: rgba(10, 10, 20, 0.46);
            padding: 44px 48px;
            border-radius: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            max-width: 720px;
            margin: 0 auto;
        }

        .hero-subtitle {
            font-size: 1.08rem;
            font-weight: 400;
            opacity: 0.95;
            display: block;
            margin-bottom: 16px;
            letter-spacing: 0.6px;
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 4.2rem);
            line-height: 1.1;
            font-weight: 700;
            margin-bottom: 26px;
            text-shadow: 2px 2px 14px rgba(0, 0, 0, 0.55);
        }

        .hero-btn {
            display: inline-block;
            padding: 14px 34px;
            color: #fff;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: all 0.3s ease;
        }

        .hero-btn-primary {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            box-shadow: 0 10px 30px rgba(238, 90, 36, 0.42);
        }

        .hero-btn-secondary {
            background: linear-gradient(45deg, #3742fa, #2f3542);
            box-shadow: 0 10px 30px rgba(55, 66, 250, 0.42);
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 14px;
        }

        .section-subtitle {
            font-size: 1.08rem;
            color: #666;
            max-width: 640px;
            margin: 0 auto;
            line-height: 1.7;
        }

        @media (max-width: 768px) {
            .hero-panel {
                padding: 28px 22px;
                border-radius: 18px;
                margin: 0 14px;
            }
        }
    </style>
@endsection

@section('content')


    <!-- Floating Particles Background -->
    <div class="particles" id="particles"></div>

    <!-- Hero Slider Section -->
    <section class="hero-section">
        <div class="hero-slider" style="height: 100vh; position: relative; overflow: hidden;">
            <div id="customSlider" style="height: 100%;">
                <div class="hero-slide active" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.35), rgba(118, 75, 162, 0.35)), url('{{ asset('images/slide-01.jpg') }}'); background-size: cover; background-position: center; height: 100%; display: flex; align-items: center; justify-content: center;">
                    <div class="hero-content text-center text-white">
                        <div class="hero-text hero-panel">
                            <span class="hero-subtitle">Women Collection 2025</span>
                            <h1 class="hero-title">New Season</h1>
                            <a href="{{ route('shop.products.index') }}" class="hero-btn hero-btn-primary">
                                <i class="fa fa-shopping-bag" style="margin-right: 10px;"></i>Shop Now
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hero-slide" style="background: linear-gradient(135deg, rgba(118, 75, 162, 0.35), rgba(102, 126, 234, 0.35)), url('{{ asset('images/slide-02.jpg') }}'); background-size: cover; background-position: center; height: 100%; display: none; align-items: center; justify-content: center;">
                    <div class="hero-content text-center text-white">
                        <div class="hero-text hero-panel">
                            <span class="hero-subtitle">Men Collection 2025</span>
                            <h1 class="hero-title">New Arrivals</h1>
                            <a href="{{ route('shop.products.index') }}" class="hero-btn hero-btn-secondary">
                                <i class="fa fa-star" style="margin-right: 10px;"></i>Discover
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slider Navigation -->
            <div class="slider-nav" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px;">
                <button class="nav-dot active" onclick="goToSlide(0)" style="width: 15px; height: 15px; border-radius: 50%; border: 2px solid white; background: white; cursor: pointer; transition: all 0.3s ease;"></button>
                <button class="nav-dot" onclick="goToSlide(1)" style="width: 15px; height: 15px; border-radius: 50%; border: 2px solid white; background: transparent; cursor: pointer; transition: all 0.3s ease;"></button>
            </div>
        </div>

        <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.nav-dot');

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.style.display = i === index ? 'flex' : 'none';
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                    dot.style.background = i === index ? 'white' : 'transparent';
                });
            }

            function goToSlide(index) {
                currentSlide = index;
                showSlide(currentSlide);
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            // Auto-play slider
            setInterval(nextSlide, 6000);

            // Create floating particles
            function createParticles() {
                const particlesContainer = document.getElementById('particles');
                for (let i = 0; i < 50; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.width = Math.random() * 4 + 2 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.animationDelay = Math.random() * 6 + 's';
                    particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                    particlesContainer.appendChild(particle);
                }
            }

            // Initialize particles when page loads
            document.addEventListener('DOMContentLoaded', createParticles);
        </script>
    </section>
    <!-- Categories Section -->
    <section class="categories-container">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Our Collections</h2>
                <p class="section-subtitle">Discover our carefully curated collections designed to elevate your style</p>
            </div>

            <div class="row">
                @foreach($categories as $index => $category)
                    <div class="col-md-4 mb-4">
                        <div class="category-card" style="animation: fadeInUp 0.8s ease-out {{ $index * 0.2 }}s both;">
                            <div class="card h-100 shadow-sm">
                                <div class="category-image-container" style="position: relative; overflow: hidden;">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" class="card-img-top" alt="{{ $category->name }}" style="transition: transform 0.5s ease;">
                                    @else
                                        <img src="{{ asset('images/default-category.jpg') }}" class="card-img-top" alt="{{ $category->name }}" style="transition: transform 0.5s ease;">
                                    @endif
                                    <div class="category-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8)); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-eye" style="color: white; font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column" style="padding: 25px;">
                                    <h5 class="card-title" style="font-size: 1.4rem; font-weight: 600; color: #333; margin-bottom: 15px;">{{ $category->name }}</h5>
                                    <p class="card-text" style="color: #666; line-height: 1.6; flex-grow: 1;">{{ $category->description }}</p>
                                    <a href="{{ route('shop.products.index', ['category' => $category->slug]) }}" class="btn btn-primary mt-auto category-btn" style="border-radius: 25px; padding: 12px 25px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                                        Explore Collection
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



@endsection

@section('scripts')
    <script>
        // Favorite functionality
        function toggleFavorite(button) {
            const productId = button.dataset.productId;
            const icon = button.querySelector('i');

            console.log('Toggling favorite for product ID:', productId);

            // Check if user is logged in
            @guest
                showNotification('Please login first to add favorites.', 'error');
                setTimeout(() => window.location.href = '/login', 1500);
                return;
            @endguest

            // Show loading state
            button.style.pointerEvents = 'none';
            icon.className = 'fa fa-spinner fa-spin';

            const url = '/shop/favorites/' + productId + '/toggle';
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            console.log('Making request to:', url);
            console.log('CSRF Token:', token);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    return response.text().then(text => {
                        console.log('Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                // Reset loading state
                button.style.pointerEvents = 'auto';
                icon.className = 'fa fa-heart';

                if (data.status === 'added') {
                    button.classList.add('active');
                    showNotification('Product added to favorites!', 'success');
                } else {
                    button.classList.remove('active');
                    showNotification('Product removed from favorites!', 'info');
                }

                // Update favorite count in navbar if exists
                updateFavoriteCount(data.favorites_count);
            })
            .catch(error => {
                console.error('Error:', error);
                // Reset loading state
                button.style.pointerEvents = 'auto';
                icon.className = 'fa fa-heart';

                // Show detailed error
                if (error.message.includes('401')) {
                    showNotification('Please login first to add favorites.', 'error');
                    window.location.href = '/login';
                } else if (error.message.includes('419')) {
                    showNotification('Session expired. Please refresh the page.', 'error');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showNotification('Something went wrong: ' + error.message, 'error');
                }
            });
        }

        function updateFavoriteCount(count) {
            const counters = document.querySelectorAll('.favorite-count');
            counters.forEach(counter => {
                counter.textContent = count;
            });
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} notification-toast`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                animation: slideIn 0.3s ease-out;
            `;
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                    ${message}
                </div>
            `;

            // Add to page
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Add CSS for notifications
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            .notification-toast {
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: none;
                border-radius: 8px;
            }
        `;
        document.head.appendChild(style);

        // Update favorite count on page load
        document.addEventListener('DOMContentLoaded', function() {
            @auth
            fetch('/shop/favorites/count')
                .then(response => response.json())
                .then(data => {
                    updateFavoriteCount(data.count);
                })
                .catch(error => console.error('Error fetching favorite count:', error));
            @endauth
        });
    </script>
@endsection
