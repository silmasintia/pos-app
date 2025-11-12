<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('hopeui/assets/images/favicon.ico') }}" />

    <!-- Library / Plugin Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/core/libs.min.css') }}" />

    <!-- Hope UI Design System -->
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/hope-ui.min.css') }}" />

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/custom.min.css') }}" />

    <!-- Dark Mode -->
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/dark.min.css') }}" />

    <!-- RTL -->
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/rtl.min.css') }}" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --light-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #2d3748;
            --text-muted: #718096;
            --border-color: #e2e8f0;
        }

        body {
            background: var(--light-bg);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(245, 87, 108, 0.07) 0%, transparent 20%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .register-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-card {
            border: none;
            border-radius: 16px;
            background: var(--card-bg);
            box-shadow: 
                0 10px 25px rgba(0, 0, 0, 0.08),
                0 4px 10px rgba(0, 0, 0, 0.04);
            padding: 40px 30px;
            overflow: hidden;
            position: relative;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .logo i {
            color: white;
            font-size: 24px;
        }

        .register-card h2 {
            font-weight: 700;
            font-size: 28px;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 10px;
        }

        .register-card p {
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 1rem 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .form-floating label {
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 1rem 0.75rem;
        }

        .form-control:focus ~ label {
            color: var(--primary-color);
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border-color: var(--border-light);
            margin-top: 0.15rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #495057;
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            margin-top: 0.25rem;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: var(--text-primary);
            font-size: 15px;
            margin-left: 8px;
        }

        .text-center a {
            color: #667eea;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .text-center a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .divider span {
            padding: 0 15px;
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 500;
        }

        .social-btn:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .social-btn i {
            margin-right: 8px;
            font-size: 16px;
        }

        .google-btn i { color: #ea4335; }
        .facebook-btn i { color: #3b5998; }
        .twitter-btn i { color: #1da1f2; }

        @media (max-width: 576px) {
            .register-card {
                padding: 30px 20px;
                border-radius: 12px;
            }
            
            .register-card h2 {
                font-size: 24px;
            }
            
            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="register-wrapper">
        <div class="card register-card">
            <div class="card-body">
                <div class="logo-container">
                    <div class="logo">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h2>Create Account</h2>
                    <p>Sign up to get started</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row">
                        <!-- Name -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name" id="name" value="{{ old('name') }}"
                                    placeholder="Your Name" required autofocus>
                                <label for="name">Name</label>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    name="username" id="username" value="{{ old('username') }}"
                                    placeholder="Username" required>
                                <label for="username">Username</label>
                                @error('username')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email" id="email" value="{{ old('email') }}"
                                    placeholder="Email Address" required>
                                <label for="email">Email</label>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                    placeholder="Phone Number" required>
                                <label for="phone_number">Phone Number</label>
                                @error('phone_number')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" id="password" placeholder="Password"
                                    required>
                                <label for="password">Password</label>
                                @error('password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control"
                                    name="password_confirmation" id="password_confirmation"
                                    placeholder="Confirm Password" required>
                                <label for="password_confirmation">Confirm Password</label>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="col-lg-12 mt-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree with the terms of use
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">
                        Sign Up
                    </button>

                    {{-- <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="social-login">
                        <a href="#" class="social-btn google-btn">
                            <i class="fab fa-google"></i> Google
                        </a>
                        <a href="#" class="social-btn facebook-btn">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="#" class="social-btn twitter-btn">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                    </div> --}}

                    <div class="text-center mt-4">
                        Already have an account?
                        <a href="{{ route('login') }}">Sign in here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('hopeui/assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hopeui/assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('hopeui/assets/js/hope-ui.js') }}" defer></script>
</body>

</html>