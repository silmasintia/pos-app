<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>

    <link rel="shortcut icon" href="{{ asset('hopeui/assets/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/hope-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/dark.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/rtl.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #395886;
            --primary-dark: #2C426A;
            --bg-solid: #eef2f6; 
            --text-primary: #232d42;
            --text-muted: #8a92a6;
        }

        body {
            background-color: var(--bg-solid); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            display: flex;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-height: 600px;
        }

        .login-left {
            flex: 1;
            background-color: var(--primary-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
            text-align: center;
            color: white;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 20%),
                              radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 20%);
            opacity: 0.6;
        }

        .login-img {
            width: 80%;
            max-width: 350px;
            position: relative;
            z-index: 2;
            transition: transform 0.3s ease;
        }

        .login-welcome-text {
            position: relative;
            z-index: 2;
            margin-top: 20px;
        }

        .login-welcome-text h3 {
            color: white;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-welcome-text p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }

        .login-right {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-header {
            margin-bottom: 30px;
            text-align: left;
        }

        .login-header h2 {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 28px;
        }

        .login-header p {
            color: var(--text-muted);
        }

        .form-control {
            border: 1.5px solid #e0e6ed;
            border-radius: 10px;
            padding: 1rem 0.75rem;
            font-size: 0.95rem;
            background-color: #fbfdff;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(64, 123, 255, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color); 
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(64, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(64, 123, 255, 0.4);
            background-color: var(--primary-dark); 
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        @media (max-width: 992px) {
            .login-card {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 500px;
                margin: 0 auto;
            }

            .login-left {
                padding: 30px;
                min-height: 200px;
            }
            
            .login-img {
                width: 150px;
            }
            
            .login-welcome-text {
                display: none;
            }

            .login-right {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="login-left">
                <img src="{{ asset('images/auth/login.svg') }}" alt="Logo" class="img-fluid login-img">
                <div class="login-welcome-text">
                    <h3>Secure Access</h3>
                    <p>Manage your dashboard efficiently and securely.</p>
                </div>
            </div>

            <div class="login-right">
                <div class="login-header">
                    <h2>Welcome Back!</h2>
                    <p>Please enter your details to sign in.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="name@example.com" required
                            autofocus>
                        <label for="email">Email Address</label>
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            placeholder="Password" required>
                        <label for="password">Password</label>
                        <span class="password-toggle" onclick="togglePasswordVisibility()">
                            <i id="toggle-icon" class="fas fa-eye-slash"></i>
                        </span>
                        @error('password')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color: var(--primary-color); text-decoration: none; font-weight: 500; font-size: 0.9rem;">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Sign In
                    </button>

                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('hopeui/assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hopeui/assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('hopeui/assets/js/hope-ui.js') }}" defer></script>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>