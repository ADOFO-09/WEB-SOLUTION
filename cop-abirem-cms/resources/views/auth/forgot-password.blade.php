<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Church of Pentecost Abirem CMS</title>

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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--gray-100);
        }

        /* Left branding panel */
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
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .floating-shapes { position: absolute; width: 100%; height: 100%; pointer-events: none; }
        .shape {
            position: absolute; border-radius: 50%;
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
            position: relative; z-index: 10;
            text-align: center; max-width: 500px;
        }

        .brand-logo {
            width: 120px; height: 120px;
            background: var(--white); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            animation: logoFloat 3s ease-in-out infinite;
            overflow: hidden;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: cover; display: block; }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .brand-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem; font-weight: 600;
            color: var(--white); margin-bottom: 0.5rem;
        }
        .brand-content .subtitle {
            color: var(--accent);
            font-size: 0.9rem; text-transform: uppercase;
            letter-spacing: 0.15em; margin-bottom: 2rem;
        }
        .brand-content p {
            color: rgba(255,255,255,0.7);
            font-size: 1rem; line-height: 1.7; margin-bottom: 2.5rem;
        }

        .lock-visual {
            display: flex; align-items: center; justify-content: center;
            gap: 1rem; margin-top: 1rem;
        }
        .lock-icon {
            width: 56px; height: 56px;
            background: rgba(212,175,55,0.2);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .lock-icon svg { width: 28px; height: 28px; stroke: var(--accent); }
        .lock-text { text-align: left; }
        .lock-text strong { display: block; color: var(--white); font-size: 0.95rem; margin-bottom: 0.2rem; }
        .lock-text span { color: rgba(255,255,255,0.6); font-size: 0.85rem; }

        /* Right form panel */
        .form-panel {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem;
            background: var(--white);
        }

        .form-container { width: 100%; max-width: 420px; }

        .form-header { text-align: center; margin-bottom: 2.5rem; }
        .form-icon {
            width: 64px; height: 64px;
            background: rgba(30,58,95,0.08); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .form-icon svg { width: 30px; height: 30px; stroke: var(--primary); }
        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem; font-weight: 600;
            color: var(--gray-900); margin-bottom: 0.5rem;
        }
        .form-header p { color: var(--gray-500); font-size: 0.95rem; line-height: 1.6; }

        /* Alerts */
        .alert {
            display: flex; align-items: flex-start; gap: 0.75rem;
            padding: 1rem; border-radius: 12px;
            margin-bottom: 1.5rem; font-size: 0.9rem;
        }
        .alert svg { width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px; }
        .alert-success {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.3);
            color: #065f46;
        }
        .alert-success svg { stroke: #10b981; }
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: var(--error);
        }
        .alert-error svg { stroke: var(--error); }

        /* Form elements */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block; font-size: 0.875rem; font-weight: 600;
            color: var(--gray-700); margin-bottom: 0.5rem;
        }
        .input-wrapper { position: relative; }
        .input-wrapper input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px; font-size: 1rem;
            color: var(--gray-800); background: var(--white);
            transition: all 0.3s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .input-wrapper input:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30,58,95,0.1);
        }
        .input-wrapper input::placeholder { color: var(--gray-400); }
        .input-wrapper .icon {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            width: 20px; height: 20px;
            stroke: var(--gray-400); pointer-events: none;
            transition: stroke 0.3s ease;
        }
        .input-wrapper input:focus ~ .icon { stroke: var(--primary); }

        .field-error {
            font-size: 0.82rem; color: var(--error);
            margin-top: 0.4rem; display: flex; align-items: center; gap: 0.3rem;
        }

        .btn-submit {
            width: 100%; padding: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none; border-radius: 12px;
            color: var(--white); font-size: 1rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30,58,95,0.3);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 1.5rem;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30,58,95,0.4);
        }
        .btn-submit svg { width: 18px; height: 18px; }

        .back-link {
            text-align: center; padding-top: 1.25rem;
            border-top: 1px solid var(--gray-200);
        }
        .back-link a {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-size: 0.9rem; font-weight: 500;
            color: var(--primary); text-decoration: none;
            transition: color 0.3s;
        }
        .back-link a:hover { color: var(--primary-light); }
        .back-link a svg { width: 16px; height: 16px; stroke: currentColor; }

        @media (max-width: 1024px) { .brand-panel { display: none; } }
        @media (max-width: 480px) {
            .form-panel { padding: 1.5rem; }
            .form-header h2 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <!-- Left branding panel -->
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
            <p>Enter your registered email address and we'll send you a secure link to reset your password.</p>

            <div class="lock-visual">
                <div class="lock-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <div class="lock-text">
                    <strong>Secure reset link</strong>
                    <span>Valid for 60 minutes only</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <div class="form-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <h2>Forgot Password?</h2>
                <p>No problem. Enter the email address linked to your account and we'll send you a reset link.</p>
            </div>

            {{-- Success status --}}
            @if (session('status'))
            <div class="alert alert-success">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            {{-- Email error --}}
            @error('email')
            <div class="alert alert-error">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
            @enderror

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               required autofocus
                               placeholder="your.email@example.com">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                    </svg>
                    <span>Send Reset Link</span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const els = document.querySelectorAll('.form-header, .form-group, .btn-submit, .back-link');
            els.forEach((el, i) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 80 + i * 80);
            });
        });
    </script>
</body>
</html>
