<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password</title>

    <link rel="shortcut icon" href="{{ asset('hopeui/assets/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/hope-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/dark.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('hopeui/assets/css/rtl.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    :root {
        --primary-color: #2367E6;
        --primary-dark: #2C426A;
        --primary-solid: #2C426A; 
        --bg-solid: #f3f7fc; 

        --text-primary: #232d42;
        --text-muted: #8a92a6;
    }

    body {
        background: var(--bg-solid); 
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

    /* Card layout */
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
        background: var(--primary-solid); 
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        color: white;
        position: relative;
        text-align: center;
    }

    .login-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: none; 
        opacity: 0.2; 
    }

    .login-img {
        width: 80%;
        max-width: 350px;
        position: relative;
        z-index: 2;
    }

    .login-welcome-text {
        margin-top: 20px;
        position: relative;
        z-index: 2;
    }

    .login-welcome-text h3 {
        font-weight: 700;
        color: white;
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

    .login-header h2 {
        color: var(--text-primary);
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .login-header p {
        color: var(--text-muted);
        margin-bottom: 30px;
    }

    .form-control {
        border: 1.5px solid #e0e6ed;
        border-radius: 10px;
        padding: 1rem 0.75rem;
        background-color: #fbfdff;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(35, 103, 230, 0.15);
    }

    .btn-primary {
        background: var(--primary-solid); 
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(35, 103, 230, 0.35);
        transition: 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(35, 103, 230, 0.45);
        background: var(--primary-dark);
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        cursor: pointer;
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    @media (max-width: 768px) {
        .login-card {
            flex-direction: column;
            max-width: 500px;
            margin: auto;
        }

        .login-left {
            padding: 30px;
            min-height: 200px;
        }

        .login-welcome-text {
            display: none;
        }

        .login-img {
            width: 150px;
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

            <div class="login-right">
                
                <div class="login-header">
                    <h2>Forgot Password?</h2>
                    <p>No problem. Just let us know your email address and we will email you a password reset link.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success mb-4" role="alert" style="border-radius: 10px; font-size: 0.9rem;">
                        <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="form-floating mb-4">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="name@example.com" required
                            autofocus>
                        <label for="email">Email Address</label>
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Email Password Reset Link
                    </button>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center;">
                            <i class="fas fa-arrow-left me-2"></i> Back to Login
                        </a>
                    </div>

                </form>
            </div>

            <div class="login-left">
                <img src="{{ asset('images/auth/forgot-password.svg') }}" alt="Logo" class="img-fluid login-img">
                <div class="login-welcome-text">
                    <h3>Reset Password</h3>
                    <p>Don't worry, we'll help you get back in.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('hopeui/assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('hopeui/assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('hopeui/assets/js/hope-ui.js') }}" defer></script>

</body>
</html>