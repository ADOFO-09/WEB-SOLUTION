<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ \App\Helpers\SettingHelper::churchName() }}</title>

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
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --success: #10b981;
            --error: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh; display: flex;
            background: var(--gray-100);
        }

        .brand-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 3rem; position: relative; overflow: hidden;
        }
        .brand-pattern {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .floating-shapes { position: absolute; width: 100%; height: 100%; pointer-events: none; }
        .shape { position: absolute; border-radius: 50%; background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%); opacity: 0.1; animation: float 15s ease-in-out infinite; }
        .shape-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .shape-2 { width: 200px; height: 200px; bottom: 10%; right: -50px; animation-delay: -5s; }
        .shape-3 { width: 150px; height: 150px; top: 40%; left: 10%; animation-delay: -10s; }
        @keyframes float { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(20px,-20px) scale(1.05); } }

        .brand-content { position: relative; z-index: 10; text-align: center; max-width: 480px; }
        .brand-logo {
            width: 110px; height: 110px;
            background: var(--white); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            animation: logoFloat 3s ease-in-out infinite; overflow: hidden;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        @keyframes logoFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .brand-content h1 { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 600; color: var(--white); margin-bottom: 0.5rem; }
        .brand-content .subtitle { color: var(--accent); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 2rem; }
        .brand-content p { color: rgba(255,255,255,0.7); font-size: 1rem; line-height: 1.7; margin-bottom: 2rem; }

        .steps { display: flex; flex-direction: column; gap: 0.875rem; text-align: left; }
        .step { display: flex; align-items: center; gap: 0.875rem; }
        .step-num {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(212,175,55,0.25);
            border: 1px solid rgba(212,175,55,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: var(--accent); flex-shrink: 0;
        }
        .step span { color: rgba(255,255,255,0.75); font-size: 0.9rem; }

        /* Form panel */
        .form-panel { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; background: var(--white); }
        .form-container { width: 100%; max-width: 420px; }

        .form-header { text-align: center; margin-bottom: 2rem; }
        .form-icon {
            width: 64px; height: 64px; background: rgba(30,58,95,0.08); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;
        }
        .form-icon svg { width: 30px; height: 30px; stroke: var(--primary); }
        .form-header h2 { font-family: 'Playfair Display', serif; font-size: 1.9rem; font-weight: 600; color: var(--gray-900); margin-bottom: 0.4rem; }
        .form-header p { color: var(--gray-500); font-size: 0.9rem; }

        .alert { display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .alert svg { width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px; }
        .alert-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: var(--error); }
        .alert-error svg { stroke: var(--error); }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem; }

        .input-wrapper { position: relative; }
        .input-wrapper input {
            width: 100%; padding: 0.875rem 3rem 0.875rem 3rem;
            border: 2px solid var(--gray-200); border-radius: 12px;
            font-size: 1rem; color: var(--gray-800); background: var(--white);
            transition: all 0.3s ease; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .input-wrapper input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(30,58,95,0.1); }
        .input-wrapper input::placeholder { color: var(--gray-400); }
        .input-wrapper input:read-only { background: var(--gray-100); color: var(--gray-500); cursor: default; }
        .input-wrapper input:read-only:focus { border-color: var(--gray-300); box-shadow: none; }

        .input-wrapper .icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; stroke: var(--gray-400); pointer-events: none; transition: stroke 0.3s ease; }
        .input-wrapper input:focus ~ .icon { stroke: var(--primary); }

        .password-toggle { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0.25rem; }
        .password-toggle svg { width: 20px; height: 20px; stroke: var(--gray-400); transition: stroke 0.3s ease; }
        .password-toggle:hover svg { stroke: var(--gray-600); }

        .field-error { font-size: 0.82rem; color: var(--error); margin-top: 0.4rem; }

        .password-hint { font-size: 0.8rem; color: var(--gray-400); margin-top: 0.4rem; }

        .btn-submit {
            width: 100%; padding: 1rem; margin-top: 0.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none; border-radius: 12px;
            color: var(--white); font-size: 1rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30,58,95,0.3);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 1.5rem;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(30,58,95,0.4); }
        .btn-submit svg { width: 18px; height: 18px; }

        .back-link { text-align: center; padding-top: 1.25rem; border-top: 1px solid var(--gray-200); }
        .back-link a { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 500; color: var(--primary); text-decoration: none; transition: color 0.3s; }
        .back-link a:hover { color: var(--primary-light); }
        .back-link a svg { width: 16px; height: 16px; stroke: currentColor; }

        @media (max-width: 1024px) { .brand-panel { display: none; } }
        @media (max-width: 480px) { .form-panel { padding: 1.5rem; } .form-header h2 { font-size: 1.6rem; } }
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
            <p>Choose a strong, memorable password that you haven't used before.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <span>Enter your email address (pre-filled)</span>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <span>Set a new password (min. 8 characters)</span>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <span>Sign in with your new credentials</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                </div>
                <h2>Set New Password</h2>
                <p>Create a new password for your account</p>
            </div>

            {{-- Validation errors summary --}}
            @if ($errors->any())
            <div class="alert alert-error">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email: pre-filled, read-only --}}
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $request->email) }}"
                               required readonly autocomplete="username">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                </div>

                {{-- New password --}}
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password"
                               required autofocus autocomplete="new-password"
                               placeholder="Minimum 8 characters">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <button type="button" class="password-toggle" onclick="toggleField('password','eye1')">
                            <svg id="eye1" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <p class="password-hint">Use letters, numbers and symbols for a stronger password</p>
                </div>

                {{-- Confirm password --}}
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               required autocomplete="new-password"
                               placeholder="Repeat your new password">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <button type="button" class="password-toggle" onclick="toggleField('password_confirmation','eye2')">
                            <svg id="eye2" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <span>Reset Password</span>
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
        function toggleField(fieldId, iconId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const els = document.querySelectorAll('.form-header, .form-group, .btn-submit, .back-link');
            els.forEach((el, i) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.5s ease';
                setTimeout(() => { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }, 80 + i * 80);
            });
        });
    </script>
</body>
</html>
