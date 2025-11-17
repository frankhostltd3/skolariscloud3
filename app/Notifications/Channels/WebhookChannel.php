<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWebhook')) {
            return;
        }

        $payload = $notification->toWebhook($notifiable);
        if (empty($payload)) {
            return;
        }

        $targets = $notifiable->routeNotificationFor('webhook', $notification);
        if (empty($targets)) {
            return;
        }

        $targets = is_array($targets) ? $targets : [$targets];
        $targets = array_filter(array_map('strval', $targets));

        if (empty($targets)) {
            return;
        }

        $signingSecret = config('skolaris.billing.webhook.signing_secret');

        foreach ($targets as $url) {
            try {
                $request = Http::asJson();

                if ($signingSecret) {
                    $signature = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES), $signingSecret);
                    $request = $request->withHeaders(['X-Skolaris-Signature' => $signature]);
                }

                $response = $request->post($url, $payload);

                if ($response->failed()) {
                    Log::warning('Webhook notification request failed', [
                        'url' => $url,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('Webhook notification request errored', [
                    'url' => $url,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
