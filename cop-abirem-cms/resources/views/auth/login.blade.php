<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - Church of Pentecost Abirem CMS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-dark: #0f2744;
            --primary-light: #2d5a8a;
            --accent: #d4af37;
            --accent-light: #e8c94b;
            --accent-dark: #b8960e;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --success: #10b981;
            --error: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--gray-100);
        }
        
        /* Left Panel - Branding */
        .brand-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .brand-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            opacity: 0.1;
            animation: float 15s ease-in-out infinite;
        }
        
        .shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .shape-2 { width: 200px; height: 200px; bottom: 10%; right: -50px; animation-delay: -5s; }
        .shape-3 { width: 150px; height: 150px; top: 40%; left: 10%; animation-delay: -10s; }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -20px) scale(1.05); }
        }
        
        .brand-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 500px;
        }
        
        .brand-logo {
            width: 100px;
            height: 100px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: logoFloat 3s ease-in-out infinite;
            overflow: hidden;
            padding: 8px;
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .brand-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 0.5rem;
        }
        
        .brand-content .subtitle {
            color: var(--accent);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 2rem;
        }
        
        .brand-content p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 3rem;
        }
        
        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            text-align: left;
        }
        
        .brand-feature {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }
        
        .brand-feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .brand-feature-icon svg {
            width: 20px;
            height: 20px;
            stroke: var(--accent);
        }
        
        /* Right Panel - Login Form */
        .login-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--white);
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--gray-500);
            font-size: 0.95rem;
        }
        
        /* Error/Success Messages */
        .error-message {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            color: var(--error);
            font-size: 0.9rem;
        }
        
        .error-message svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        
        .success-message {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            color: var(--success);
            font-size: 0.9rem;
        }
        
        .success-message svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        
        /* Form Styles */
        .login-form {
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            color: var(--gray-800);
            background: var(--white);
            transition: all 0.3s ease;
        }
        
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
        }
        
        .input-wrapper input::placeholder {
            color: var(--gray-400);
        }
        
        .input-wrapper .icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            stroke: var(--gray-400);
            pointer-events: none;
            transition: stroke 0.3s ease;
        }
        
        .input-wrapper input:focus + .icon,
        .input-wrapper input:focus ~ .icon {
            stroke: var(--primary);
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.25rem;
        }
        
        .password-toggle svg {
            width: 20px;
            height: 20px;
            stroke: var(--gray-400);
            transition: stroke 0.3s ease;
        }
        
        .password-toggle:hover svg {
            stroke: var(--gray-600);
        }
        
        /* Form Options */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--gray-600);
        }
        
        .remember-me input {
            display: none;
        }
        
        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-300);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .checkbox-custom svg {
            width: 12px;
            height: 12px;
            stroke: var(--white);
            stroke-width: 3;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .remember-me input:checked + .checkbox-custom {
            background: var(--primary);
            border-color: var(--primary);
        }
        
        .remember-me input:checked + .checkbox-custom svg {
            opacity: 1;
        }
        
        .forgot-link {
            font-size: 0.9rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: var(--primary-light);
        }
        
        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 12px;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 95, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }
        
        .btn-submit:hover svg {
            transform: translateX(4px);
        }
        
        /* Help Section */
        .help-section {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .help-section p {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }
        
        .help-section a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .help-section a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .brand-panel {
                display: none;
            }
            
            .login-panel {
                flex: 1;
            }
        }
        
        @media (max-width: 480px) {
            .login-panel {
                padding: 1.5rem;
            }
            
            .login-header h2 {
                font-size: 1.75rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Left Panel -->
    <div class="brand-panel">
        <div class="brand-pattern"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="brand-content">
            <div class="brand-logo">
                <img src="{{ asset('images/cop-logo.png') }}" alt="COP Logo" onerror="this.style.display='none'">
            </div>
            <h1>Church of Pentecost</h1>
            <div class="subtitle">Abirem Assembly</div>
            <p>Empowering our church community with modern tools for efficient administration, member care, and spiritual growth.</p>
            
            <div class="brand-features">
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                            <path d="M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <span>Complete member management</span>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <span>Secure access control</span>
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                    </div>
                    <span>24/7 system availability</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Panel -->
    <div class="login-panel">
        <div class="login-container">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to continue to your dashboard</p>
            </div>
            
            <!-- Error Messages -->
            @if ($errors->any())
            <div class="error-message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif
            
            @if (session('status'))
            <div class="success-message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif
            
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span class="checkbox-custom">
                            <svg viewBox="0 0 24 24" fill="none">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </span>
                        <span>Remember me</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                    @endif
                </div>
                
                <button type="submit" class="btn-submit">
                    <span>Sign In</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>
            
            <div class="help-section">
                <p>Need help accessing your account?</p>
                <p>Contact church admin or <a href="mailto:support@copabirem.org">support@copabirem.org</a></p>
            </div>
        </div>
    </div>
    
    <script>
        // Password toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
        
        // Form animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.login-header, .form-group, .form-options, .btn-submit, .help-section');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        });
    </script>
</body>
</html>