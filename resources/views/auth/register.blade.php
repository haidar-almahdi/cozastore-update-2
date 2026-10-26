<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CozaStore</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4a5f7a 0%, #5a6c7d 50%, #4a5f7a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Floating Particles */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 2px, transparent 2px),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 2px, transparent 2px),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 200px 200px, 300px 300px, 150px 150px;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
            animation: slideInUp 0.8s ease;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .register-header p {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 15px 50px 15px 20px;
            background: rgba(238, 238, 238, 0.8);
            border: 2px solid transparent;
            border-radius: 12px;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #7494ec;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(116, 148, 236, 0.3);
        }

        .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #666;
            transition: color 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: #7494ec;
        }

        .gender-options {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .gender-option {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: rgba(238, 238, 238, 0.5);
        }

        .gender-option:hover {
            background: rgba(116, 148, 236, 0.1);
        }

        .gender-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #7494ec;
        }

        .btn-register {
            width: 100%;
            height: 50px;
            background: linear-gradient(45deg, #7494ec, #9bb5ff);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(116, 148, 236, 0.4);
            margin-top: 20px;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(116, 148, 236, 0.6);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .login-link a {
            color: #7494ec;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #5a7bd8;
            text-decoration: underline;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.8rem;
            margin-top: 5px;
            animation: slideInDown 0.3s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 600px) {
            .register-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .register-header h1 {
                font-size: 2rem;
            }

            .gender-options {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Create Account</h1>
            <p>Join CozaStore and start shopping!</p>
        </div>

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-wrapper">
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required autocomplete="name" placeholder="Enter your full name">
                    <i class='bx bxs-user input-icon'></i>
                </div>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">
                    <i class='bx bxs-envelope input-icon'></i>
                </div>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper">
                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}" required placeholder="Enter your phone number">
                    <i class='bx bxs-phone input-icon'></i>
                </div>
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <div class="input-wrapper">
                    <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address') }}" required placeholder="Enter your address">
                    <i class='bx bxs-map input-icon'></i>
                </div>
                @error('address')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Gender</label>
                <div class="gender-options">
                    <label class="gender-option">
                        <input type="radio" name="gender" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                        <span>Male</span>
                    </label>
                    <label class="gender-option">
                        <input type="radio" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }} required>
                        <span>Female</span>
                    </label>
                </div>
                @error('gender')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           required autocomplete="new-password" placeholder="Create a password">
                    <i class='bx bxs-lock-alt input-icon'></i>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                           required autocomplete="new-password" placeholder="Confirm your password">
                    <i class='bx bxs-lock-alt input-icon'></i>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <span class="btn-text">Create Account</span>
            </button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
        </div>
    </div>

    <script>
        // Enhanced form interactions
        const form = document.getElementById('registerForm');
        const inputs = document.querySelectorAll('.form-control');
        const submitBtn = document.querySelector('.btn-register');
        const btnText = document.querySelector('.btn-text');

        // Input animations
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.parentElement.style.transition = 'transform 0.3s ease';
            });

            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'scale(1)';
            });

            // Real-time validation feedback
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.style.background = 'rgba(116, 148, 236, 0.1)';
                    this.style.borderColor = '#7494ec';
                } else {
                    this.style.background = 'rgba(238, 238, 238, 0.8)';
                    this.style.borderColor = 'transparent';
                }
            });
        });

        // Form submission with loading
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            btnText.innerHTML = '<span class="loading"></span> Creating Account...';
            submitBtn.style.background = 'linear-gradient(45deg, #95a5f0, #a8b8ff)';
        });

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        passwordInput.addEventListener('input', function() {
            const strength = getPasswordStrength(this.value);
            updatePasswordStrength(strength);
        });

        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.style.borderColor = '#e74c3c';
            } else {
                this.style.borderColor = '#27ae60';
            }
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        function updatePasswordStrength(strength) {
            const colors = ['#e74c3c', '#e67e22', '#f39c12', '#27ae60', '#2ecc71'];
            const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];

            if (strength > 0) {
                passwordInput.style.borderColor = colors[strength - 1];
            }
        }

        // Page load animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Ripple effect for button
        submitBtn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // Add CSS for ripple effect
        const style = document.createElement('style');
        style.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
