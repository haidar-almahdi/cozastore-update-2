<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup Form</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
            list-style: none;
        }

        body{
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #4a5f7a 0%, #5a6c7d 50%, #4a5f7a 100%);
            position: relative;
            overflow: hidden;
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

        .container{
            position: relative;
            width: 850px;
            height: 550px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            margin: 20px;
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        .container h1{
            font-size: 36px;
            margin: -10px 0;
        }

        .container p{
            font-size: 14.5px;
            margin: 15px 0;
        }

        form{ width: 100%; height: 100%; }

        .form-box{
            position: absolute;
            right: 0;
            width: 50%;
            height: 100%;
            background: #fff;
            display: flex;
            align-items: center;
            color: #333;
            text-align: center;
            padding: 40px;
            z-index: 1;
            transition: .6s ease-in-out 1.2s, visibility 0s 1s;
        }

        .container.active .form-box{ right: 50%; bottom: 3%; }

        .form-box.register{ visibility: hidden; }
        .container.active .form-box.register{ visibility: visible; overflow-x: auto; }

        .input-box{
            position: relative;
            margin: 30px 0;
        }

        .input-box input{
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: rgba(238, 238, 238, 0.8);
            border-radius: 12px;
            border: 2px solid transparent;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .input-box input:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #7494ec;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(116, 148, 236, 0.3);
        }

        .input-box input::placeholder{
            color: #888;
            font-weight: 400;
        }

        .input-box i{
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .forgot-link{ margin: -15px 0 15px; }
        .forgot-link a{
            font-size: 14.5px;
            color: #333;
        }

        .btn{
            width: 100%;
            height: 48px;
            background: linear-gradient(45deg, #7494ec, #9bb5ff);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(116, 148, 236, 0.4);
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(116, 148, 236, 0.6);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .social-icons{
            display: flex;
            justify-content: center;
        }

        .social-icons a{
            display: inline-flex;
            padding: 12px;
            border: 2px solid rgba(204, 204, 204, 0.5);
            border-radius: 12px;
            font-size: 24px;
            color: #333;
            margin: 0 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .social-icons a:hover {
            transform: translateY(-3px) scale(1.1);
            border-color: #7494ec;
            color: #7494ec;
            box-shadow: 0 8px 25px rgba(116, 148, 236, 0.3);
        }

        .toggle-box{
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .toggle-box::before{
            content: '';
            position: absolute;
            left: -250%;
            width: 300%;
            height: 100%;
            background: linear-gradient(135deg, #7494ec 0%, #9bb5ff 50%, #7494ec 100%);
            border-radius: 150px;
            z-index: 2;
            transition: 1.8s ease-in-out;
            box-shadow: 0 0 50px rgba(116, 148, 236, 0.5);
        }

        .container.active .toggle-box::before{ left: 50%; }

        .toggle-panel{
            position: absolute;
            width: 50%;
            height: 100%;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 2;
            transition: .6s ease-in-out;
        }

        .toggle-panel.toggle-left{
            left: 0;
            transition-delay: 1.2s;
        }
        .container.active .toggle-panel.toggle-left{
            left: -50%;
            transition-delay: .6s;
        }

        .toggle-panel.toggle-right{
            right: -50%;
            transition-delay: .6s;
        }
        .container.active .toggle-panel.toggle-right{
            right: 0;
            transition-delay: 1.2s;
        }

        .toggle-panel p{ margin-bottom: 20px; }

        .toggle-panel .btn{
            width: 160px;
            height: 46px;
            background: transparent;
            border: 2px solid #fff;
            box-shadow: none;
        }

        @media screen and (max-width: 650px){
            .container{ height: calc(100vh - 40px); }

            .form-box{
                bottom: 0;
                width: 100%;
                height: 70%;
            }

            .container.active .form-box{
                right: 0;
                bottom: 30%;
            }

            .toggle-box::before{
                left: 0;
                top: -270%;
                width: 100%;
                height: 300%;
                border-radius: 20vw;
            }

            .container.active .toggle-box::before{
                left: 0;
                top: 70%;
            }

            .container.active .toggle-panel.toggle-left{
                left: 0;
                top: -30%;
            }

            .toggle-panel{
                width: 100%;
                height: 30%;
            }
            .toggle-panel.toggle-left{ top: 0; }
            .toggle-panel.toggle-right{
                right: 0;
                bottom: -30%;
            }

            .container.active .toggle-panel.toggle-right{ bottom: 0; }
        }

        @media screen and (max-width: 400px){
            .form-box { padding: 20px; }

            .toggle-panel h1{font-size: 30px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-box login">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h1>Login</h1>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>
                <div class="forgot-link">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <p>or login with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-google' ></i></a>
                    <a href="#"><i class='bx bxl-facebook' ></i></a>
                    <a href="#"><i class='bx bxl-github' ></i></a>
                    <a href="#"><i class='bx bxl-linkedin' ></i></a>
                </div>
            </form>
        </div>

        <div class="form-box register">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="name" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope' ></i>
                </div>
                <div class="input-box">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                    <i class='bx bxs-phone'></i>
                </div>
                <div class="input-box">
                    <textarea name="address" placeholder="Address" required style="width: 100%; padding: 13px 50px 13px 20px; background: #eee; border-radius: 8px; border: none; outline: none; font-size: 16px; color: #333; font-weight: 500; resize: none; height: 100px;"></textarea>
                    <i class='bx bxs-map' style="top: 20px;"></i>
                </div>
                <div class="input-box" style="text-align: left; padding: 0 20px;">
                    <label style="display: block; margin-bottom: 10px; color: #333;">Gender:</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="gender" value="male" required>
                            <span>Male</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="gender" value="female" required>
                            <span>Female</span>
                        </label>
                    </div>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>
                <button type="submit" class="btn">Register</button>
                <p>or register with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-google' ></i></a>
                    <a href="#"><i class='bx bxl-facebook' ></i></a>
                    <a href="#"><i class='bx bxl-github' ></i></a>
                    <a href="#"><i class='bx bxl-linkedin' ></i></a>
                </div>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.querySelector('.container');
        const registerBtn = document.querySelector('.register-btn');
        const loginBtn = document.querySelector('.login-btn');
        const inputs = document.querySelectorAll('.input-box input');
        const buttons = document.querySelectorAll('.btn');

        // Toggle between login and register
        registerBtn.addEventListener('click', () => {
            container.classList.add('active');
            addRippleEffect(registerBtn, event);
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove('active');
            addRippleEffect(loginBtn, event);
        });

        // Enhanced input animations
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });

            // Typing animation
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.style.background = 'rgba(116, 148, 236, 0.1)';
                } else {
                    this.style.background = 'rgba(238, 238, 238, 0.8)';
                }
            });
        });

        // Ripple effect for buttons
        function addRippleEffect(button, event) {
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Add ripple effect to all buttons
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                addRippleEffect(this, e);
            });
        });

        // Form validation with animations
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn');
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
                submitBtn.disabled = true;

                // Add loading animation
                setTimeout(() => {
                    if (!this.checkValidity()) {
                        submitBtn.innerHTML = 'Submit';
                        submitBtn.disabled = false;
                    }
                }, 100);
            });
        });

        // Floating animation for social icons
        const socialIcons = document.querySelectorAll('.social-icons a');
        socialIcons.forEach((icon, index) => {
            icon.style.animationDelay = (index * 0.1) + 's';
            icon.style.animation = 'float 3s ease-in-out infinite';
        });

        // Page load animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Error handling with animations
        function showError(input, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            errorDiv.style.cssText = `
                color: #e74c3c;
                font-size: 12px;
                margin-top: 5px;
                animation: slideInDown 0.3s ease;
            `;
            input.parentElement.appendChild(errorDiv);
            input.style.borderColor = '#e74c3c';

            setTimeout(() => {
                errorDiv.remove();
                input.style.borderColor = 'transparent';
            }, 3000);
        }

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
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
