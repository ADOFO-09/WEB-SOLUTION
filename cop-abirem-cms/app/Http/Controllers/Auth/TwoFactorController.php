<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    private const CODE_EXPIRY_MINUTES    = 10;
    private const MAX_ATTEMPTS           = 5;
    private const RESEND_THROTTLE_SECONDS = 60;

    // ── Show challenge page ───────────────────────────────────────────────────

    public function show(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('two_fa_code_hash')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', [
            'maskedEmail' => $this->maskEmail(auth()->user()->email),
        ]);
    }

    // ── Verify submitted code ─────────────────────────────────────────────────

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $session = $request->session();

        if (!$session->has('two_fa_code_hash')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        if (now()->timestamp > (int) $session->get('two_fa_expires_at')) {
            $this->clearSession($session);
            return redirect()->route('login')
                ->withErrors(['email' => 'Your verification code expired. Please log in again.']);
        }

        $attempts = (int) $session->get('two_fa_attempts', 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->clearSession($session);
            return redirect()->route('login')
                ->withErrors(['email' => 'Too many incorrect attempts. Please log in again.']);
        }

        $session->put('two_fa_attempts', $attempts + 1);

        if (hash('sha256', $request->input('code')) !== $session->get('two_fa_code_hash')) {
            $remaining = self::MAX_ATTEMPTS - ($attempts + 1);
            $msg = $remaining > 0
                ? "Incorrect code. {$remaining} attempt(s) remaining."
                : 'Too many incorrect attempts. Please log in again.';

            return back()->withErrors(['code' => $msg]);
        }

        // ── Code correct ──────────────────────────────────────────────────────
        $this->clearSession($session);
        $session->put('two_fa_verified', true);

        return redirect(RoleHelper::getDashboardUrl(auth()->user()));
    }

    // ── Resend code ───────────────────────────────────────────────────────────

    public function resend(Request $request): RedirectResponse
    {
        $sentAt = (int) $request->session()->get('two_fa_sent_at', 0);
        $elapsed = now()->timestamp - $sentAt;

        if ($elapsed < self::RESEND_THROTTLE_SECONDS) {
            $wait = self::RESEND_THROTTLE_SECONDS - $elapsed;
            return back()->withErrors([
                'code' => "Please wait {$wait} second(s) before requesting a new code.",
            ]);
        }

        static::send($request, auth()->user());

        return back()->with('resent', 'A new code has been sent to your email address.');
    }

    // ── Static initiator — called from login controller ───────────────────────

    public static function send(Request $request, $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put('two_fa_code_hash', hash('sha256', $code));
        $request->session()->put('two_fa_expires_at', now()->addMinutes(self::CODE_EXPIRY_MINUTES)->timestamp);
        $request->session()->put('two_fa_attempts', 0);
        $request->session()->put('two_fa_sent_at', now()->timestamp);

        try {
            Mail::to($user->email)
                ->send(new TwoFactorCodeMail($code, self::CODE_EXPIRY_MINUTES));
        } catch (\Throwable $e) {
            Log::error('2FA email failed for user #' . $user->id . ': ' . $e->getMessage());
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function clearSession(\Illuminate\Session\Store $session): void
    {
        $session->forget([
            'two_fa_code_hash',
            'two_fa_expires_at',
            'two_fa_attempts',
            'two_fa_sent_at',
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, 2);
        $masked  = $visible . str_repeat('*', max(3, \strlen($local) - 2));
        return $masked . '@' . $domain;
    }
}
