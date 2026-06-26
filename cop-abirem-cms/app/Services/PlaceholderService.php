<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Channel-agnostic placeholder resolution engine.
 *
 * Knows nothing about SMS, email, or PDF — it only resolves {placeholders}
 * in template strings from three sources:
 *
 *   Type A (recipient)  — per-recipient fields from the Member model
 *   Type B (manual)     — values supplied by the sender at compose time
 *   Type C (system)     — church settings / app context (now(), auth user)
 *
 * All placeholder types and their resolution sources are defined in
 * config/placeholders.php.  Adding a new placeholder = one entry there.
 */
class PlaceholderService
{
    private const PATTERN = '/\{\s*([a-zA-Z0-9_]+)\s*\}/';

    private array $registry;

    public function __construct()
    {
        $this->registry = config('placeholders', []);
    }

    // ──────────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────────

    /**
     * Extract all placeholder keys found in a template.
     *
     * Returns: [ 'key' => registry_entry_or_unknown_entry, ... ]
     * Keys are normalised to lowercase. Unknown placeholders get type='unknown'.
     */
    public function extract(string $template): array
    {
        $found = [];
        preg_match_all(self::PATTERN, $template, $matches);
        foreach ($matches[1] as $rawKey) {
            $key = strtolower($rawKey);
            if (!isset($found[$key])) {
                $found[$key] = $this->registry[$key] ?? ['type' => 'unknown', 'label' => $key];
            }
        }
        return $found;
    }

    /**
     * Resolve all placeholders in $template and return the final string.
     *
     * @param  string      $template     The raw template (may contain {placeholders})
     * @param  Model|null  $recipient    The recipient model (Member or Visitor)
     * @param  array       $manualValues Sender-supplied values keyed by placeholder name
     * @return string                    The fully resolved text
     */
    public function resolve(string $template, ?Model $recipient = null, array $manualValues = []): string
    {
        // Resolve system values once (not per-recipient)
        $systemValues = $this->resolveSystemValues();

        return preg_replace_callback(self::PATTERN, function ($m) use ($recipient, $manualValues, $systemValues) {
            $key = strtolower($m[1]);
            $def = $this->registry[$key] ?? null;

            if (!$def) {
                return $m[0]; // unknown — leave as-is; validate() will flag it
            }

            return match ($def['type']) {
                'recipient' => $this->resolveRecipient($def, $recipient),
                'manual'    => trim($manualValues[$key] ?? ''),
                'system'    => $systemValues[$key] ?? '',
                default     => $m[0],
            };
        }, $template);
    }

    /**
     * Validate a template + manual values.
     *
     * Returns an array of human-readable warning strings. Empty = no problems.
     *
     * Categories:
     *   "Unknown placeholder: {x}"  — not in registry (typo); blocks send
     *   "Missing value for: Label"  — Type B field left blank; warns but allows
     */
    public function validate(string $template, array $manualValues = []): array
    {
        $warnings = [];
        $placeholders = $this->extract($template);

        foreach ($placeholders as $key => $def) {
            if ($def['type'] === 'unknown') {
                $warnings[] = ['level' => 'error', 'message' => "Unknown placeholder: {{$key}}"];
                continue;
            }
            if ($def['type'] === 'manual' && empty(trim($manualValues[$key] ?? ''))) {
                $label = $def['label'] ?? $key;
                $warnings[] = ['level' => 'warning', 'message' => "Missing value for: {$label}"];
            }
        }

        return $warnings;
    }

    /**
     * Resolve all Type C (system) placeholder values.
     * Called once per send batch — not per recipient.
     *
     * Returns: [ 'church_name' => 'COP Abirem', 'current_date' => '25 Jun, 2026', ... ]
     */
    public function resolveSystemValues(): array
    {
        $values = [];
        foreach ($this->registry as $key => $def) {
            if ($def['type'] !== 'system') {
                continue;
            }
            $values[$key] = $this->resolveSystemSource($def['source'] ?? '');
        }
        return $values;
    }

    /**
     * Return registry entries grouped by type, excluding aliases.
     * Useful for building the Insert Placeholder UI.
     *
     * Returns: [ 'recipient' => [...], 'manual' => [...], 'system' => [...] ]
     */
    public function uiRegistry(): array
    {
        $grouped = ['recipient' => [], 'manual' => [], 'system' => []];

        foreach ($this->registry as $key => $def) {
            if (!empty($def['alias'])) {
                continue; // hide aliases from UI
            }
            $type = $def['type'];
            if (isset($grouped[$type])) {
                $grouped[$type][$key] = ['label' => $def['label']];
            }
        }

        return $grouped;
    }

    public function getRegistry(): array
    {
        return $this->registry;
    }

    // ──────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────

    private function resolveRecipient(array $def, ?Model $recipient): string
    {
        if (!$recipient) {
            return '';
        }

        $source = $def['source'] ?? '';
        $value  = $recipient->{$source} ?? null;

        // Graceful fallback for name fields when the specific field is empty
        if (empty($value) && in_array($source, ['full_name', 'first_name', 'last_name'], true)) {
            $value = $recipient->full_name
                  ?? trim(($recipient->first_name ?? '') . ' ' . ($recipient->last_name ?? ''))
                  ?: 'Member';
        }

        // If member_id requested but not set (e.g. visitor), return empty string
        return (string) ($value ?? '');
    }

    private function resolveSystemSource(string $source): string
    {
        // Pull from church settings (editable in Settings → General/SMS)
        if (str_starts_with($source, 'setting:')) {
            return (string) Setting::get(substr($source, 8), '');
        }

        if ($source === 'auth_user_name') {
            return (string) (Auth::user()?->name ?? '');
        }

        $now    = Carbon::now();
        $dateFmt = Setting::get('date_format', 'd M, Y') ?: 'd M, Y';
        $timeFmt = Setting::get('time_format', 'h:i A') ?: 'h:i A';

        return match ($source) {
            'now:date' => $now->format($dateFmt),
            'now:year' => (string) $now->year,
            'now:time' => $now->format($timeFmt),
            default    => '',
        };
    }
}
