<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #ffffff;
            color: #333333;
            line-height: 1.6;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 48px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            font-size: 2.5rem;
            color: #333333;
            margin-bottom: 16px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 400;
            color: #333333;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }

        .logo-subtitle {
            color: #666666;
            font-size: 0.875rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-weight: 400;
            color: #333333;
            margin-bottom: 4px;
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            border: 1px solid #d0d0d0;
            border-radius: 2px;
            padding: 8px 12px;
            font-size: 0.875rem;
            transition: border-color 0.2s ease;
            background-color: #ffffff;
        }

        .form-control:focus {
            outline: none;
            border-color: #000000;
        }

        .login-btn {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #000000;
            border-radius: 2px;
            background-color: #000000;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 400;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 8px;
        }

        .login-btn:hover {
            background-color: #333333;
        }

        .login-btn:disabled {
            background-color: #cccccc;
            border-color: #cccccc;
            cursor: not-allowed;
        }

        .error-message {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
            border-radius: 2px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .form-check-input {
            margin-right: 8px;
        }

        .form-check-label {
            font-size: 0.875rem;
            color: #333333;
        }

        .forgot-password {
            text-align: center;
            margin-top: 16px;
        }

        .forgot-password a {
            color: #333333;
            text-decoration: underline;
            font-size: 0.875rem;
        }

        .forgot-password a:hover {
            text-decoration: none;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-card {
                padding: 32px 24px;
                margin: 16px;
            }

            .logo-text {
                font-size: 1.375rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 16px;
            }

            .login-card {
                padding: 24px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h1 class="logo-text">ERP System</h1>
                <p class="logo-subtitle">Clean & Simple</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username">
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password">
                </div>

                <!-- Remember Me -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-btn" id="loginButton">
                    <span class="btn-text">Sign In</span>
                </button>
            </form>

            <!-- Forgot Password -->
            <div class="forgot-password">
                <a href="#" onclick="alert('Contact administrator for password reset')">
                    Forgot password?
                </a>
                <span style="margin: 0 8px;">•</span>
                <a href="{{ route('skin.selector') }}">
                    <i class="fas fa-palette" style="margin-right: 4px;"></i>Change Style
                </a>
            </div>
        </div>
    </div>

    <script>
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            const btnText = button.querySelector('.btn-text');
            
            button.disabled = true;
            btnText.textContent = 'Signing in...';
        });

        // Auto-focus on email field
        window.addEventListener('load', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>
