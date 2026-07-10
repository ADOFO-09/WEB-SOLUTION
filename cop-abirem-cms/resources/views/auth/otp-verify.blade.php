<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enter OTP - {{ \App\Helpers\SettingHelper::churchName() }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:#1e3a5f; --primary-dark:#0f2744; --primary-light:#2d5a8a;
            --accent:#d4af37; --accent-light:#e8c94b; --white:#ffffff;
            --gray-100:#f1f5f9; --gray-200:#e2e8f0; --gray-400:#94a3b8;
            --gray-500:#64748b; --gray-700:#334155; --gray-800:#1e293b;
            --gray-900:#0f172a; --error:#ef4444; --success:#10b981;
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
        .brand-content p{color:rgba(255,255,255,.7);font-size:1rem;line-height:1.7;}
        .steps{margin-top:2rem;display:flex;flex-direction:column;gap:.75rem;text-align:left;}
        .step{display:flex;align-items:center;gap:.75rem;}
        .step-num{width:28px;height:28px;border-radius:50%;background:rgba(212,175,55,.25);border:1px solid var(--accent);color:var(--accent);font-size:.8rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .step span{color:rgba(255,255,255,.75);font-size:.9rem;}
        .form-panel{flex:1;display:flex;align-items:center;justify-content:center;padding:2rem;background:var(--white);overflow-y:auto;}
        .form-container{width:100%;max-width:440px;}
        .form-header{text-align:center;margin-bottom:2rem;}
        .form-icon{width:64px;height:64px;background:rgba(30,58,95,.08);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;}
        .form-icon svg{width:30px;height:30px;stroke:var(--primary);}
        .form-header h2{font-family:'Playfair Display',serif;font-size:1.9rem;font-weight:600;color:var(--gray-900);margin-bottom:.5rem;}
        .form-header p{color:var(--gray-500);font-size:.95rem;line-height:1.6;}
        .phone-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(30,58,95,.06);border:1px solid rgba(30,58,95,.15);border-radius:20px;padding:.35rem .9rem;font-size:.85rem;font-weight:600;color:var(--primary);margin-top:.6rem;}
        .alert{display:flex;align-items:flex-start;gap:.75rem;padding:1rem;border-radius:12px;margin-bottom:1.5rem;font-size:.9rem;}
        .alert svg{width:20px;height:20px;flex-shrink:0;margin-top:1px;}
        .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--error);}
        .alert-error svg{stroke:var(--error);}
        /* OTP digit boxes */
        .otp-label{display:block;font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:.75rem;}
        .otp-boxes{display:flex;gap:.6rem;justify-content:center;margin-bottom:.4rem;}
        .otp-box{width:52px;height:60px;border:2px solid var(--gray-200);border-radius:12px;font-size:1.6rem;font-weight:700;text-align:center;color:var(--gray-900);background:var(--white);transition:all .2s ease;font-family:'Plus Jakarta Sans',sans-serif;-moz-appearance:textfield;}
        .otp-box::-webkit-outer-spin-button,.otp-box::-webkit-inner-spin-button{-webkit-appearance:none;}
        .otp-box:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px rgba(30,58,95,.1);}
        .otp-box.filled{border-color:var(--primary);background:rgba(30,58,95,.04);}
        .otp-hidden{position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;}
        .form-group{margin-bottom:1.25rem;}
        .form-group label{display:block;font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:.5rem;}
        .input-wrapper{position:relative;}
        .input-wrapper input{width:100%;padding:.875rem 2.75rem .875rem 3rem;border:2px solid var(--gray-200);border-radius:12px;font-size:1rem;color:var(--gray-800);background:var(--white);transition:all .3s ease;font-family:'Plus Jakarta Sans',sans-serif;}
        .input-wrapper input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px rgba(30,58,95,.1);}
        .input-wrapper input::placeholder{color:var(--gray-400);}
        .input-wrapper .icon{position:absolute;left:1rem;top:50%;transform:translateY(-50%);width:20px;height:20px;stroke:var(--gray-400);pointer-events:none;}
        .toggle-pw{position:absolute;right:.9rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:.25rem;color:var(--gray-400);}
        .toggle-pw svg{width:18px;height:18px;stroke:currentColor;display:block;}
        .field-error{font-size:.82rem;color:var(--error);margin-top:.4rem;}
        .btn-submit{width:100%;padding:1rem;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border:none;border-radius:12px;color:var(--white);font-size:1rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.75rem;transition:all .3s ease;box-shadow:0 4px 15px rgba(30,58,95,.3);font-family:'Plus Jakarta Sans',sans-serif;margin-top:1.5rem;margin-bottom:1.5rem;}
        .btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(30,58,95,.4);}
        .back-link{text-align:center;padding-top:1.25rem;border-top:1px solid var(--gray-200);}
        .back-link a{display:inline-flex;align-items:center;gap:.5rem;font-size:.9rem;font-weight:500;color:var(--primary);text-decoration:none;}
        .back-link a svg{width:16px;height:16px;stroke:currentColor;}
        .divider{display:flex;align-items:center;gap:.75rem;margin:1.5rem 0;}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--gray-200);}
        .divider span{font-size:.8rem;color:var(--gray-400);white-space:nowrap;}
        @media(max-width:1024px){.brand-panel{display:none;}}
        @media(max-width:480px){.form-panel{padding:1.5rem;}.otp-box{width:44px;height:54px;font-size:1.4rem;}}
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
            <p>Check your phone for the 6-digit code we just sent you.</p>
            <div class="steps">
                <div class="step"><div class="step-num">1</div><span>Open your SMS messages</span></div>
                <div class="step"><div class="step-num">2</div><span>Find the code from COP Abirem</span></div>
                <div class="step"><div class="step-num">3</div><span>Enter it here and set your new password</span></div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <div class="form-icon">
                    <svg fill="none" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <h2>Enter Your Code</h2>
                <p>We sent a 6-digit code to</p>
                <div class="phone-badge">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/>
                    </svg>
                    ****{{ substr(preg_replace('/\D/', '', $maskedPhone), -4) }}
                </div>
            </div>

            @error('otp')
            <div class="alert alert-error">
                <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span>{{ $message }}</span>
            </div>
            @enderror

            <form method="POST" action="{{ route('password.sms.verify.store') }}">
                @csrf

                {{-- OTP hidden input (synced from digit boxes) --}}
                <input type="hidden" name="otp" id="otp-hidden">

                {{-- OTP digit boxes --}}
                <div style="margin-bottom:1.5rem;">
                    <label class="otp-label">6-Digit Code</label>
                    <div class="otp-boxes" id="otp-boxes">
                        @for($i = 0; $i < 6; $i++)
                        <input type="number" class="otp-box" maxlength="1" min="0" max="9"
                               inputmode="numeric" autocomplete="one-time-code"
                               data-index="{{ $i }}">
                        @endfor
                    </div>
                    @error('otp')
                    <p class="field-error" style="text-align:center;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="divider"><span>then set your new password</span></div>

                {{-- New password --}}
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password"
                               required autocomplete="new-password"
                               placeholder="Minimum 8 characters">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <button type="button" class="toggle-pw" onclick="togglePw('password',this)" tabindex="-1">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               required autocomplete="new-password"
                               placeholder="Re-enter your password">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation',this)" tabindex="-1">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                    <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit" id="submit-btn" disabled>
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Reset Password</span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('password.sms.request') }}">
                    <svg fill="none" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Didn't receive a code? Try again
                </a>
            </div>
        </div>
    </div>

    <script>
        // ── OTP digit boxes ────────────────────────────────────────────────
        const boxes  = Array.from(document.querySelectorAll('.otp-box'));
        const hidden = document.getElementById('otp-hidden');
        const submit = document.getElementById('submit-btn');

        function syncOtp() {
            const val = boxes.map(b => b.value.slice(-1)).join('');
            hidden.value = val;
            boxes.forEach(b => b.classList.toggle('filled', b.value !== ''));
            submit.disabled = val.length < 6;
        }

        boxes.forEach((box, idx) => {
            box.addEventListener('input', function () {
                // Keep only last digit typed
                this.value = this.value.slice(-1).replace(/\D/, '');
                syncOtp();
                if (this.value && idx < boxes.length - 1) boxes[idx + 1].focus();
            });

            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && idx > 0) {
                    boxes[idx - 1].value = '';
                    boxes[idx - 1].focus();
                    syncOtp();
                }
            });

            box.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                paste.split('').slice(0, 6).forEach((ch, i) => {
                    if (boxes[i]) boxes[i].value = ch;
                });
                syncOtp();
                const next = Math.min(paste.length, 5);
                boxes[next].focus();
            });
        });

        // Auto-focus first box
        boxes[0].focus();

        // ── Password visibility toggle ────────────────────────────────────
        function togglePw(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // ── Entry animations ──────────────────────────────────────────────
        document.querySelectorAll('.form-header, .otp-boxes, .form-group, .btn-submit, .back-link').forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.5s ease';
            setTimeout(() => { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }, 80 + i * 80);
        });
    </script>
</body>
</html>
