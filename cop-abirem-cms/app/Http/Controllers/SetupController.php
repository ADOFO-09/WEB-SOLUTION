<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SetupController extends Controller
{
    // Steps in order — each key is also a route name suffix and session key
    private const STEPS = ['account', 'church', 'financial', 'sms'];

    // ──────────────────────────────────────────────────────
    // Guard helpers
    // ──────────────────────────────────────────────────────

    private function guardAdmin(): ?RedirectResponse
    {
        if (!Auth::check() || !RoleHelper::isSystemAdmin(Auth::user())) {
            return redirect()->route('login')
                ->with('error', 'Setup is restricted to the system administrator.');
        }
        // If already installed, send to dashboard
        if (Setting::get('app_installed') === '1') {
            return redirect()->route('admin.dashboard');
        }
        return null;
    }

    private function requireStep(string $step): ?RedirectResponse
    {
        $done  = session('setup_done', []);
        $index = array_search($step, self::STEPS);

        // Redirect to the first incomplete step before this one
        for ($i = 0; $i < $index; $i++) {
            if (!in_array(self::STEPS[$i], $done)) {
                return redirect()->route('setup.' . self::STEPS[$i]);
            }
        }
        return null;
    }

    private function markDone(string $step): void
    {
        $done = session('setup_done', []);
        if (!in_array($step, $done)) {
            $done[] = $step;
        }
        session(['setup_done' => $done]);
    }

    private function saveSetting(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => (string) $value, 'group' => $group, 'type' => $type, 'updated_at' => now()]
        );
    }

    // ──────────────────────────────────────────────────────
    // Index — redirect to first incomplete step
    // ──────────────────────────────────────────────────────

    public function index(): RedirectResponse
    {
        if ($r = $this->guardAdmin()) return $r;

        $done = session('setup_done', []);
        foreach (self::STEPS as $step) {
            if (!in_array($step, $done)) {
                return redirect()->route('setup.' . $step);
            }
        }
        return redirect()->route('setup.complete');
    }

    // ──────────────────────────────────────────────────────
    // Step 1 — Administrator Account
    // ──────────────────────────────────────────────────────

    public function account()
    {
        if ($r = $this->guardAdmin()) return $r;
        return view('setup.account', ['step' => 1, 'done' => session('setup_done', [])]);
    }

    public function saveAccount(Request $request): RedirectResponse
    {
        if ($r = $this->guardAdmin()) return $r;

        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => ['required', 'email', 'max:150',
                                        \Illuminate\Validation\Rule::unique('users', 'email')
                                            ->ignore(Auth::id())],
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        Auth::user()->update([
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'password'             => Hash::make($validated['password']),
            'must_change_password' => false,
            'email_verified_at'    => now(),
        ]);

        $this->markDone('account');
        return redirect()->route('setup.church');
    }

    // ──────────────────────────────────────────────────────
    // Step 2 — Church Identity
    // ──────────────────────────────────────────────────────

    public function church()
    {
        if ($r = $this->guardAdmin()) return $r;
        if ($r = $this->requireStep('church')) return $r;

        $current = [
            'church_name'       => Setting::get('church_name', ''),
            'church_short_name' => Setting::get('church_short_name', ''),
            'church_slogan'     => Setting::get('church_slogan', ''),
            'church_address'    => Setting::get('church_address', ''),
            'church_phone'      => Setting::get('church_phone', ''),
            'church_email'      => Setting::get('church_email', ''),
        ];

        return view('setup.church', ['step' => 2, 'done' => session('setup_done', []), 'current' => $current]);
    }

    public function saveChurch(Request $request): RedirectResponse
    {
        if ($r = $this->guardAdmin()) return $r;
        if ($r = $this->requireStep('church')) return $r;

        $validated = $request->validate([
            'church_name'       => 'required|string|max:150',
            'church_short_name' => 'nullable|string|max:60',
            'church_slogan'     => 'nullable|string|max:255',
            'church_address'    => 'nullable|string|max:500',
            'church_phone'      => 'nullable|string|max:30',
            'church_email'      => 'nullable|email|max:150',
            'church_logo'       => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
        ]);

        foreach (['church_name', 'church_short_name', 'church_slogan', 'church_address', 'church_phone', 'church_email'] as $key) {
            $this->saveSetting($key, $validated[$key] ?? '', 'general');
        }

        if ($request->hasFile('church_logo')) {
            $path = $request->file('church_logo')->store('logos', 'public');
            $this->saveSetting('church_logo', $path, 'general', 'file');
        }

        $this->markDone('church');
        return redirect()->route('setup.financial');
    }

    // ──────────────────────────────────────────────────────
    // Step 3 — Financial Year
    // ──────────────────────────────────────────────────────

    public function financial()
    {
        if ($r = $this->guardAdmin()) return $r;
        if ($r = $this->requireStep('financial')) return $r;

        $year       = (int) date('Y');
        $existing   = DB::table('financial_years')
            ->where('is_active', true)
            ->first();

        return view('setup.financial', [
            'step'        => 3,
            'done'        => session('setup_done', []),
            'defaultYear' => $year,
            'existing'    => $existing,
        ]);
    }

    public function saveFinancial(Request $request): RedirectResponse
    {
        if ($r = $this->guardAdmin()) return $r;
        if ($r = $this->requireStep('financial')) return $r;

        $validated = $request->validate([
            'year_name'  => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        DB::table('financial_years')->where('is_active', true)->update(['is_active' => false]);

        DB::table('financial_years')->updateOrInsert(
            ['name' => $validated['year_name']],
            [
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date'],
                'is_active'  => true,
                'is_closed'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->markDone('financial');
        return redirect()->route('setup.sms');
    }

    // ──────────────────────────────────────────────────────
    // Step 4 — SMS Gateway (skippable)
    // ──────────────────────────────────────────────────────

    public function sms()
    {
        if ($r = $this->guardAdmin()) return $r;
        if ($r = $this->requireStep('sms')) return $r;

        return view('setup.sms', ['step' => 4, 'done' => session('setup_done', [])]);
    }

    public function saveSms(Request $request): RedirectResponse
    {
        if ($r = $this->guardAdmin()) return $r;
        if ($r = $this->requireStep('sms')) return $r;

        if ($request->boolean('skip_sms')) {
            $this->saveSetting('sms_enabled', '0', 'sms', 'boolean');
            $this->markDone('sms');
            return redirect()->route('setup.complete');
        }

        $validated = $request->validate([
            'sms_provider'  => 'required|in:giantsms',
            'sms_sender_id' => 'required|string|max:11',
            'sms_api_key'   => 'required|string|max:255',
        ]);

        $this->saveSetting('sms_provider',  $validated['sms_provider'],  'sms');
        $this->saveSetting('sms_sender_id', $validated['sms_sender_id'], 'sms');
        $this->saveSetting('sms_api_key',   $validated['sms_api_key'],   'sms');
        $this->saveSetting('sms_enabled',   '1',                         'sms', 'boolean');

        $this->markDone('sms');
        return redirect()->route('setup.complete');
    }

    // ──────────────────────────────────────────────────────
    // Step 5 — Complete
    // ──────────────────────────────────────────────────────

    public function complete()
    {
        if ($r = $this->guardAdmin()) return $r;

        // Ensure all non-optional steps are done
        $done = session('setup_done', []);
        foreach (['account', 'church', 'financial'] as $required) {
            if (!in_array($required, $done)) {
                return redirect()->route('setup.' . $required);
            }
        }

        // Mark the system as installed
        $this->saveSetting('app_installed', '1', 'system', 'boolean');

        session()->forget('setup_done');

        $churchName = Setting::get('church_name', 'Kerith');
        $smsEnabled = Setting::get('sms_enabled') === '1';

        return view('setup.complete', compact('churchName', 'smsEnabled'));
    }
}
