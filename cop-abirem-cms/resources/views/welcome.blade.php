<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Church of Pentecost Abirem CMS</title>
    
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }
        
        .shape-1 { width: 400px; height: 400px; top: -100px; right: -100px; animation-delay: 0s; }
        .shape-2 { width: 300px; height: 300px; bottom: -50px; left: -50px; animation-delay: -5s; }
        .shape-3 { width: 200px; height: 200px; top: 50%; right: 10%; animation-delay: -10s; }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -20px) rotate(5deg); }
            50% { transform: translate(-10px, 20px) rotate(-5deg); }
            75% { transform: translate(-20px, -10px) rotate(3deg); }
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background: linear-gradient(to bottom, rgba(15, 39, 68, 0.9) 0%, transparent 100%);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(212, 175, 55, 0.6);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            background: var(--white);
            flex-shrink: 0;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .logo-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--white);
            letter-spacing: -0.02em;
        }
        
        .logo-text span {
            font-size: 0.75rem;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .nav-links a {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .nav-links .login-btn {
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .nav-links .login-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--white);
        }
        
        
        /* Main Content */
        .main {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            z-index: 10;
        }
        
        .hero {
            text-align: center;
            max-width: 900px;
            animation: fadeInUp 1s ease-out;
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
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }
        
        .hero-badge svg {
            width: 16px;
            height: 16px;
        }
        
        .hero h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 600;
            color: var(--white);
            line-height: 1.1;
            margin-bottom: 1.5rem;
            animation: fadeInUp 1s ease-out 0.3s backwards;
        }
        
        .hero h2 .accent {
            color: var(--accent);
            position: relative;
        }
        
        .hero h2 .accent::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--accent);
            opacity: 0.3;
            border-radius: 4px;
        }
        
        .hero p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 600px;
            margin: 0 auto 3rem;
            line-height: 1.7;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.5s backwards;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--accent);
            color: var(--primary-dark);
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
        }
        
        .btn-primary:hover {
            background: var(--accent-light);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--white);
        }
        
        .btn svg {
            width: 20px;
            height: 20px;
        }
        
        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 5rem;
            animation: fadeInUp 1s ease-out 0.6s backwards;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: left;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
            border-color: var(--accent);
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        
        .feature-icon svg {
            width: 24px;
            height: 24px;
            stroke: var(--primary-dark);
        }
        
        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 0.5rem;
        }
        
        .feature-card p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 3rem 2rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            position: relative;
            z-index: 10;
        }
        
        .footer a {
            color: var(--accent);
            text-decoration: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }
            
            .logo-text h1 {
                font-size: 1.2rem;
            }
            
            .logo-text span {
                display: none;
            }
            
            .hero h2 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>
    
    <header class="header">
        <div class="logo">            
            <div class="logo-icon">
                <img src="{{ asset('images/cop-logo.png') }}" alt="COP Abirem">
            </div>

            <div class="logo-text">
                <h1>COP Abirem</h1>
                <span>Church Management System</span>
            </div>
        </div>
        
        <nav class="nav-links">
            @auth
                <a href="{{ url('/dashboard') }}" class="login-btn">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="login-btn">Sign In</a>
            @endauth
        </nav>
    </header>
    
    <main class="main">
        <div class="hero">
            <div class="hero-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                Empowering Church Administration
            </div>
            
            <h2>
                Managing God's People with <span class="accent">Excellence</span>
            </h2>
            
            <p>
                A comprehensive church management system designed to streamline membership records, 
                track giving, manage attendance, and strengthen your church community.
            </p>
            
            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                    </svg>
                    Sign In to Portal
                </a>
                <a href="#features" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4M12 8h.01"/>
                    </svg>
                    Learn More
                </a>
            </div>
            
            <div class="features" id="features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <h3>Membership Management</h3>
                    <p>Track member profiles, family relationships, and spiritual growth with ease.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                    </div>
                    <h3>Financial Tracking</h3>
                    <p>Manage tithes, offerings, pledges, and generate detailed financial reports.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <path d="M9 16l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3>Attendance Tracking</h3>
                    <p>Record service attendance and monitor congregation engagement patterns.</p>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="footer">
        <p>&copy; {{ date('Y') }} Church of Pentecost - Abirem. Surely the Lord is in this place🙏</p>
    </footer>
</body>
</html>
