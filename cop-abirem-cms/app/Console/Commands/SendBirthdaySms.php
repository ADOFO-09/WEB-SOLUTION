<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Setting;
use App\Models\SmsMessage;
use App\Models\SmsRecipient;
use App\Services\GiantSmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBirthdaySms extends Command
{
    protected $signature = 'sms:send-birthdays
                            {--dry-run : List recipients and messages without sending}
                            {--force   : Send even if birthday SMS is disabled in settings}';

    protected $description = 'Send birthday SMS greetings to members whose birthday is today (scheduled 06:00 daily)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force  = $this->option('force');

        // Guard: settings must have birthday SMS enabled (bypass with --force for manual runs)
        if (!$force && !$this->birthdaySmsEnabled()) {
            $this->info('Birthday SMS is disabled in settings (use --force to override).');
            Log::info('[BirthdaySMS] Skipped — birthday SMS disabled in settings.');
            return self::SUCCESS;
        }

        $template = Setting::get('sms_birthday_template', '');
        if (empty(trim($template))) {
            $this->error('Birthday SMS template is not configured. Go to Settings → SMS and add a template.');
            Log::warning('[BirthdaySMS] Aborted — no birthday template configured.');
            return self::FAILURE;
        }

        // Find active members with a birthday today and a phone number
        $members = Member::birthdayToday()
            ->where('membership_status', 'active')
            ->whereNotNull('phone_primary')
            ->where('phone_primary', '!=', '')
            ->get();

        $today = now()->format('d F');

        if ($members->isEmpty()) {
            $this->info("No active member birthdays today ({$today}).");
            Log::info("[BirthdaySMS] No birthdays today ({$today}).");
            return self::SUCCESS;
        }

        $this->info("Birthday members today ({$today}): {$members->count()}");

        // Dry-run: just preview
        if ($dryRun) {
            $rows = $members->map(fn($m) => [
                $m->full_name,
                $m->phone_primary,
                $this->personalise($template, $m),
            ])->toArray();
            $this->table(['Name', 'Phone', 'Message'], $rows);
            return self::SUCCESS;
        }

        // Verify SMS service is ready
        $smsService = new GiantSmsService();
        if (!$smsService->isConfigured()) {
            $this->error('SMS credentials are not configured. Go to Settings → SMS and save your credentials.');
            Log::error('[BirthdaySMS] Aborted — SMS service not configured.');
            return self::FAILURE;
        }

        // Create a parent SmsMessage record for this batch
        $smsMessage = SmsMessage::create([
            'message_type'    => 'automated',
            'category'        => 'birthday',
            'subject'         => 'Birthday Greetings — ' . now()->format('d M Y'),
            'message_content' => $template,
            'recipient_count' => $members->count(),
            'successful_count' => 0,
            'failed_count'    => 0,
            'status'          => 'sending',
            'sent_at'         => now(),
            'sent_by'         => auth()->id(), // null when run by scheduler, user ID when triggered manually
        ]);

        $successCount = 0;
        $failCount    = 0;

        foreach ($members as $member) {
            $phone   = $member->phone_primary;
            $message = $this->personalise($template, $member);

            $recipient = SmsRecipient::create([
                'sms_message_id' => $smsMessage->id,
                'member_id'      => $member->id,
                'phone_number'   => $phone,
                'recipient_name' => $member->full_name,
                'status'         => 'pending',
            ]);

            try {
                $result = $smsService->send($phone, $message);

                $recipient->update([
                    'status'             => 'sent',
                    'sent_at'            => now(),
                    'gateway_message_id' => $result['message_id'] ?? ($result['id'] ?? null),
                ]);

                $successCount++;
                $this->line("  <info>✓</info> {$member->full_name} ({$phone})");
                Log::info("[BirthdaySMS] Sent to {$member->full_name} ({$phone})");
            } catch (\Exception $e) {
                $recipient->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $failCount++;
                $this->line("  <error>✗</error> {$member->full_name} ({$phone}): {$e->getMessage()}");
                Log::error("[BirthdaySMS] Failed for {$member->full_name} ({$phone}): {$e->getMessage()}");
            }
        }

        $finalStatus = match(true) {
            $failCount    === 0 => 'sent',
            $successCount === 0 => 'failed',
            default             => 'partially_sent',
        };

        $smsMessage->update([
            'status'           => $finalStatus,
            'successful_count' => $successCount,
            'failed_count'     => $failCount,
        ]);

        $this->info("Complete — Sent: {$successCount}, Failed: {$failCount}");
        Log::info("[BirthdaySMS] Complete — Sent: {$successCount}, Failed: {$failCount}");

        return $failCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function birthdaySmsEnabled(): bool
    {
        return Setting::get('sms_birthday_enabled', '0') === '1'
            && Setting::get('enable_sms_notifications', '0') === '1';
    }

    private function personalise(string $template, Member $member): string
    {
        return str_replace(
            ['{name}', '{first_name}', '{last_name}'],
            [$member->full_name, $member->first_name, $member->last_name],
            $template
        );
    }
}
