<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Login - {{ \App\Helpers\SettingHelper::churchName() }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#1e3a5f;--primary-dark:#0f2744;--primary-light:#2d5a8a;--accent:#d4af37;--accent-light:#e8c94b;--white:#ffffff;--gray-100:#f1f5f9;--gray-200:#e2e8f0;--gray-400:#94a3b8;--gray-500:#64748b;--gray-700:#334155;--gray-800:#1e293b;--gray-900:#0f172a;--error:#ef4444;--success:#10b981;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;display:flex;background:var(--gray-100);}

        /* Brand panel */
        .brand-panel{flex:1;background:linear-gradient(135deg,var(--primary-dark) 0%,var(--primary) 50%,var(--primary-light) 100%);display:flex;flex-direction:column;justify-content:center;align-items:center;padding:3rem;position:relative;overflow:hidden;}
        .brand-pattern{position:absolute;inset:0;opacity:.05;background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
        .floating-shapes{position:absolute;inset:0;pointer-events:none;}
        .shape{position:absolute;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-light));opacity:.1;animation:float 15s ease-in-out infinite;}
        .shape-1{width:300px;height:300px;top:-100px;left:-100px;}
        .shape-2{width:200px;height:200px;bottom:10%;right:-50px;animation-delay:-5s;}
        .shape-3{width:150px;height:150px;top:40%;left:10%;animation-delay:-10s;}
        @keyframes float{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(20px,-20px) scale(1.05);}}
        .brand-content{position:relative;z-index:10;text-align:center;max-width:420px;}
        .brand-logo{width:110px;height:110px;background:var(--white);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 20px 40px rgba(0,0,0,.3);overflow:hidden;animation:logoFloat 3s ease-in-out infinite;}
        .brand-logo img{width:100%;height:100%;object-fit:cover;}
        @keyframes logoFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
        .brand-content h1{font-family:'Playfair Display',serif;font-size:2.1rem;font-weight:600;color:var(--white);margin-bottom:.4rem;}
        .brand-content .subtitle{color:var(--accent);font-size:.85rem;text-transform:uppercase;letter-spacing:.15em;margin-bottom:2rem;}
        .security-visual{display:flex;align-items:center;gap:1rem;margin-top:.5rem;background:rgba(255,255,255,.05);border-radius:16px;padding:1.25rem;}
        .security-icon{width:52px;height:52px;background:rgba(212,175,55,.2);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .security-icon svg{width:26px;height:26px;stroke:var(--accent);}
        .security-text{text-align:left;}
        .security-text strong{display:block;color:var(--white);font-size:.9rem;margin-bottom:.25rem;}
        .security-text span{color:rgba(255,255,255,.55);font-size:.82rem;line-height:1.5;}

        /* Form panel */
        .form-panel{flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;background:var(--white);}
        .form-container{width:100%;max-width:400px;}
        .form-header{text-align:center;margin-bottom:2rem;}
        .form-icon{width:68px;height:68px;background:rgba(30,58,95,.08);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;}
        .form-icon svg{width:32px;height:32px;stroke:var(--primary);}
        .form-header h2{font-family:'Playfair Display',serif;font-size:1.85rem;font-weight:600;color:var(--gray-900);margin-bottom:.4rem;}
        .form-header p{color:var(--gray-500);font-size:.9rem;line-height:1.6;}
        .form-header .email-hint{font-weight:600;color:var(--gray-700);}

        /* Alerts */
        .alert{display:flex;align-items:flex-start;gap:.75rem;padding:.9rem 1rem;border-radius:10px;margin-bottom:1.25rem;font-size:.875rem;}
        .alert svg{width:18px;height:18px;flex-shrink:0;margin-top:1px;}
        .alert-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:var(--error);}
        .alert-error svg{stroke:var(--error);}
        .alert-success{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);color:var(--success);}
        .alert-success svg{stroke:var(--success);}

        /* Code input */
        .code-row{display:flex;gap:.6rem;justify-content:center;margin:1.5rem 0;}
        .code-digit{width:48px;height:60px;border:2px solid var(--gray-200);border-radius:12px;font-size:1.6rem;font-weight:700;text-align:center;color:var(--gray-800);background:var(--white);transition:all .2s;font-family:'Plus Jakarta Sans',sans-serif;}
        .code-digit:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px rgba(30,58,95,.1);}
        .code-digit.filled{border-color:var(--primary);background:rgba(30,58,95,.03);}

        /* Submit */
        .btn-submit{width:100%;padding:.95rem;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border:none;border-radius:12px;color:var(--white);font-size:1rem;font-weight:600;cursor:pointer;transition:all .3s;box-shadow:0 4px 15px rgba(30,58,95,.3);font-family:'Plus Jakarta Sans',sans-serif;margin-bottom:1.25rem;}
        .btn-submit:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 8px 25px rgba(30,58,95,.4);}
        .btn-submit:disabled{opacity:.5;cursor:not-allowed;transform:none;}

        /* Resend */
        .resend-row{text-align:center;padding-top:1.25rem;border-top:1px solid var(--gray-200);}
        .resend-row p{font-size:.875rem;color:var(--gray-500);margin-bottom:.5rem;}
        .btn-resend{background:none;border:none;color:var(--primary);font-size:.875rem;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;padding:0;transition:color .2s;}
        .btn-resend:hover{color:var(--primary-light);}
        .btn-resend:disabled{color:var(--gray-400);cursor:not-allowed;}
        .countdown{font-size:.8rem;color:var(--gray-400);margin-top:.25rem;}

        /* Back link */
        .back-link{text-align:center;margin-top:1.5rem;}
        .back-link a{display:inline-flex;align-items:center;gap:.4rem;font-size:.85rem;color:var(--gray-500);text-decoration:none;transition:color .2s;}
        .back-link a:hover{color:var(--primary);}
        .back-link a svg{width:14px;height:14px;stroke:currentColor;}

        @media(max-width:1024px){.brand-panel{display:none;}}
        @media(max-width:480px){.form-panel{padding:1.5rem;}.code-digit{width:42px;height:54px;font-size:1.4rem;}}
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
            <div class="brand-logo">
                <img src="{{ asset('images/cop-logo.png') }}" alt="COP Logo" onerror="this.style.display='none'">
            </div>
            <h1>Church of Pentecost</h1>
            <div class="subtitle">Abirem Assembly</div>
            <div class="security-visual">
                <div class="security-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <div class="security-text">
                    <strong>Two-Factor Authentication</strong>
                    <span>An extra layer of security<br>to protect your account.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <div class="form-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
                <h2>Check Your Email</h2>
                <p>
                    We sent a 6-digit code to<br>
                    <span class="email-hint">{{ $maskedEmail }}</span>
                </p>
            </div>

            @if($errors->any())
            <div class="alert alert-error">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('resent'))
            <div class="alert alert-success">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('resent') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" id="twoFaForm">
                @csrf

                <div class="code-row" id="codeRow">
                    @for($i = 0; $i < 6; $i++)
                    <input type="tel" maxlength="1" class="code-digit" data-index="{{ $i }}"
                           autocomplete="one-time-code" inputmode="numeric"
                           aria-label="Digit {{ $i + 1 }}">
                    @endfor
                </div>

                <input type="hidden" name="code" id="codeHidden">

                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    Verify Code
                </button>
            </form>

            <div class="resend-row">
                <p>Didn't receive the email?</p>
                <form method="POST" action="{{ route('two-factor.resend') }}" id="resendForm">
                    @csrf
                    <button type="submit" class="btn-resend" id="resendBtn" disabled>
                        Resend Code
                    </button>
                </form>
                <div class="countdown" id="countdown"></div>
            </div>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Back to login
                </a>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const digits    = Array.from(document.querySelectorAll('.code-digit'));
        const hidden    = document.getElementById('codeHidden');
        const submitBtn = document.getElementById('submitBtn');
        const resendBtn = document.getElementById('resendBtn');
        const countdown = document.getElementById('countdown');

        // ── Digit auto-advance ────────────────────────────────────────────
        digits.forEach((input, idx) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/g, '').slice(-1);
                input.classList.toggle('filled', input.value !== '');
                if (input.value && idx < digits.length - 1) digits[idx + 1].focus();
                syncHidden();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    digits[idx - 1].value = '';
                    digits[idx - 1].classList.remove('filled');
                    digits[idx - 1].focus();
                    syncHidden();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                pasted.split('').forEach((ch, i) => {
                    if (digits[i]) {
                        digits[i].value = ch;
                        digits[i].classList.add('filled');
                    }
                });
                const next = digits[Math.min(pasted.length, digits.length - 1)];
                if (next) next.focus();
                syncHidden();
            });
        });

        function syncHidden() {
            const code = digits.map(d => d.value).join('');
            hidden.value = code;
            submitBtn.disabled = code.length < 6;
        }

        // ── Resend countdown (60 s) ───────────────────────────────────────
        let remaining = 60;
        const tick = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(tick);
                resendBtn.disabled = false;
                countdown.textContent = '';
            } else {
                countdown.textContent = `Wait ${remaining}s before resending`;
            }
        }, 1000);

        // ── Fade-in ───────────────────────────────────────────────────────
        ['.form-header', '.code-row', '.btn-submit', '.resend-row', '.back-link'].forEach((sel, i) => {
            const el = document.querySelector(sel);
            if (!el) return;
            el.style.cssText += 'opacity:0;transform:translateY(18px);transition:all .45s ease';
            setTimeout(() => el.style.cssText += 'opacity:1;transform:translateY(0)', 70 + i * 70);
        });

        // Focus first digit on load
        digits[0].focus();
    })();
    </script>
</body>
</html>
