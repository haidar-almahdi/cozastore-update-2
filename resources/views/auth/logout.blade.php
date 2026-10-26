<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
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

        .user-info-container {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

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
        }

        .input-box{
            position: relative;
            margin: 30px 0;
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
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            text-decoration: none;
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

        .btn-secondary{
            background: linear-gradient(45deg, #e74c3c, #ff6b6b);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        }

        .btn-secondary:hover {
            box-shadow: 0 12px 35px rgba(231, 76, 60, 0.6);
        }

        .toggle-box{
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .toggle-box::before{
            content: '';
            position: absolute;
            left: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, #7494ec 0%, #9bb5ff 50%, #7494ec 100%);
            border-radius: 30px 0 0 30px;
            z-index: 2;
            box-shadow: 0 0 50px rgba(116, 148, 236, 0.5);
        }

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
        }

        .toggle-panel.toggle-left{
            left: 0;
        }

        .toggle-panel p{
            margin-bottom: 20px;
            font-size: 18px;
        }

        .user-info{
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-direction: column;
        }

        .user-info p{
            margin: 10px 0;
            font-size: 16px;
        }

        @media screen and (max-width: 650px){
            .container{
                height: auto;
                min-height: 100vh;
                border-radius: 0;
            }

            .form-box{
                position: relative;
                width: 100%;
                height: auto;
                padding: 20px;
            }

            .toggle-box::before{
                display: none;
            }

            .toggle-panel{
                position: relative;
                width: 100%;
                height: auto;
                color: #333;
                padding: 20px;
            }

            .user-info{
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-box">
            <div class="user-info-container">
                <h1>Logout</h1>
                <p>Are you sure you want to logout?</p>

                <div class="user-info">
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    @if(Auth::user()->phone)
                        <p><strong>Phone:</strong> {{ Auth::user()->phone }}</p>
                    @endif
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Yes, Logout</button>
                </form>
                <a href="{{ route('shop.home') }}" class="btn">No, Stay Logged In</a>
            </div>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Goodbye!</h1>
                <p>We hope to see you again soon.</p>
                <p>Thank you for using our service.</p>
            </div>
        </div>
    </div>
    <script>
        // Enhanced button interactions
        const buttons = document.querySelectorAll('.btn');

        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Ripple effect
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
        });

        // Form submission with loading
        const logoutForm = document.querySelector('form');
        const logoutBtn = document.querySelector('.btn-secondary');

        logoutForm.addEventListener('submit', function(e) {
            logoutBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Logging out...';
            logoutBtn.disabled = true;
        });

        // Page load animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // User info animation
        const userInfo = document.querySelector('.user-info');
        userInfo.style.animation = 'fadeInUp 0.8s ease 0.3s both';

        // Add CSS for animations
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
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
