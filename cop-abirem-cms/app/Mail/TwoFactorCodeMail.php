<?php

namespace App\Mail;

use App\Helpers\SettingHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly int $expiryMinutes,
    ) {}

    public function envelope(): Envelope
    {
        $church = SettingHelper::churchShortName() ?: SettingHelper::churchName();

        return new Envelope(
            subject: "[{$church}] Your Login Verification Code",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-code',
        );
    }
}
