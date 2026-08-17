<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GiantSmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SmsPasswordResetController extends Controller
{
    private const OTP_EXPIRY_MINUTES      = 10;
    private const MAX_ATTEMPTS            = 5;
    private const RESEND_THROTTLE_SECONDS = 60;

    // ── Step 1: show email entry form ─────────────────────────────────────────

    public function create(): View
    {
        return view('auth.forgot-password-sms');
    }

    // ── Step 2: look up account, send OTP to registered phone ─────────────────

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Honeypot: bots fill in the hidden website field, humans don't
        if ($request->filled('website')) {
            return $this->otpSentResponse(null);
        }

        $user = User::where('email', $request->input('email'))->first();

        // Account not found — redirect the same way as success to prevent email enumeration
        if (!$user) {
            return $this->otpSentResponse(null);
        }

        // Account must be linked to a member profile with a registered phone
        if (!$user->member_id || !$user->member) {
            return back()->withInput()->withErrors([
                'email' => 'This account is not linked to a member profile. Please use email reset instead.',
            ]);
        }

        $phone = $user->member->phone_primary ?: null;

        if (!$phone) {
            return back()->withInput()->withErrors([
                'email' => 'No mobile number is on file for this account. Please use email reset or contact the administrator.',
            ]);
        }

        // Throttle: prevent re-sending within RESEND_THROTTLE_SECONDS
        $recentExists = DB::table('password_reset_otps')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subSeconds(self::RESEND_THROTTLE_SECONDS))
            ->whereNull('used_at')
            ->exists();

        if ($recentExists) {
            return back()->withErrors([
                'email' => 'Please wait 60 seconds before requesting another code.',
            ])->withInput();
        }

        // Invalidate any previous unused OTPs for this user
        DB::table('password_reset_otps')
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        // Generate a 6-digit OTP and store its hash
        $otp     = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = hash('sha256', $otp);

        DB::table('password_reset_otps')->insert([
            'user_id'    => $user->id,
            'phone'      => $phone,
            'otp_hash'   => $otpHash,
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'used_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send SMS — surface errors instead of swallowing them
        try {
            $sms = new GiantSmsService();

            if (!$sms->isConfigured()) {
                // Delete the OTP we just stored since we can't deliver it
                DB::table('password_reset_otps')->where('user_id', $user->id)->whereNull('used_at')->delete();

                return back()->withInput()->withErrors([
                    'email' => 'SMS service is not configured. Please use email reset or contact the administrator.',
                ]);
            }

            $churchName = \App\Helpers\SettingHelper::churchShortName();
            $message    = "Your {$churchName} password reset code is: {$otp}. "
                        . 'Valid for ' . self::OTP_EXPIRY_MINUTES . ' minutes. Do not share this code.';

            $sms->send($phone, $message);

        } catch (\Throwable $e) {
            Log::error('SMS OTP send failed for user #' . $user->id . ': ' . $e->getMessage());

            // Delete the unused OTP so the throttle does not block a retry
            DB::table('password_reset_otps')->where('user_id', $user->id)->whereNull('used_at')->delete();

            return back()->withInput()->withErrors([
                'email' => 'Failed to send the code. Please try again or use email reset.',
            ]);
        }

        return $this->otpSentResponse($phone);
    }

    // ── Step 3: show OTP + new password form ──────────────────────────────────

    public function showVerify(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('otp_phone')) {
            return redirect()->route('password.sms.request');
        }

        $phone = $request->session()->get('otp_phone');

        return view('auth.otp-verify', [
            'maskedPhone' => $phone ? $this->maskPhone($phone) : '****',
        ]);
    }

    // ── Step 4: verify OTP, reset password, log in ────────────────────────────

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp'      => ['required', 'digits:6'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);

        $phone = $request->session()->get('otp_phone');

        if (!$phone) {
            return redirect()->route('password.sms.request')
                ->withErrors(['otp' => 'Session expired. Please request a new code.']);
        }

        // Find the latest valid OTP for this phone
        $record = DB::table('password_reset_otps')
            ->where('phone', $phone)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->where('attempts', '<', self::MAX_ATTEMPTS)
            ->orderByDesc('created_at')
            ->first();

        if (!$record) {
            return back()->withErrors([
                'otp' => 'This code has expired or is no longer valid. Please request a new one.',
            ]);
        }

        // Increment attempt count before checking (prevents timing-based brute force)
        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->increment('attempts');

        if (hash('sha256', $request->input('otp')) !== $record->otp_hash) {
            $remaining = self::MAX_ATTEMPTS - $record->attempts - 1;
            $msg = $remaining > 0
                ? "Incorrect code. {$remaining} attempt(s) remaining."
                : 'Too many incorrect attempts. Please request a new code.';

            return back()->withErrors(['otp' => $msg]);
        }

        // OTP is correct — mark used and reset password
        DB::table('password_reset_otps')
            ->where('id', $record->id)
            ->update(['used_at' => now(), 'updated_at' => now()]);

        $user = User::find($record->user_id);
        $user->update(['password' => Hash::make($request->input('password'))]);

        // Invalidate all other sessions for this user
        DB::table('sessions')->where('user_id', $user->id)->delete();

        $request->session()->forget('otp_phone');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect(\App\Helpers\RoleHelper::getDashboardUrl($user))
            ->with('success', 'Password reset successfully. Welcome back!');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function otpSentResponse(?string $phone): RedirectResponse
    {
        session(['otp_phone' => $phone]);
        return redirect()->route('password.sms.verify');
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $len    = \strlen($digits);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }
        return str_repeat('*', $len - 4) . substr($digits, -4);
    }
}
