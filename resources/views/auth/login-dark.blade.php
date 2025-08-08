<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        .login-card {
            background: #2d2d2d;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #404040;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 3rem;
            color: #ff6b6b;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 10px rgba(255, 107, 107, 0.3));
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 5px;
        }

        .logo-subtitle {
            color: #888888;
            font-size: 0.9rem;
        }

        .form-label {
            font-weight: 500;
            color: #cccccc;
            margin-bottom: 8px;
        }

        .form-control {
            background-color: #404040;
            border: 1px solid #555555;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #505050;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
            color: #ffffff;
        }

        .form-control::placeholder {
            color: #888888;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #ee5a52 0%, #dc4e41 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .login-btn:disabled {
            background: #555555;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .error-message {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ff6b6b;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #ff6b6b;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #ee5a52;
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .form-check-label {
            color: #cccccc;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-card {
                padding: 30px 25px;
                margin: 10px;
            }

            .logo-text {
                font-size: 1.6rem;
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
                <p class="logo-subtitle">Dark Mode Experience</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="Enter your email"
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username">
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password"
                           required 
                           autocomplete="current-password">
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-btn" id="loginButton">
                    <span class="btn-text">Login to Dashboard</span>
                </button>
            </form>

            <!-- Forgot Password -->
            <div class="forgot-password">
                <a href="#" onclick="alert('Contact administrator for password reset')">
                    Forgot your password?
                </a>
                <span class="mx-2">•</span>
                <a href="{{ route('skin.selector') }}">
                    <i class="fas fa-palette me-1"></i>Change Style
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            const btnText = button.querySelector('.btn-text');
            
            button.disabled = true;
            btnText.textContent = 'Logging in...';
        });

        // Auto-focus on email field
        window.addEventListener('load', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>
