<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class GiantSmsService
{
    private const BASE_URL = 'https://api.giantsms.com/api/v1';

    private string $username;
    private string $password;
    private string $senderId;

    public function __construct()
    {
        $this->username = $this->decrypt(Setting::get('sms_api_key', ''));
        $this->password = $this->decrypt(Setting::get('sms_api_secret', ''));
        $this->senderId = Setting::get('sms_sender_id', '');
    }

    private function decrypt(string $value): string
    {
        if (empty($value)) {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $value; // plain-text fallback for values not yet encrypted
        }
    }

    /**
     * Send a single SMS message.
     *
     * @return array{status:string, message:string, message_id?:string}
     * @throws \RuntimeException on API error or non-success status
     */
    public function send(string $to, string $message): array
    {
        $to      = $this->normalizePhone($to);
        $message = $this->normalizeMessage($message);

        $payload = [
            'username' => $this->username,
            'password' => $this->password,
            'from'     => $this->senderId,
            'to'       => $to,
            'msg'      => $message,
        ];

        \Illuminate\Support\Facades\Log::debug('GiantSMS single send', [
            'to'           => $to,
            'message_len'  => \strlen($message),
            'sender_id'    => $this->senderId,
        ]);

        $response = Http::asForm()
            ->timeout(15)
            ->post(self::BASE_URL . '/send', $payload);

        $data = $response->json() ?? [];

        \Illuminate\Support\Facades\Log::debug('GiantSMS single response', [
            'status' => $response->status(),
            'body'   => $data,
        ]);

        if (!$response->successful() || (isset($data['status']) && $data['status'] === false)) {
            $err = $data['message'] ?? ('GiantSMS HTTP ' . $response->status() . ': ' . $response->body());
            throw new \RuntimeException($err);
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
        $message    = $this->normalizeMessage($message);

        $payload = [
            'username' => $this->username,
            'password' => $this->password,
            'from'     => $this->senderId,
            'to'       => implode(',', $recipients),
            'msg'      => $message,
        ];

        \Illuminate\Support\Facades\Log::debug('GiantSMS bulk send', [
            'recipients'  => \count($recipients),
            'message_len' => \strlen($message),
            'sender_id'   => $this->senderId,
        ]);

        $response = Http::asForm()
            ->timeout(30)
            ->post(self::BASE_URL . '/send', $payload);

        $data = $response->json() ?? [];

        \Illuminate\Support\Facades\Log::debug('GiantSMS bulk response', [
            'status' => $response->status(),
            'body'   => $data,
        ]);

        if (!$response->successful() || (isset($data['status']) && $data['status'] === false)) {
            $err = $data['message'] ?? ('GiantSMS HTTP ' . $response->status() . ': ' . $response->body());
            throw new \RuntimeException($err);
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
        $response = Http::timeout(10)
            ->get(self::BASE_URL . '/balance', [
                'username' => $this->username,
                'password' => $this->password,
            ]);

        $data = $response->json() ?? [];

        $authOk = $response->successful() && !(isset($data['status']) && $data['status'] === false);

        \Illuminate\Support\Facades\Log::log($authOk ? 'debug' : 'warning', 'GiantSMS balance response', [
            'http_status'    => $response->status(),
            'body'           => $data,
            'username_len'   => strlen($this->username),
            'username_prefix'=> substr($this->username, 0, 3) . '***',
            'sender_id'      => $this->senderId,
        ]);

        if (!$authOk) {
            $err = $data['message'] ?? "GiantSMS balance check failed (HTTP {$response->status()})";
            throw new \RuntimeException($err);
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

    private function normalizeMessage(string $message): string
    {
        $message = str_replace(["\r\n", "\r"], "\n", $message);
        return trim($message);
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
