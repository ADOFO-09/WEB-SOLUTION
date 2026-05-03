<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class GiantSmsService
{
    private const BASE_URL = 'https://api.giantsms.com/api/v1';

    private string $username;
    private string $password;
    private string $senderId;

    public function __construct()
    {
        $this->username = Setting::get('sms_api_key', '');
        $this->password = Setting::get('sms_api_secret', '');
        $this->senderId = Setting::get('sms_sender_id', '');
    }

    /**
     * Send a single SMS message.
     *
     * @return array{status:string, message:string, message_id?:string}
     * @throws \RuntimeException on API error or non-success status
     */
    public function send(string $to, string $message): array
    {
        $to = $this->normalizePhone($to);

        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(15)
            ->post(self::BASE_URL . '/send', [
                'from' => $this->senderId,
                'to'   => $to,
                'msg'  => $message,
            ]);

        $data = $response->json() ?? [];

        if (!$response->successful() || ($data['status'] ?? false) === false) {
            throw new \RuntimeException($data['message'] ?? "GiantSMS HTTP {$response->status()}");
        }

        return $data;
    }

    /**
     * Send the same message to multiple recipients in one API call.
     *
     * @param  string[] $recipients  Phone numbers
     * @return array
     * @throws \RuntimeException on API error
     */
    public function sendBulk(array $recipients, string $message): array
    {
        $recipients = array_map([$this, 'normalizePhone'], $recipients);

        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->post(self::BASE_URL . '/send', [
                'from'       => $this->senderId,
                'recipients' => $recipients,
                'msg'        => $message,
            ]);

        $data = $response->json() ?? [];

        if (!$response->successful() || ($data['status'] ?? false) === false) {
            throw new \RuntimeException($data['message'] ?? "GiantSMS HTTP {$response->status()}");
        }

        return $data;
    }

    /**
     * Fetch the account SMS balance.
     *
     * Returns ['balance' => float, 'raw' => array] so the caller can
     * surface the raw API payload if parsing fails.
     *
     * @throws \RuntimeException on HTTP or API error
     */
    public function getBalance(): array
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(10)
            ->get(self::BASE_URL . '/balance');

        $data = $response->json() ?? [];

        if (!$response->successful() || ($data['status'] ?? false) === false) {
            throw new \RuntimeException("GiantSMS balance check failed (HTTP {$response->status()})");
        }

        // GiantSMS returns the credit count in the "message" field.
        return [
            'balance' => (float) ($data['message'] ?? 0),
            'raw'     => $data,
        ];
    }

    /**
     * Validate that credentials are configured and non-empty.
     */
    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->password) && !empty($this->senderId);
    }

    /**
     * Normalise a Ghanaian phone number to international format (233XXXXXXXXX).
     * Accepts: 024XXXXXXX, +233XXXXXXXXX, 233XXXXXXXXX, 0XXXXXXXXX
     */
    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // strip non-digits

        if (str_starts_with($phone, '233') && \strlen($phone) === 12) {
            return $phone;
        }

        if (str_starts_with($phone, '0') && \strlen($phone) === 10) {
            return '233' . substr($phone, 1);
        }

        // Already looks like international without country code prefix
        if (\strlen($phone) === 9) {
            return '233' . $phone;
        }

        return $phone; // return as-is if format is unrecognised
    }
}
