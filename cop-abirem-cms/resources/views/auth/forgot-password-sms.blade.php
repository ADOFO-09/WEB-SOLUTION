<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset via SMS - {{ \App\Helpers\SettingHelper::churchName() }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#1e3a5f; --primary-dark:#0f2744; --primary-light:#2d5a8a;
            --accent:#d4af37; --accent-light:#e8c94b; --white:#ffffff;
            --gray-100:#f1f5f9; --gray-200:#e2e8f0; --gray-400:#94a3b8;
            --gray-500:#64748b; --gray-700:#334155; --gray-800:#1e293b;
            --gray-900:#0f172a; --error:#ef4444;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;display:flex;background:var(--gray-100);}
        .brand-panel{flex:1;background:linear-gradient(135deg,var(--primary-dark) 0%,var(--primary) 50%,var(--primary-light) 100%);display:flex;flex-direction:column;justify-content:center;align-items:center;padding:3rem;position:relative;overflow:hidden;}
        .brand-pattern{position:absolute;top:0;left:0;width:100%;height:100%;opacity:.05;background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
        .floating-shapes{position:absolute;width:100%;height:100%;pointer-events:none;}
        .shape{position:absolute;border-radius:50%;background:linear-gradient(135deg,var(--accent) 0%,var(--accent-light) 100%);opacity:.1;animation:float 15s ease-in-out infinite;}
        .shape-1{width:300px;height:300px;top:-100px;left:-100px;}
        .shape-2{width:200px;height:200px;bottom:10%;right:-50px;animation-delay:-5s;}
        .shape-3{width:150px;height:150px;top:40%;left:10%;animation-delay:-10s;}
        @keyframes float{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(20px,-20px) scale(1.05);}}
        .brand-content{position:relative;z-index:10;text-align:center;max-width:500px;}
        .brand-logo{width:120px;height:120px;background:var(--white);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 20px 40px rgba(0,0,0,.3);animation:logoFloat 3s ease-in-out infinite;overflow:hidden;}
        .brand-logo img{width:100%;height:100%;object-fit:cover;display:block;}
        @keyframes logoFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
        .brand-content h1{font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:600;color:var(--white);margin-bottom:.5rem;}
        .brand-content .subtitle{color:var(--accent);font-size:.9rem;text-transform:uppercase;letter-spacing:.15em;margin-bottom:2rem;}
        .brand-content p{color:rgba(255,255,255,.7);font-size:1rem;line-height:1.7;margin-bottom:2.5rem;}
        .sms-visual{display:flex;align-items:center;justify-content:center;gap:1rem;margin-top:1rem;}
        .sms-icon{width:56px;height:56px;background:rgba(212,175,55,.2);border-radius:16px;display:flex;align-items:center;justify-content:center;}
        .sms-icon svg{width:28px;height:28px;stroke:var(--accent);}
        .sms-text{text-align:left;}
        .sms-text strong{display:block;color:var(--white);font-size:.95rem;margin-bottom:.2rem;}
        .sms-text span{color:rgba(255,255,255,.6);font-size:.85rem;}
        .form-panel{flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;background:var(--white);}
        .form-container{width:100%;max-width:420px;}
        .form-header{text-align:center;margin-bottom:2.5rem;}
        .form-icon{width:64px;height:64px;background:rgba(30,58,95,.08);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;}
        .form-icon svg{width:30px;height:30px;stroke:var(--primary);}
        .form-header h2{font-family:'Playfair Display',serif;font-size:1.9rem;font-weight:600;color:var(--gray-900);margin-bottom:.5rem;}
        .form-header p{color:var(--gray-500);font-size:.95rem;line-height:1.6;}
        .alert{display:flex;align-items:flex-start;gap:.75rem;padding:1rem;border-radius:12px;margin-bottom:1.5rem;font-size:.9rem;}
        .alert svg{width:20px;height:20px;flex-shrink:0;margin-top:1px;}
        .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--error);}
        .alert-error svg{stroke:var(--error);}
        .form-group{margin-bottom:1.5rem;}
        .form-group label{display:block;font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:.5rem;}
        .input-wrapper{position:relative;}
        .input-wrapper input{width:100%;padding:.875rem 1rem .875rem 3rem;border:2px solid var(--gray-200);border-radius:12px;font-size:1rem;color:var(--gray-800);background:var(--white);transition:all .3s ease;font-family:'Plus Jakarta Sans',sans-serif;}
        .input-wrapper input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px rgba(30,58,95,.1);}
        .input-wrapper input::placeholder{color:var(--gray-400);}
        .input-wrapper .icon{position:absolute;left:1rem;top:50%;transform:translateY(-50%);width:20px;height:20px;stroke:var(--gray-400);pointer-events:none;transition:stroke .3s ease;}
        .input-wrapper input:focus~.icon{stroke:var(--primary);}
        .field-error{font-size:.82rem;color:var(--error);margin-top:.4rem;}
        .btn-submit{width:100%;padding:1rem;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border:none;border-radius:12px;color:var(--white);font-size:1rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.75rem;transition:all .3s ease;box-shadow:0 4px 15px rgba(30,58,95,.3);font-family:'Plus Jakarta Sans',sans-serif;margin-bottom:1.5rem;}
        .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(30,58,95,.4);}
        .btn-submit svg{width:18px;height:18px;}
        .back-link{text-align:center;padding-top:1.25rem;border-top:1px solid var(--gray-200);}
        .back-link a{display:inline-flex;align-items:center;gap:.5rem;font-size:.9rem;font-weight:500;color:var(--primary);text-decoration:none;transition:color .3s;}
        .back-link a:hover{color:var(--primary-light);}
        .back-link a svg{width:16px;height:16px;stroke:currentColor;}
        @media(max-width:1024px){.brand-panel{display:none;}}
        @media(max-width:480px){.form-panel{padding:1.5rem;}.form-header h2{font-size:1.6rem;}}
    </style>
</head>
<body>
    <div class="brand-panel">
        <div class="brand-pattern"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="brand-content">
            @php
                $_authName   = \App\Helpers\SettingHelper::churchName();
                $_authSlogan = \App\Helpers\SettingHelper::churchSlogan();
                $_authLogo   = \App\Helpers\SettingHelper::churchLogo();
            @endphp
            <div class="brand-logo">
                <img src="{{ $_authLogo ?? asset('images/cop-logo.png') }}" alt="{{ $_authName }}" onerror="this.style.display='none'">
            </div>
            <h1>{{ $_authName }}</h1>
            @if($_authSlogan)<div class="subtitle">{{ $_authSlogan }}</div>@endif
            <p>Enter your registered email address and we'll send a 6-digit code to your registered phone number.</p>
            <div class="sms-visual">
                <div class="sms-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/>
                    </svg>
                </div>
                <div class="sms-text">
                    <strong>6-digit OTP by SMS</strong>
                    <span>Valid for 10 minutes only</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <div class="form-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/>
                    </svg>
                </div>
                <h2>Reset via SMS</h2>
                <p>Enter your account email and we'll send a one-time code to your registered phone number.</p>
            </div>

            @error('email')
            <div class="alert alert-error">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
            @enderror

            <form method="POST" action="{{ route('password.sms.send') }}">
                @csrf
                {{-- Honeypot: invisible to humans, bots fill it in --}}
                <input type="text" name="website" value="" style="opacity:0;position:absolute;top:0;left:0;height:0;width:0;z-index:-1;" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               required autofocus
                               placeholder="your@email.com">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    @error('email')
                    <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                    </svg>
                    <span>Send OTP Code</span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('password.request') }}">
                    <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Reset via email instead
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.form-header, .form-group, .btn-submit, .back-link').forEach((el, i) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.5s ease';
                setTimeout(() => { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }, 80 + i * 80);
            });
        });
    </script>
</body>
</html>
