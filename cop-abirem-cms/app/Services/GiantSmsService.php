<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(15)
            ->post(self::BASE_URL . '/send', [
                'from' => $this->senderId,
                'to'   => $to,
                'msg'  => $message,
            ]);

        $data = $response->json() ?? [];

        if (!$response->successful() || ($data['status'] ?? '') === 'error') {
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
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->post(self::BASE_URL . '/send', [
                'from'       => $this->senderId,
                'recipients' => $recipients,
                'msg'        => $message,
            ]);

        $data = $response->json() ?? [];

        if (!$response->successful() || ($data['status'] ?? '') === 'error') {
            throw new \RuntimeException($data['message'] ?? "GiantSMS HTTP {$response->status()}");
        }

        return $data;
    }

    /**
     * Fetch the account SMS balance.
     */
    public function getBalance(): float
    {
        try {
            $response = Http::timeout(10)
                ->get(self::BASE_URL . '/balance', [
                    'username' => $this->username,
                    'password' => $this->password,
                ]);

            $data = $response->json() ?? [];

            if ($response->successful() && ($data['status'] ?? '') !== 'error') {
                return (float) ($data['balance'] ?? 0);
            }
        } catch (\Exception $e) {
            Log::warning('GiantSMS balance check failed: ' . $e->getMessage());
        }

        return 0.0;
    }
}
