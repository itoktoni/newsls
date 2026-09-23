<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CentrifugoService
{
    public function publish(string $channel, array $data): void
    {
        if (! config('langkahkecil.notification_enable')) {
            return;
        }

        $url = config('centrifugo.url').'/api';

        Http::withHeaders([
            'Authorization' => 'apikey '.config('centrifugo.api_key'),
            'Content-Type' => 'application/json',
        ])->post($url, [
            'method' => 'publish',
            'params' => [
                'channel' => $channel,
                'data' => $data,
            ],
        ]);
    }

    public function generateConnectionToken(string $userId, ?int $expire = null): string
    {
        $expire = $expire ?? now()->addSeconds(config('centrifugo.token_expire', 3600))->timestamp;

        return $this->signToken([
            'sub' => $userId,
            'exp' => $expire,
            'info' => json_encode(['user_id' => $userId]),
        ]);
    }

    public function generateSubscriptionToken(string $userId, string $channel, ?int $expire = null): string
    {
        $expire = $expire ?? now()->addSeconds(config('centrifugo.token_expire', 3600))->timestamp;

        return $this->signToken([
            'sub' => $userId,
            'channel' => $channel,
            'exp' => $expire,
        ]);
    }

    private function signToken(array $claims): string
    {
        $header = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = $this->base64UrlEncode(json_encode($claims));
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", config('centrifugo.hmac_secret'), true)
        );

        return "$header.$payload.$signature";
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
