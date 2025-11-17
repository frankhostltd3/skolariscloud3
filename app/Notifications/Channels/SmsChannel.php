<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = (string) $notification->toSms($notifiable);
        if ($message === '') {
            return;
        }

        $recipients = $notifiable->routeNotificationFor('sms', $notification);
        if (empty($recipients)) {
            return;
        }

        $recipients = is_array($recipients) ? $recipients : [$recipients];
        $recipients = array_filter(array_map('strval', $recipients));

        if (empty($recipients)) {
            return;
        }

        $config = config('skolaris.billing.sms', []);
        $endpoint = $config['http_endpoint'] ?? null;
        $token = $config['http_token'] ?? null;

        foreach ($recipients as $recipient) {
            if (! $endpoint) {
                Log::notice('SMS notification dispatched (no endpoint configured)', [
                    'recipient' => $recipient,
                    'message' => $message,
                ]);
                continue;
            }

            try {
                $response = Http::withHeaders(
                    $token ? ['Authorization' => 'Bearer ' . $token] : []
                )->asJson()->post($endpoint, [
                    'to' => $recipient,
                    'message' => $message,
                ]);

                if ($response->failed()) {
                    Log::warning('SMS notification request failed', [
                        'recipient' => $recipient,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('SMS notification request errored', [
                    'recipient' => $recipient,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
