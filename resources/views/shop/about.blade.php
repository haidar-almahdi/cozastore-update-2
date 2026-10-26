@extends('layouts.app')

@section('title', 'About Us')

@section('styles')
<style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            background-attachment: fixed;
        }

        .about-hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), url('{{ asset('images/banner-06.jpg') }}') center/cover no-repeat;
            min-height: 500px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        .about-hero h1 {
            font-size: 4rem;
            font-weight: 800;
            animation: fadeInUp 1s ease-out;
            position: relative;
            z-index: 2;
        }

        .about-section {
            padding: 80px 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            margin: 50px 0;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .about-img {
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            transition: all 0.4s ease;
        }

        .about-img:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
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

        /* Scroll animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endsection

@section('content')


    <!-- Hero Section -->
    <div class="about-hero">
        <h1>About CozaStore</h1>
    </div>
    <!-- About Details Section -->
    <section class="about-section container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6 mb-4 mb-md-0" style="animation: slideInLeft 0.8s ease-out;">
                <img src="{{ asset('images/about-01.jpg') }}" alt="Our Store" class="img-fluid about-img">
            </div>
            <div class="col-md-6" style="animation: slideInRight 0.8s ease-out;">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: #333; margin-bottom: 25px; background: linear-gradient(45deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Who We Are</h2>
                <p class="lead" style="font-size: 1.2rem; line-height: 1.8; color: #555; margin-bottom: 30px;">CozaStore is a modern e-commerce platform dedicated to providing the latest fashion trends for men and women. Our mission is to deliver high-quality products, exceptional customer service, and a seamless shopping experience.</p>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 10px 0; color: #666; font-size: 1.1rem; position: relative; padding-left: 30px;">
                        <i class="fa fa-check-circle" style="color: #667eea; position: absolute; left: 0; top: 12px; font-size: 1.2rem;"></i>
                        Wide range of products and categories
                    </li>
                    <li style="padding: 10px 0; color: #666; font-size: 1.1rem; position: relative; padding-left: 30px;">
                        <i class="fa fa-check-circle" style="color: #667eea; position: absolute; left: 0; top: 12px; font-size: 1.2rem;"></i>
                        Secure and easy checkout process
                    </li>
                    <li style="padding: 10px 0; color: #666; font-size: 1.1rem; position: relative; padding-left: 30px;">
                        <i class="fa fa-check-circle" style="color: #667eea; position: absolute; left: 0; top: 12px; font-size: 1.2rem;"></i>
                        Fast shipping and reliable support
                    </li>
                </ul>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-6 order-md-2 mb-4 mb-md-0" style="animation: slideInRight 0.8s ease-out 0.2s both;">
                <img src="{{ asset('images/about-02.jpg') }}" alt="Our Team" class="img-fluid about-img">
            </div>
            <div class="col-md-6 order-md-1" style="animation: slideInLeft 0.8s ease-out 0.2s both;">
                <h2 style="font-size: 2.5rem; font-weight: 700; color: #333; margin-bottom: 25px; background: linear-gradient(45deg, #764ba2, #667eea); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Our Vision</h2>
                <p style="font-size: 1.2rem; line-height: 1.8; color: #555;">We believe in making fashion accessible to everyone. Our team works tirelessly to curate collections that inspire confidence and style. At CozaStore, we value innovation, quality, and customer satisfaction above all else.</p>
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-3" style="padding: 15px; background: rgba(102, 126, 234, 0.1); border-radius: 15px; border-left: 4px solid #667eea;">
                        <i class="fa fa-lightbulb-o" style="color: #667eea; font-size: 1.5rem; margin-right: 15px;"></i>
                        <span style="color: #333; font-weight: 600;">Innovation at our core</span>
                    </div>
                    <div class="d-flex align-items-center mb-3" style="padding: 15px; background: rgba(118, 75, 162, 0.1); border-radius: 15px; border-left: 4px solid #764ba2;">
                        <i class="fa fa-heart" style="color: #764ba2; font-size: 1.5rem; margin-right: 15px;"></i>
                        <span style="color: #333; font-weight: 600;">Customer satisfaction first</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Scroll animations
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;

                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animated');
                }
            });
        }

        window.addEventListener('scroll', animateOnScroll);
        document.addEventListener('DOMContentLoaded', animateOnScroll);

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.about-hero');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });

        // Smooth hover effects
        document.querySelectorAll('.about-img').forEach(img => {
            img.addEventListener('mouseenter', function() {
                this.style.filter = 'brightness(1.1) contrast(1.1)';
            });

            img.addEventListener('mouseleave', function() {
                this.style.filter = 'brightness(1) contrast(1)';
            });
        });
    </script>
@endsection
